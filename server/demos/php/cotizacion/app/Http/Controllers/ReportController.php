<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year  = $request->get('year', date('Y'));

        // Detect DB driver for year extraction
        $driver = \DB::connection()->getDriverName();

        // Get available years
        $yearExpr = $driver === 'sqlite'
            ? "STRFTIME('%Y', issue_date)"
            : "YEAR(issue_date)";

        $years = Quotation::selectRaw("{$yearExpr} as yr")
                    ->groupBy('yr')->orderByRaw('yr DESC')->pluck('yr')->toArray();
        if (empty($years)) $years = [date('Y')];

        $defCurrency = \App\Models\Setting::get('default_currency', 'PEN');

        // Monthly totals
        $monthly = [];
        $monthLabels = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthLabels[] = date('M', mktime(0,0,0,$m,1));
            $monthly[]     = (float) Quotation::whereYear('issue_date', $year)
                                ->where('currency', $defCurrency)
                                ->whereMonth('issue_date', $m)->sum('total');
        }

        // By status (we sum total only for default currency, but qty can be overall? No, let's sum total by currency)
        $byStatus = Quotation::whereYear('issue_date', $year)
                        ->where('currency', $defCurrency)
                        ->selectRaw('status, count(*) as qty, sum(total) as amount')
                        ->groupBy('status')->get();

        // By currency
        $byCurrency = Quotation::whereYear('issue_date', $year)
                        ->selectRaw('currency, count(*) as qty, sum(total) as amount')
                        ->groupBy('currency')->get();

        // Top clients by amount (default currency)
        $topClients = Quotation::whereYear('issue_date', $year)
                        ->where('currency', $defCurrency)
                        ->whereIn('status',['Aprobada'])
                        ->with('client')
                        ->selectRaw('client_id, sum(total) as total_amount, count(*) as qty')
                        ->groupBy('client_id')
                        ->orderByDesc('total_amount')
                        ->take(5)->get();

        // KPIs
        $totalQuotations  = Quotation::whereYear('issue_date', $year)->count();
        $totalApproved    = Quotation::whereYear('issue_date', $year)->where('status','Aprobada')->count();
        $totalRevenue     = Quotation::whereYear('issue_date', $year)->where('currency', $defCurrency)->where('status','Aprobada')->sum('total');
        $avgTicket        = $totalApproved > 0 ? $totalRevenue / $totalApproved : 0;
        $conversionRate   = $totalQuotations > 0 ? round($totalApproved / $totalQuotations * 100, 1) : 0;

        return view('reports.index', compact(
            'year','years','monthly','monthLabels',
            'byStatus','byCurrency','topClients',
            'totalQuotations','totalApproved','totalRevenue','avgTicket','conversionRate'
        ));
    }

    public function exportExcel(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $defCurrency = \App\Models\Setting::get('default_currency', 'PEN');
        
        $quotations = Quotation::whereYear('issue_date', $year)
                        ->where('currency', $defCurrency)
                        ->with('client')
                        ->get();

        $filename = "reporte_ventas_{$year}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($quotations, $year) {
            $fh = fopen('php://output', 'w');
            fputs($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ["Reporte de Ventas - Año {$year}"]);
            fputcsv($fh, []);
            fputcsv($fh, ['Número', 'Cliente', 'Fecha Emisión', 'Total', 'Estado']);
            foreach ($quotations as $q) {
                fputcsv($fh, [
                    $q->quotation_number,
                    $q->client->name ?? '',
                    $q->issue_date->format('d/m/Y'),
                    number_format($q->total, 2, '.', ''),
                    $q->status,
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $defCurrency = \App\Models\Setting::get('default_currency', 'PEN');
        
        // Data for PDF
        $quotations = Quotation::whereYear('issue_date', $year)
                        ->where('currency', $defCurrency)
                        ->with('client')
                        ->get();
        
        $totalRevenue = $quotations->where('status', 'Aprobada')->sum('total');
        $totalApproved = $quotations->where('status', 'Aprobada')->count();
        $totalQuotations = $quotations->count();
        $globalSym = $defCurrency === 'USD' ? '$' : 'S/';

        $pdf = Pdf::loadView('exports.reports_pdf', compact('quotations', 'year', 'totalRevenue', 'totalApproved', 'totalQuotations', 'globalSym'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download("reporte_ventas_{$year}.pdf");
    }
}
