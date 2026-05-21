<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('document_number', 'like', '%' . $request->search . '%');
        }
        $companies = $query->latest()->paginate(10)->withQueryString();
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:500',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:30',
        ]);
        Company::create($request->all());
        return redirect()->route('companies.index')->with('success', 'Empresa creada correctamente.');
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'document_number' => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:500',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:30',
        ]);
        $company->update($request->all());
        return redirect()->route('companies.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Empresa eliminada correctamente.');
    }

    public function exportExcel(Request $request)
    {
        $query = Company::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('document_number', 'like', '%' . $request->search . '%');
        }
        $companies = $query->latest()->get();

        $filename = 'empresas_' . date('Y_m_d_H_i_s') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($companies) {
            $fh = fopen('php://output', 'w');
            fputs($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['Nombre / Razón Social', 'RUC / NIT', 'Email', 'Teléfono', 'Dirección']);
            foreach ($companies as $c) {
                fputcsv($fh, [
                    $c->name,
                    $c->document_number ?? '',
                    $c->email ?? '',
                    $c->phone ?? '',
                    $c->address ?? '',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Company::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('document_number', 'like', '%' . $request->search . '%');
        }
        $companies = $query->latest()->get();
        $pdf = Pdf::loadView('exports.companies_pdf', compact('companies'))
                  ->setPaper('a4', 'landscape');
        return $pdf->download('empresas_' . date('Y_m_d') . '.pdf');
    }
}
