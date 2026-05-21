<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $products = $query->latest()->paginate(10)->withQueryString();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'unit'        => 'nullable|string|max:30',
        ]);
        Product::create($request->only(['name', 'description', 'price', 'unit']));
        return redirect()->route('products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'unit'        => 'nullable|string|max:30',
        ]);
        $product->update($request->only(['name', 'description', 'price', 'unit']));
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $products = $query->latest()->get();
        $globalSym = \App\Models\Setting::get('default_currency', 'PEN') === 'USD' ? '$' : 'S/';

        $filename = 'productos_' . date('Y_m_d_H_i_s') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($products, $globalSym) {
            $fh = fopen('php://output', 'w');
            fputs($fh, "\xEF\xBB\xBF");
            fputcsv($fh, ['Nombre', 'Descripción', 'Unidad', 'Precio Unit.']);
            foreach ($products as $p) {
                fputcsv($fh, [
                    $p->name,
                    $p->description ?? '',
                    $p->unit ?? '',
                    number_format($p->price, 2),
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $query = Product::query();
        if ($request->search) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        $products  = $query->latest()->get();
        $globalSym = \App\Models\Setting::get('default_currency', 'PEN') === 'USD' ? '$' : 'S/';
        $pdf = Pdf::loadView('exports.products_pdf', compact('products', 'globalSym'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('productos_' . date('Y_m_d') . '.pdf');
    }
}
