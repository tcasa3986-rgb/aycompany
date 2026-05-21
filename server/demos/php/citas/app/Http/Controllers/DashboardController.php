<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect logic for patients accessing the main dashboard
        $user = auth()->user();
        if ($user && $user->hasRole('patient') && !$user->hasRole(['admin', 'doctor', 'receptionist'])) {
            return redirect()->route('portal.dashboard');
        }

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Base query for appointments to filter by doctor if applicable
        $baseAppointmentQuery = function () {
            $query = Appointment::query();
            if (auth()->user()->hasRole('doctor') && !auth()->user()->hasRole('admin')) {
                $query->where('doctor_id', auth()->user()->doctor->id ?? 0);
            }
            return $query;
        };

        // ── KPI Cards ──────────────────────────────────────────────────────────
        $todayAppointments = $baseAppointmentQuery()->whereDate('date', $today)->count();
        $weekAppointments = $baseAppointmentQuery()->whereBetween('date', [$startOfWeek, Carbon::now()->endOfWeek()])->count();

        $totalPatients = Patient::count();
        if (auth()->user()->hasRole('doctor') && !auth()->user()->hasRole('admin')) {
            $totalPatients = \App\Models\Appointment::where('doctor_id', auth()->user()->doctor->id ?? 0)->distinct('patient_id')->count('patient_id');
        }

        $totalDoctors = Doctor::count();

        $pendingAppointments = $baseAppointmentQuery()->where('status', 'pending')->count();
        $confirmedAppointments = $baseAppointmentQuery()->where('status', 'confirmed')->count();
        $completedThisMonth = $baseAppointmentQuery()->where('status', 'completed')
            ->where('date', '>=', $startOfMonth)->count();
        $cancelledThisMonth = $baseAppointmentQuery()->where('status', 'cancelled')
            ->where('date', '>=', $startOfMonth)->count();

        // ── Upcoming Appointments (next 10) ─────────────────────────────────
        $upcomingAppointments = $baseAppointmentQuery()->with(['patient.user', 'doctor.user', 'specialty'])
            ->where('date', '>=', Carbon::now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('date')
            ->take(8)
            ->get();

        // ── Chart: Appointments per day (next 7 days including today) ────────────
        $next7Days = collect(range(0, 6))->map(fn($d) => Carbon::today()->addDays($d));

        $apptByDay = clone $baseAppointmentQuery();
        $apptByDay = $apptByDay->select(
            DB::raw('DATE(date) as day'),
            DB::raw('COUNT(*) as total')
        )
            ->whereBetween('date', [Carbon::today()->startOfDay(), Carbon::today()->addDays(6)->endOfDay()])
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartDays = $next7Days->map(fn($d) => $d->format('d M'))->values()->toArray();
        $chartValues = $next7Days->map(fn($d) => (int) ($apptByDay[$d->toDateString()] ?? 0))->values()->toArray();

        // ── Chart: Appointments by Status (donut) ────────────────────────────
        $byStatusQuery = clone $baseAppointmentQuery();
        $byStatus = $byStatusQuery->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = ['pending' => 'Pendiente', 'confirmed' => 'Confirmada', 'in_progress' => 'En Atención', 'completed' => 'Completada', 'cancelled' => 'Cancelada', 'no_show' => 'No Asistió'];
        $donutLabels = $byStatus->keys()->map(fn($k) => $statusLabels[$k] ?? $k)->values()->toArray();
        $donutValues = $byStatus->values()->toArray();

        // ── Top Doctors by appointments this month ───────────────────────────
        $topDoctors = Doctor::with('user')
            ->withCount([
                'appointments as month_count' => function ($q) use ($startOfMonth) {
                    $q->where('date', '>=', $startOfMonth);
                }
            ])
            ->orderByDesc('month_count')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'todayAppointments',
            'weekAppointments',
            'totalPatients',
            'totalDoctors',
            'pendingAppointments',
            'confirmedAppointments',
            'completedThisMonth',
            'cancelledThisMonth',
            'upcomingAppointments',
            'chartDays',
            'chartValues',
            'donutLabels',
            'donutValues',
            'topDoctors'
        ));
    }
}
