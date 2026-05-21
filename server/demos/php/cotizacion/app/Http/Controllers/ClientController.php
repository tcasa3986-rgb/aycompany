<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('quotations');
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('document_number', 'like', '%'.$request->search.'%');
        }
        $clients = $query->latest()->paginate(15)->withQueryString();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'notes'           => 'nullable|string|max:1000',
        ]);
        Client::create($request->only(['name','document_number','email','phone','address','notes']));
        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client)
    {
        $client->load('quotations');

        // KPIs
        $totalQuotations  = $client->quotations->count();
        $totalApproved    = $client->quotations->where('status','Aprobada')->count();
        $totalRevenue     = $client->quotations->where('status','Aprobada')->sum('total');
        $conversionRate   = $totalQuotations > 0 ? round($totalApproved / $totalQuotations * 100, 1) : 0;
        $lastQuotation    = $client->quotations->sortByDesc('issue_date')->first();

        // Grouped by status
        $byStatus = $client->quotations->groupBy('status')->map->count();

        // Quotations paginated — we'll pass all sorted
        $quotations = $client->quotations->sortByDesc('issue_date');

        return view('clients.show', compact(
            'client','totalQuotations','totalApproved',
            'totalRevenue','conversionRate','lastQuotation',
            'byStatus','quotations'
        ));
    }

    public function edit(Client $client)
    {
        $client->load(['quotations' => fn($q) => $q->latest()->take(10)]);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'notes'           => 'nullable|string|max:1000',
        ]);
        $client->update($request->only(['name','document_number','email','phone','address','notes']));
        return redirect()->route('clients.show', $client)->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        $query = Client::withCount('quotations');
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('document_number', 'like', '%'.$request->search.'%');
        }
        $clients = $query->latest()->get();

        $filename = 'clientes_' . date('Y_m_d_H_i_s') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($clients) {
            $fh = fopen('php://output', 'w');
            // BOM para Excel
            fputs($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['Nombre / Razón Social', 'RUC / DNI', 'Email', 'Teléfono', 'Dirección', 'Cotizaciones']);
            foreach ($clients as $c) {
                fputcsv($fh, [
                    $c->name,
                    $c->document_number ?? '',
                    $c->email ?? '',
                    $c->phone ?? '',
                    $c->address ?? '',
                    $c->quotations_count ?? 0,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Client::withCount('quotations');
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('document_number', 'like', '%'.$request->search.'%');
        }
        $clients  = $query->latest()->get();
        $pdf = Pdf::loadView('exports.clients_pdf', compact('clients'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('clientes_' . date('Y_m_d') . '.pdf');
    }
}
