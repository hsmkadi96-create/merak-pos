<?php

namespace App\Http\Controllers;

use App\Models\EmployeeFaceId;
use App\Models\FaceAttendanceLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class FaceIdController extends Controller
{
    /**
     * Dashboard pointage faciale.
     */
    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $employees = Employee::where('tenant_id', $tenantId)->get();
        $faceEnabled = EmployeeFaceId::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $totalEmployees = $employees->count();

        $recentLogs = FaceAttendanceLog::where('tenant_id', $tenantId)
            ->with('employee')
            ->orderBy('clocked_at', 'desc')
            ->limit(20)
            ->get();

        return view('faceid.index', compact('employees', 'faceEnabled', 'totalEmployees', 'recentLogs'));
    }

    /**
     * Enregistrer le visage d'un employé.
     */
    public function storeFace(Request $request, Employee $employee)
    {
        $request->validate([
            'face_template' => 'required|string',
            'face_image' => 'nullable|image|max:2048',
        ]);

        $tenantId = session('current_tenant_id');

        $imagePath = null;
        if ($request->hasFile('face_image')) {
            $imagePath = $request->file('face_image')->store('faces/' . $tenantId, 'public');
        }

        EmployeeFaceId::updateOrCreate(
            ['tenant_id' => $tenantId, 'employee_id' => $employee->id],
            [
                'face_template' => $request->face_template,
                'face_image_path' => $imagePath,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Visage enregistré pour ' . $employee->firstname . ' ' . $employee->lastname);
    }

    /**
     * Pointage par reconnaissance faciale (API).
     */
    public function clock(Request $request)
    {
        $request->validate([
            'face_template' => 'required|string',
            'type' => 'required|in:clock_in,clock_out',
            'device_info' => 'nullable|string',
            'ip_address' => 'nullable|string',
        ]);

        $tenantId = session('current_tenant_id');

        // Recherche d'employé par correspondance faciale (simulation - à remplacer par vraie reconnaissance)
        $faceRecord = EmployeeFaceId::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where('face_template', $request->face_template)
            ->first();

        if (!$faceRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Visage non reconnu. Veuillez réessayer.',
            ], 404);
        }

        $log = FaceAttendanceLog::create([
            'tenant_id' => $tenantId,
            'employee_id' => $faceRecord->employee_id,
            'clocked_at' => now(),
            'type' => $request->type,
            'method' => 'face_id',
            'device_info' => $request->device_info,
            'ip_address' => $request->ip_address,
            'confidence_score' => 98.5, // Score simulé
        ]);

        // Mise à jour du dernier pointage
        if ($request->type === 'clock_in') {
            $faceRecord->update(['last_clock_in' => now()]);
        } else {
            $faceRecord->update(['last_clock_out' => now()]);
        }

        return response()->json([
            'success' => true,
            'employee' => $faceRecord->employee->firstname . ' ' . $faceRecord->employee->lastname,
            'type' => $request->type,
            'time' => now()->format('H:i:s'),
            'confidence' => 98.5,
        ]);
    }

    /**
     * Liste des logs de pointage.
     */
    public function logs(Request $request)
    {
        $tenantId = session('current_tenant_id');

        $logs = FaceAttendanceLog::where('tenant_id', $tenantId)
            ->with('employee')
            ->orderBy('clocked_at', 'desc')
            ->paginate(20);

        return view('faceid.logs', compact('logs'));
    }
}
