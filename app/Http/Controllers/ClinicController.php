<?php

namespace App\Http\Controllers;

use App\Models\ClinicDoctor;
use App\Models\ClinicPatient;
use App\Models\ClinicAppointment;
use App\Models\ClinicMedicalRecord;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    /**
     * Dashboard clinique - médecins, patients, rendez-vous.
     */
    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $stats = [
            'doctors' => ClinicDoctor::where('tenant_id', $tenantId)->count(),
            'patients' => ClinicPatient::where('tenant_id', $tenantId)->count(),
            'appointments_today' => ClinicAppointment::where('tenant_id', $tenantId)
                ->whereDate('appointment_date', today())
                ->count(),
            'pending_appointments' => ClinicAppointment::where('tenant_id', $tenantId)
                ->where('status', 'scheduled')
                ->count(),
        ];

        $doctors = ClinicDoctor::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->get();

        $recentAppointments = ClinicAppointment::where('tenant_id', $tenantId)
            ->with('doctor', 'patient')
            ->orderBy('appointment_date', 'desc')
            ->limit(10)
            ->get();

        return view('clinic.index', compact('stats', 'doctors', 'recentAppointments'));
    }

    /**
     * Liste des patients.
     */
    public function patients(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $query = ClinicPatient::where('tenant_id', $tenantId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('clinic.patients', compact('patients'));
    }

    /**
     * Créer un patient.
     */
    public function storePatient(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:M,F,Other',
            'address' => 'nullable|string',
            'insurance_number' => 'nullable|string|max:100',
        ]);

        $tenantId = session('current_tenant_id');

        ClinicPatient::create(array_merge($request->only([
            'first_name', 'last_name', 'phone', 'email',
            'date_of_birth', 'gender', 'address', 'insurance_number',
        ]), ['tenant_id' => $tenantId]));

        return back()->with('success', 'Patient ajouté avec succès.');
    }

    /**
     * Liste des médecins.
     */
    public function doctors(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $doctors = ClinicDoctor::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        return view('clinic.doctors', compact('doctors'));
    }

    /**
     * Créer un médecin.
     */
    public function storeDoctor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'license_number' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $tenantId = session('current_tenant_id');

        ClinicDoctor::create(array_merge($request->only([
            'name', 'specialty', 'license_number', 'phone', 'email',
        ]), ['tenant_id' => $tenantId, 'is_active' => true]));

        return back()->with('success', 'Médecin ajouté avec succès.');
    }

    /**
     * Rendez-vous.
     */
    public function appointments(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $appointments = ClinicAppointment::where('tenant_id', $tenantId)
            ->with('doctor', 'patient')
            ->orderBy('appointment_date', 'desc')
            ->paginate(15);

        return view('clinic.appointments', compact('appointments'));
    }

    /**
     * Créer un rendez-vous.
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:clinic_doctors,id',
            'patient_id' => 'required|exists:clinic_patients,id',
            'appointment_date' => 'required|date|after:now',
            'duration' => 'nullable|integer|min:15|max:480',
            'type' => 'required|in:consultation,follow_up,emergency,procedure',
            'consultation_fee' => 'nullable|numeric|min:0',
            'symptoms' => 'nullable|string',
        ]);

        $tenantId = session('current_tenant_id');

        ClinicAppointment::create(array_merge($request->only([
            'doctor_id', 'patient_id', 'appointment_date', 'duration',
            'type', 'consultation_fee', 'symptoms',
        ]), [
            'tenant_id' => $tenantId,
            'status' => 'scheduled',
        ]));

        return back()->with('success', 'Rendez-vous fixé avec succès.');
    }

    /**
     * Dossier médical d'un patient.
     */
    public function medicalRecord(ClinicPatient $patient)
    {
        $records = ClinicMedicalRecord::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('record_date', 'desc')
            ->get();

        return view('clinic.medical-record', compact('patient', 'records'));
    }
}
