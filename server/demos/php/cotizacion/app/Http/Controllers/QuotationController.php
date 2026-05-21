<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationDetail;
use App\Models\Setting;
use App\Mail\QuotationMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    // ────────────────────────────────── INDEX ──
    public function index(Request $request)
    {
        $query = Quotation::with('client')->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->currency) {
            $query->where('currency', $request->currency);
        }

        $quotations = $query->paginate(10)->withQueryString();
        $statuses   = ['Borrador', 'Emitida', 'Aprobada', 'Rechazada'];
        return view('quotations.index', compact('quotations', 'statuses'));
    }

    // ────────────────────────────────── EXPORT EXCEL ──
    public function exportExcel(Request $request)
    {
        $query = Quotation::with('client')->latest();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('quotation_number','like',"%{$s}%")
                  ->orWhereHas('client', fn($c) => $c->where('name','like',"%{$s}%"));
            });
        }
        if ($request->status)   $query->where('status',   $request->status);
        if ($request->currency) $query->where('currency', $request->currency);

        $quotations = $query->get();
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="cotizaciones-'.date('Y-m-d').'.csv"',
        ];

        $callback = function () use ($quotations) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($f, ['Número','Cliente','RUC Cliente','Fecha Emisión','Vencimiento','Moneda','Subtotal','Descuento','IGV','Total','Estado']);
            foreach ($quotations as $q) {
                fputcsv($f, [
                    $q->quotation_number,
                    $q->client->name ?? '',
                    $q->client->document_number ?? '',
                    $q->issue_date->format('d/m/Y'),
                    $q->due_date ? $q->due_date->format('d/m/Y') : '',
                    $q->currency,
                    number_format($q->subtotal, 2, '.', ''),
                    number_format($q->discount_amount, 2, '.', ''),
                    number_format($q->tax_amount, 2, '.', ''),
                    number_format($q->total, 2, '.', ''),
                    $q->status,
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ────────────────────────────────── EXPORT PDF ──
    public function exportPdf(Request $request)
    {
        $query = Quotation::with('client')->latest();
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('quotation_number','like',"%{$s}%")
                  ->orWhereHas('client', fn($c) => $c->where('name','like',"%{$s}%"));
            });
        }
        if ($request->status)   $query->where('status',   $request->status);
        if ($request->currency) $query->where('currency', $request->currency);

        $quotations = $query->get();
        $pdf = Pdf::loadView('exports.quotations_pdf', compact('quotations'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('cotizaciones_' . date('Y_m_d') . '.pdf');
    }

    // ────────────────────────────────── CREATE ──
    public function create()
    {
        $clients              = Client::orderBy('name')->get();
        $products             = Product::orderBy('name')->get();
        $number               = Quotation::generateNumber();
        $defaultCurrency      = Setting::get('default_currency', 'PEN');
        $defaultTaxRate       = Setting::get('default_tax_rate', 18);
        $defaultTerms         = Setting::get('terms_and_conditions', '');
        $defaultValidityDays  = Setting::get('default_validity_days', 30);
        return view('quotations.create', compact(
            'clients', 'products', 'number',
            'defaultCurrency', 'defaultTaxRate', 'defaultTerms', 'defaultValidityDays'
        ));
    }

    // ────────────────────────────────── STORE ──
    public function store(Request $request)
    {
        $request->validate([
            'quotation_number'       => 'required|string|unique:quotations,quotation_number',
            'issue_date'             => 'required|date',
            'due_date'               => 'nullable|date|after_or_equal:issue_date',
            'client_id'              => 'required|exists:clients,id',
            'currency'               => 'required|in:PEN,USD,EUR',
            'notes'                  => 'nullable|string',
            'terms'                  => 'nullable|string',
            'details'                => 'required|array|min:1',
            'details.*.product_name' => 'required|string',
            'details.*.quantity'     => 'required|numeric|min:0.001',
            'details.*.unit_price'   => 'required|numeric|min:0',
            'details.*.discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request) {
            ['subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'total' => $total]
                = $this->calcTotals($request);

            $quotation = Quotation::create([
                'quotation_number' => $request->quotation_number,
                'issue_date'       => $request->issue_date,
                'due_date'         => $request->due_date,
                'client_id'        => $request->client_id,
                'currency'         => $request->currency,
                'subtotal'         => $subtotal,
                'discount_amount'  => $discount,
                'tax_amount'       => $tax,
                'total'            => $total,
                'status'           => 'Borrador',
                'notes'            => $request->notes,
                'terms'            => $request->terms,
            ]);

            $this->saveDetails($quotation, $request->details);
        });

        return redirect()->route('quotations.index')
                         ->with('success', 'Cotización creada correctamente.');
    }

    // ────────────────────────────────── SHOW ──
    public function show(Quotation $quotation)
    {
        $quotation->load('client', 'details.product');
        $company = array_merge(Setting::defaults(), Setting::all_keyed());
        return view('quotations.show', compact('quotation', 'company'));
    }

    // ────────────────────────────────── EDIT ──
    public function edit(Quotation $quotation)
    {
        $quotation->load('details');
        $clients         = Client::orderBy('name')->get();
        $products        = Product::orderBy('name')->get();
        $defaultCurrency = Setting::get('default_currency', 'PEN');
        $defaultTaxRate  = Setting::get('default_tax_rate', 18);
        $defaultTerms    = Setting::get('terms_and_conditions', '');
        return view('quotations.edit', compact(
            'quotation', 'clients', 'products',
            'defaultCurrency', 'defaultTaxRate', 'defaultTerms'
        ));
    }

    // ────────────────────────────────── UPDATE ──
    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'issue_date'             => 'required|date',
            'due_date'               => 'nullable|date|after_or_equal:issue_date',
            'client_id'              => 'required|exists:clients,id',
            'currency'               => 'required|in:PEN,USD,EUR',
            'notes'                  => 'nullable|string',
            'terms'                  => 'nullable|string',
            'details'                => 'required|array|min:1',
            'details.*.product_name' => 'required|string',
            'details.*.quantity'     => 'required|numeric|min:0.001',
            'details.*.unit_price'   => 'required|numeric|min:0',
            'details.*.discount_pct' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            ['subtotal' => $subtotal, 'discount' => $discount, 'tax' => $tax, 'total' => $total]
                = $this->calcTotals($request);

            $quotation->update([
                'issue_date'      => $request->issue_date,
                'due_date'        => $request->due_date,
                'client_id'       => $request->client_id,
                'currency'        => $request->currency,
                'subtotal'        => $subtotal,
                'discount_amount' => $discount,
                'tax_amount'      => $tax,
                'total'           => $total,
                'notes'           => $request->notes,
                'terms'           => $request->terms,
            ]);

            $quotation->details()->delete();
            $this->saveDetails($quotation, $request->details);
        });

        return redirect()->route('quotations.show', $quotation)
                         ->with('success', 'Cotización actualizada correctamente.');
    }

    // ────────────────────────────────── DESTROY ──
    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')
                         ->with('success', 'Cotización eliminada correctamente.');
    }

    // ────────────────────────────────── STATUS ──
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate(['status' => 'required|in:Borrador,Emitida,Aprobada,Rechazada']);
        $quotation->update(['status' => $request->status]);
        return back()->with('success', 'Estado actualizado a "' . $request->status . '".');
    }

    // ────────────────────────────────── CLONE ──
    public function clone(Quotation $quotation)
    {
        $quotation->load('details');

        DB::transaction(function () use ($quotation) {
            $new = $quotation->replicate();
            $new->quotation_number = Quotation::generateNumber();
            $new->issue_date       = now()->toDateString();
            $new->due_date         = null;
            $new->status           = 'Borrador';
            $new->save();

            foreach ($quotation->details as $d) {
                $new->details()->create($d->only([
                    'product_id', 'product_name', 'unit',
                    'quantity', 'unit_price', 'discount_pct', 'subtotal',
                ]));
            }
        });

        $newQ = Quotation::where('quotation_number', '!=', $quotation->quotation_number)
                         ->latest()->first();

        return redirect()->route('quotations.edit', $newQ)
                         ->with('success', 'Cotización duplicada como ' . $newQ->quotation_number . '.');
    }

    // ────────────────────────────────── PDF ──
    public function pdf(Quotation $quotation)
    {
        $quotation->load('client', 'details.product');
        $company = array_merge(Setting::defaults(), Setting::all_keyed());
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('cotizacion-' . $quotation->quotation_number . '.pdf');
    }

    // ────────────────────────────────── PREVIEW ──
    public function preview(Quotation $quotation)
    {
        $quotation->load('client', 'details.product');
        $company = array_merge(Setting::defaults(), Setting::all_keyed());
        $pdf = Pdf::loadView('quotations.pdf', compact('quotation', 'company'))
                  ->setPaper('a4', 'portrait');
        return $pdf->stream('cotizacion-' . $quotation->quotation_number . '.pdf');
    }

    // ────────────────────────────────── EMAIL ──
    public function sendEmail(Request $request, Quotation $quotation)
    {
        $request->validate([
            'to_email'       => 'required|email',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        $quotation->load('client', 'details.product');

        try {
            Mail::to($request->to_email)
                ->send(new QuotationMail($quotation, $request->custom_message ?? ''));

            return back()->with('success', "Cotización enviada correctamente a {$request->to_email}.");
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo enviar el correo. Verifique la configuración SMTP. ('.$e->getMessage().')');
        }
    }

    // ────────────────────────────────── HELPERS ──
    private function calcTotals(Request $request): array
    {
        $subtotal = 0;
        foreach ($request->details as $d) {
            $lineBase     = $d['quantity'] * $d['unit_price'];
            $discountPct  = isset($d['discount_pct']) ? (float) $d['discount_pct'] : 0;
            $lineSubtotal = $lineBase * (1 - $discountPct / 100);
            $subtotal    += $lineSubtotal;
        }

        $globalDiscount = round($subtotal * ($request->global_discount_pct ?? 0) / 100, 2);
        $base           = $subtotal - $globalDiscount;
        $tax            = round($base * ($request->tax_rate ?? 0) / 100, 2);
        $total          = round($base + $tax, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => $globalDiscount,
            'tax'      => $tax,
            'total'    => $total,
        ];
    }

    private function saveDetails(Quotation $quotation, array $details): void
    {
        foreach ($details as $d) {
            $discountPct  = isset($d['discount_pct']) ? (float) $d['discount_pct'] : 0;
            $lineBase     = $d['quantity'] * $d['unit_price'];
            $lineSubtotal = round($lineBase * (1 - $discountPct / 100), 2);

            $quotation->details()->create([
                'product_id'   => $d['product_id'] ?? null,
                'product_name' => $d['product_name'],
                'unit'         => $d['unit'] ?? null,
                'quantity'     => $d['quantity'],
                'unit_price'   => $d['unit_price'],
                'discount_pct' => $discountPct,
                'subtotal'     => $lineSubtotal,
            ]);
        }
    }
}
