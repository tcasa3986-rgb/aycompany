<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define permissions per module ─────────────────────────────────────
        $permissions = [
            // Patients
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.delete',
            // Doctors
            'doctors.view',
            'doctors.create',
            'doctors.edit',
            'doctors.delete',
            // Schedules
            'schedules.view',
            'schedules.manage',
            // Appointments
            'appointments.view',
            'appointments.create',
            'appointments.cancel',
            'appointments.reschedule',
            // Medical Records
            'medical-records.view',
            'medical-records.create',
            'medical-records.edit',
            'medical-records.delete',
            // Invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            // Reports
            'reports.view',
            'reports.export',
            // Settings
            'settings.view',
            'settings.edit',
            // Users (admin only)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.assign-roles',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // ── Create roles and assign permissions ───────────────────────────────

        // ADMIN — all permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // DOCTOR
        $doctor = Role::firstOrCreate(['name' => 'doctor']);
        $doctor->syncPermissions([
            'appointments.view',
            'appointments.cancel',
            'medical-records.view',
            'medical-records.create',
            'medical-records.edit',
            'schedules.view',
            'schedules.manage',
            'patients.view',
        ]);

        // RECEPTIONIST
        $receptionist = Role::firstOrCreate(['name' => 'receptionist']);
        $receptionist->syncPermissions([
            'patients.view',
            'patients.create',
            'patients.edit',
            'appointments.view',
            'appointments.create',
            'appointments.cancel',
            'appointments.reschedule',
            'invoices.view',
            'invoices.create',
            'doctors.view',
            'schedules.view',
        ]);

        // PATIENT — minimal (own data handled in code via gates)
        $patient = Role::firstOrCreate(['name' => 'patient']);
        $patient->syncPermissions([
            'appointments.view',
            'medical-records.view',
        ]);

        $this->command->info('✅ Roles and permissions seeded successfully.');
    }
}
