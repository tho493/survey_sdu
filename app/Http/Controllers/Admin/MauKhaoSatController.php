<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MauKhaoSat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MauKhaoSatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MauKhaoSat::with(['nguoiTao', 'cauHoi', 'dotKhaoSat']);

        // Search
        if ($request->filled('search')) {
            $query->where('ten_mau', 'like', '%' . $request->search . '%');
        }

        // Filter by user
        if ($request->filled('nguoi_tao')) {
            $query->where('nguoi_tao_id', $request->nguoi_tao);
        }

        // Filter by status
        if ($request->filled('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }
        // Filter by date
        if ($request->filled('ngay_tao')) {
            $query->where('created_at', $request->ngay_tao);
        }

        $mauKhaoSats = $query->orderBy('created_at', 'desc')->paginate(10);

        $dsNguoiTao = User::pluck('hoten', 'id');

        return view('admin.mau-khao-sat.index', compact('mauKhaoSats', 'dsNguoiTao'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mau-khao-sat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|max:255',
            'mota' => 'nullable'
        ], [
            'ten_mau.required' => 'Vui lòng nhập tên mẫu khảo sát',
        ]);

        DB::beginTransaction();
        try {
            $mauKhaoSat = MauKhaoSat::create([
                'ten_mau' => $validated['ten_mau'],
                'mota' => $validated['mota'],
                'nguoi_tao_id' => Auth::user()->id,
                'trangthai' => 'draft'
            ]);

            DB::commit();

            return redirect()
                ->route('admin.mau-khao-sat.edit', $mauKhaoSat)
                ->with('success', 'Tạo mẫu khảo sát thành công. Hãy thêm câu hỏi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MauKhaoSat $mauKhaoSat)
    {
        $mauKhaoSat->load(['cauHoi.phuongAnTraLoi']);

        return view('admin.mau-khao-sat.edit', compact('mauKhaoSat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MauKhaoSat $mauKhaoSat)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|max:255',
            'mota' => 'nullable',
            'trangthai' => 'in:draft,active,inactive'
        ], [
            'ten_mau.required' => 'Vui lòng nhập tên mẫu khảo sát',
            'trangthai.in' => 'Trạng thái không hợp lệ'
        ]);

        try {
            $mauKhaoSat->update($validated);

            return back()->with('success', 'Cập nhật mẫu khảo sát thành công');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MauKhaoSat $mauKhaoSat)
    {
        // Kiểm tra có đợt khảo sát nào đang sử dụng không
        if ($mauKhaoSat->dotKhaoSat()->count() > 0) {
            return back()->with('error', 'Không thể xóa mẫu khảo sát đã được sử dụng trong đợt khảo sát');
        }

        DB::beginTransaction();
        try {
            // Xóa câu hỏi và phương án trả lời
            foreach ($mauKhaoSat->cauHoi as $cauHoi) {
                $cauHoi->phuongAnTraLoi()->delete();
            }
            $mauKhaoSat->cauHoi()->delete();

            // Xóa mẫu khảo sát
            $mauKhaoSat->delete();

            DB::commit();

            return redirect()->route('admin.mau-khao-sat.index')
                ->with('success', 'Xóa mẫu khảo sát thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Copy mẫu khảo sát
     */
    public function copy(MauKhaoSat $mauKhaoSat)
    {
        DB::beginTransaction();
        try {
            // Tạo mẫu mới
            $newMau = $mauKhaoSat->replicate();
            $newMau->ten_mau = $mauKhaoSat->ten_mau . ' (Sao chép)';
            $newMau->trangthai = 'draft';
            $newMau->nguoi_tao_id = Auth::user()->id;
            $newMau->created_at = now();
            $newMau->save();

            // Sao chép nhóm câu hỏi
            foreach ($mauKhaoSat->nhomCauHoi as $nhom) {
                $newNhom = $nhom->replicate();
                $newNhom->mau_khaosat_id = $newMau->id;
                $newNhom->save();

                // Sao chép câu hỏi trong nhóm
                foreach ($nhom->cauHoi as $cauHoi) {
                    $newCauHoi = $cauHoi->replicate();
                    $newCauHoi->mau_khaosat_id = $newMau->id;
                    $newCauHoi->nhom_cauhoi_id = $newNhom->id;
                    $newCauHoi->save();

                    // Sao chép phương án trả lời
                    foreach ($cauHoi->phuongAnTraLoi as $phuongAn) {
                        $newPhuongAn = $phuongAn->replicate();
                        $newPhuongAn->cauhoi_id = $newCauHoi->id;
                        $newPhuongAn->save();
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.mau-khao-sat.edit', $newMau)
                ->with('success', 'Sao chép mẫu khảo sát thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}