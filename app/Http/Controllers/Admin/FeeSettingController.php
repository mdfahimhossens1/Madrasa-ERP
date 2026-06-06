<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\FeeSetting;
use App\Models\SubLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeeSettingController extends Controller
{
    /**
     * =========================
     * INDEX
     * =========================
     */
    public function index()
    {
        $madrasaId = auth()->user()->madrasa_id;

        $academicYears = AcademicYear::where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();

        $classes = Classes::where('status', 'active')
            ->orderBy('name')
            ->get();

        $subLedgers = SubLedger::where('madrasa_id', $madrasaId)
            ->orderBy('name')
            ->get();

        $feeSettings = FeeSetting::with(['academicYear', 'class', 'subLedger'])
            ->where('madrasa_id', $madrasaId)
            ->latest()
            ->get();

        return view('admin.fee-settings.index', compact(
            'academicYears',
            'classes',
            'subLedgers',
            'feeSettings'
        ));
    }

    /**
     * =========================
     * GET SINGLE FEE DATA (AJAX)
     * =========================
     */
    public function get(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'nullable|exists:classes,id',
            'sub_ledger_id'    => 'required|exists:sub_ledgers,id',
        ]);

        try {
            $madrasaId = auth()->user()->madrasa_id;

            $query = FeeSetting::where('madrasa_id', $madrasaId)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('sub_ledger_id', $request->sub_ledger_id);

            // =========================
            // CLASS PRIORITY FIX
            // =========================
            if ($request->class_id) {

                $query->where(function ($q) use ($request) {
                    $q->where('class_id', $request->class_id)
                      ->orWhereNull('class_id');
                });

            } else {
                $query->whereNull('class_id');
            }

            $data = $query->orderByRaw('class_id IS NULL')->first();

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);

        } catch (\Exception $e) {

            Log::error('FeeSetting GET error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * =========================
     * SAVE / UPDATE
     * =========================
     */
    public function save(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'nullable|exists:classes,id',
            'sub_ledger_id'    => 'required|exists:sub_ledgers,id',
        ]);

        try {

            $madrasaId = auth()->user()->madrasa_id;

            $data = $request->only([
                'chattra_abashik_new',
                'chattra_abashik_old',
                'chattra_onabashik_new',
                'chattra_onabashik_old',
                'chattra_dekeyr_new',
                'chattra_dekeyr_old',
                'chattra_nightcare_new',
                'chattra_nightcare_old',
                'chhatri_abashik_new',
                'chhatri_abashik_old',
                'chhatri_onabashik_new',
                'chhatri_onabashik_old',
                'chhatri_dekeyr_new',
                'chhatri_dekeyr_old',
                'chhatri_nightcare_new',
                'chhatri_nightcare_old'
            ]);

            $data['madrasa_id'] = $madrasaId;
            $data['academic_year_id'] = $request->academic_year_id;
            $data['class_id'] = $request->class_id ?: null;
            $data['sub_ledger_id'] = $request->sub_ledger_id;

            // =========================
            // SAFE UPSERT
            // =========================
            $record = FeeSetting::updateOrCreate(
                [
                    'madrasa_id'       => $madrasaId,
                    'academic_year_id' => $data['academic_year_id'],
                    'class_id'         => $data['class_id'],
                    'sub_ledger_id'    => $data['sub_ledger_id'],
                ],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'ফি সেটিংস সফলভাবে সেভ হয়েছে!',
                'data'    => $record
            ]);

        } catch (\Exception $e) {

            Log::error('FeeSetting SAVE error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Save failed'
            ], 500);
        }
    }

    /**
     * =========================
     * RESET
     * =========================
     */
    public function reset(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        try {

            $madrasaId = auth()->user()->madrasa_id;

            FeeSetting::where('madrasa_id', $madrasaId)
                ->where('academic_year_id', $request->academic_year_id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'রিসেট সম্পন্ন হয়েছে!'
            ]);

        } catch (\Exception $e) {

            Log::error('FeeSetting RESET error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Reset failed'
            ], 500);
        }
    }
}