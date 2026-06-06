<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthSetting;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MonthSettingController extends Controller
{
    protected array $monthMap = [
        '1'  => 'জানুয়ারি',  '2'  => 'ফেব্রুয়ারি', '3'  => 'মার্চ',
        '4'  => 'এপ্রিল',    '5'  => 'মে',           '6'  => 'জুন',
        '7'  => 'জুলাই',     '8'  => 'আগস্ট',        '9'  => 'সেপ্টেম্বর',
        '10' => 'অক্টোবর',   '11' => 'নভেম্বর',      '12' => 'ডিসেম্বর',
    ];


    public function index()
    {
        $monthSettings = MonthSetting::with('studentClass')
            ->orderBy('academic_year', 'desc')
            ->paginate(15);

        $classes     = Classes::orderBy('name')->get();
        $currentYear = date('Y');
        $years       = range($currentYear - 2, $currentYear + 5);

        return view('admin.month-settings.index', compact(
            'monthSettings', 'classes', 'years', 'currentYear'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data = $this->resolveAllMonths($data);

        $exists = MonthSetting::where('academic_year', $data['academic_year'])
            ->where('class_id', $data['class_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'এই শিক্ষাবর্ষ ও ক্লাসের জন্য ইতোমধ্যে মাস সেটিং আছে। এডিট করুন।',
            ], 409);
        }

        $monthSetting = MonthSetting::create($data);
        $monthSetting->load('studentClass');

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে সংরক্ষিত হয়েছে!',
            'data'    => $this->formatData($monthSetting),
        ]);
    }

    // ✅ Fix: সঠিকভাবে JSON response দিচ্ছে
    public function edit(MonthSetting $monthSetting): JsonResponse
    {
        $monthSetting->load('studentClass');

        return response()->json([
            'success' => true,
            'data'    => $this->formatData($monthSetting),
        ]);
    }

    public function update(Request $request, MonthSetting $monthSetting): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $exists = MonthSetting::where('academic_year', $request->academic_year)
            ->where('class_id', $request->class_id)
            ->where('id', '!=', $monthSetting->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'এই শিক্ষাবর্ষ ও ক্লাসের জন্য ইতোমধ্যে মাস সেটিং আছে।',
            ], 409);
        }

        $data = $validator->validated();
        $data = $this->resolveAllMonths($data);

        $monthSetting->update($data);
        $monthSetting->fresh()->load('studentClass');

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে আপডেট হয়েছে!',
            'data'    => $this->formatData($monthSetting->fresh()->load('studentClass')),
        ]);
    }

    public function destroy(MonthSetting $monthSetting): JsonResponse
    {
        $monthSetting->delete();

        return response()->json([
            'success' => true,
            'message' => 'মাসের তালিকা সফলভাবে মুছে ফেলা হয়েছে!',
        ]);
    }

    public function suggest(Request $request): JsonResponse
    {
        $query       = trim($request->get('q', ''));
        $suggestions = [];

        if (is_numeric($query)) {
            if (isset($this->monthMap[$query])) {
                $suggestions[] = [
                    'value' => $this->monthMap[$query],
                    'label' => "{$query} → {$this->monthMap[$query]}",
                    'type'  => 'bangla',
                ];
            }
            if (isset($this->englishMonthMap[$query])) {
                $suggestions[] = [
                    'value' => $this->englishMonthMap[$query],
                    'label' => "{$query} → {$this->englishMonthMap[$query]}",
                    'type'  => 'english',
                ];
            }
        } else {
            foreach ($this->monthMap as $name) {
                if (mb_strpos($name, $query) !== false) {
                    $suggestions[] = ['value' => $name, 'label' => $name, 'type' => 'bangla'];
                }
            }
            foreach ($this->englishMonthMap as $name) {
                if (stripos($name, $query) !== false) {
                    $suggestions[] = ['value' => $name, 'label' => $name, 'type' => 'english'];
                }
            }
        }

        return response()->json($suggestions);
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function rules(): array
    {
        $rules = [
            'academic_year' => 'required|digits:4',
            'class_id'      => 'required|exists:classes,id',
        ];
        for ($i = 1; $i <= 12; $i++) {
            $rules["month_$i"] = 'nullable|string|max:50';
        }
        return $rules;
    }

    private function resolveAllMonths(array $data): array
    {
        for ($i = 1; $i <= 12; $i++) {
            $key = "month_$i";
            if (!empty($data[$key])) {
                $data[$key] = $this->resolveMonthName($data[$key]);
            }
        }
        return $data;
    }

    // ✅ Fix: student_class key সহ সব দরকারি data দিচ্ছে
    private function formatData(MonthSetting $ms): array
    {
        $data = $ms->toArray();
        $data['student_class'] = $ms->studentClass ? ['name' => $ms->studentClass->name] : null;
        return $data;
    }

    protected function resolveMonthName(string $value): string
    {
        $value = trim($value);
        if (is_numeric($value) && isset($this->monthMap[$value])) {
            return $this->monthMap[$value];
        }
        return $value;
    }
}