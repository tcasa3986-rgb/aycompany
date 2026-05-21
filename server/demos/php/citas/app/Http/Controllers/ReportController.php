<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Specialty;
use App\Exports\AppointmentsExport;
use App\Exports\RevenueExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ─── Main Reports Dashboard ──────────────────────────────────────────────

    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // KPIs
        $totalAppointments = Appointment::count();
        $totalPatients = Patient::count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $cancellationRate = $totalAppointments > 0
            ? round(Appointment::where('status', 'cancelled')->count() / $totalAppointments * 100, 1)
            : 0;

        // Appointments per month (last 12 months)
        $months = collect(range(11, 0))->map(fn($m) => Carbon::now()->subMonths($m));

        $apptByMonth = Appointment::select(
            DB::raw('YEAR(date) as yr'),
            DB::raw('MONTH(date) as mo'),
            DB::raw('COUNT(*) as total')
        )
            ->where('date', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $monthLabels = $months->map(fn($m) => $m->format('M Y'))->values()->toArray();
        $monthValues = $months->map(fn($m) => (int) ($apptByMonth[$m->year . '-' . $m->month]->total ?? 0))->values()->toArray();

        // Revenue per month (last 12 months)
        $revenueByMonth = Invoice::select(
            DB::raw('YEAR(created_at) as yr'),
            DB::raw('MONTH(created_at) as mo'),
            DB::raw('SUM(amount) as total')
        )
            ->where('status', 'paid')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $revenueValues = $months->map(fn($m) => (float) ($revenueByMonth[$m->year . '-' . $m->month]->total ?? 0))->values()->toArray();

        // Appointments by status (donut)
        $byStatus = Appointment::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusMap = ['pending' => 'Pendiente', 'confirmed' => 'Confirmada', 'in_progress' => 'En Atención', 'completed' => 'Completada', 'cancelled' => 'Cancelada', 'no_show' => 'No Asistió'];
        $donutLabels = $byStatus->keys()->map(fn($k) => $statusMap[$k] ?? $k)->values()->toArray();
        $donutValues = $byStatus->values()->toArray();

        // Top 5 doctors
        $topDoctors = Doctor::with('user')
            ->withCount('appointments')
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'totalAppointments',
            'totalPatients',
            'totalRevenue',
            'cancellationRate',
            'monthLabels',
            'monthValues',
            'revenueValues',
            'donutLabels',
            'donutValues',
            'topDoctors'
        ));
    }

    // ─── Appointments Report ─────────────────────────────────────────────────

    public function appointments(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'specialty']);

        $this->applyAppointmentFilters($query, $request);

        $appointments = $query->orderByDesc('date')->paginate(20)->withQueryString();
        $doctors = Doctor::with('user')->get();
        $specialties = Specialty::all();

        return view('reports.appointments', compact('appointments', 'doctors', 'specialties'));
    }

    public function appointmentsPdf(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor', 'specialty']);
        $this->applyAppointmentFilters($query, $request);
        $appointments = $query->orderByDesc('date')->get();

        $pdf = Pdf::loadView('reports.appointments-pdf', compact('appointments', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-citas-' . now()->format('Y-m-d') . '.pdf');
    }

    public function appointmentsExcel(Request $request)
    {
        return Excel::download(
            new AppointmentsExport($request->all()),
            'reporte-citas-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ─── Revenue Report ──────────────────────────────────────────────────────

    public function revenue(Request $request)
    {
        $query = Invoice::with(['appointment.patient', 'appointment.doctor', 'appointment.specialty']);

        $this->applyRevenueFilters($query, $request);

        $invoices = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $totals = $this->revenueTotals(clone $query);

        return view('reports.revenue', compact('invoices', 'totals'));
    }

    public function revenuePdf(Request $request)
    {
        $query = Invoice::with(['appointment.patient', 'appointment.doctor']);
        $this->applyRevenueFilters($query, $request);
        $invoices = $query->orderByDesc('created_at')->get();
        $totals = $this->revenueTotals(clone $query);

        $pdf = Pdf::loadView('reports.revenue-pdf', compact('invoices', 'totals', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-ingresos-' . now()->format('Y-m-d') . '.pdf');
    }

    public function revenueExcel(Request $request)
    {
        return Excel::download(
            new RevenueExport($request->all()),
            'reporte-ingresos-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    // ─── Patients Report ─────────────────────────────────────────────────────

    public function patients(Request $request)
    {
        $totalPatients = Patient::count();
        $newThisMonth = Patient::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $newLastMonth = Patient::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ])->count();

        // Gender distribution
        $byGender = Patient::select('gender', DB::raw('COUNT(*) as total'))
            ->groupBy('gender')
            ->pluck('total', 'gender');

        $genderLabels = ['male' => 'Masculino', 'female' => 'Femenino', 'other' => 'Otro'];
        $genderData = collect(['male', 'female', 'other'])->map(fn($g) => (int) ($byGender[$g] ?? 0))->toArray();

        // New patients per month (last 12)
        $months = collect(range(11, 0))->map(fn($m) => Carbon::now()->subMonths($m));
        $newByMonth = Patient::select(
            DB::raw('YEAR(created_at) as yr'),
            DB::raw('MONTH(created_at) as mo'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('yr', 'mo')
            ->get()
            ->keyBy(fn($r) => $r->yr . '-' . $r->mo);

        $monthLabels = $months->map(fn($m) => $m->format('M Y'))->values()->toArray();
        $monthValues = $months->map(fn($m) => (int) ($newByMonth[$m->year . '-' . $m->month]->total ?? 0))->values()->toArray();

        // Blood type distribution
        $byBloodType = Patient::select('blood_type', DB::raw('COUNT(*) as total'))
            ->whereNotNull('blood_type')
            ->groupBy('blood_type')
            ->pluck('total', 'blood_type');

        return view('reports.patients', compact(
            'totalPatients',
            'newThisMonth',
            'newLastMonth',
            'genderData',
            'genderLabels',
            'monthLabels',
            'monthValues',
            'byBloodType'
        ));
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────

    private function applyAppointmentFilters($query, Request $request): void
    {
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }

    private function applyRevenueFilters($query, Request $request): void
    {
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
    }

    private function revenueTotals($query): array
    {
        $all = Invoice::when(true, fn($q) => $q);//placeholder to get fresh
        return [
            'total' => Invoice::sum('amount'),
            'paid' => Invoice::where('status', 'paid')->sum('amount'),
            'pending' => Invoice::where('status', 'pending')->sum('amount'),
            'cancelled' => Invoice::where('status', 'cancelled')->sum('amount'),
            'count' => Invoice::count(),
        ];
    }
}
