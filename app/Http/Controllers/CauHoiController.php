<?php

namespace App\Http\Controllers;

use App\Models\CauHoiKhaoSat;
use App\Models\MauKhaoSat;
use App\Models\NhomCauHoi;
use App\Models\PhuongAnTraLoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CauHoiController extends Controller
{
    public function store(Request $request, MauKhaoSat $mauKhaoSat)
    {
        $validated = $request->validate([
            'nhom_cauhoi_id' => 'nullable|exists:nhom_cauhoi,id',
            'noidung_cauhoi' => 'required',
            'loai_cauhoi' => 'required|in:single_choice,multiple_choice,text,likert,rating,date,number',
            'batbuoc' => 'boolean',
            'thutu' => 'integer',
            'phuong_an' => 'required_if:loai_cauhoi,single_choice,multiple_choice,likert|array',
            'phuong_an.*' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            // Tạo câu hỏi
            $cauHoi = $mauKhaoSat->cauHoi()->create([
                'nhom_cauhoi_id' => $validated['nhom_cauhoi_id'],
                'noidung_cauhoi' => $validated['noidung_cauhoi'],
                'loai_cauhoi' => $validated['loai_cauhoi'],
                'batbuoc' => $validated['batbuoc'] ?? true,
                'thutu' => $validated['thutu'] ?? 0
            ]);

            // Tạo phương án trả lời
            if (isset($validated['phuong_an'])) {
                foreach ($validated['phuong_an'] as $index => $phuongAn) {
                    $cauHoi->phuongAnTraLoi()->create([
                        'noidung' => $phuongAn,
                        'giatri' => $index + 1,
                        'thutu' => $index
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thêm câu hỏi thành công',
                'data' => $cauHoi->load('phuongAnTraLoi')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, CauHoiKhaoSat $cauHoi)
    {
        $validated = $request->validate([
            'noidung_cauhoi' => 'required',
            'batbuoc' => 'boolean',
            'thutu' => 'integer'
        ]);

        $cauHoi->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật câu hỏi thành công'
        ]);
    }

    public function destroy(CauHoiKhaoSat $cauHoi)
    {
        $cauHoi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa câu hỏi thành công'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:cauhoi_khaosat,id',
            'items.*.thutu' => 'required|integer'
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $item) {
                CauHoiKhaoSat::where('id', $item['id'])
                    ->update(['thutu' => $item['thutu']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thứ tự thành công'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }
}