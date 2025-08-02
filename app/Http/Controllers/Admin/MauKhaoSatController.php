<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MauKhaoSat;
use App\Models\DoiTuongKhaoSat;
use App\Models\NhomCauHoi;
use App\Models\CauHoiKhaoSat;
use App\Models\PhuongAnTraLoi;
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
        $query = MauKhaoSat::with(['doiTuong', 'nguoiTao', 'cauHoi', 'dotKhaoSat']);

        // Search
        if ($request->filled('search')) {
            $query->where('ten_mau', 'like', '%' . $request->search . '%');
        }

        // Filter by doi tuong
        if ($request->filled('ma_doituong')) {
            $query->where('ma_doituong', $request->ma_doituong);
        }

        $mauKhaoSats = $query->orderBy('created_at', 'desc')->paginate(10);
        $doiTuongs = DoiTuongKhaoSat::where('trangthai', 1)->get();

        return view('admin.mau-khao-sat.index', compact('mauKhaoSats', 'doiTuongs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doiTuongs = DoiTuongKhaoSat::where('trangthai', 1)->get();

        return view('admin.mau-khao-sat.create', compact('doiTuongs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|max:255',
            'ma_doituong' => 'required|exists:doituong_khaosat,ma_doituong',
            'mota' => 'nullable'
        ], [
            'ten_mau.required' => 'Vui lòng nhập tên mẫu khảo sát',
            'ma_doituong.required' => 'Vui lòng chọn đối tượng khảo sát',
            'ma_doituong.exists' => 'Đối tượng khảo sát không hợp lệ'
        ]);

        DB::beginTransaction();
        try {
            $mauKhaoSat = MauKhaoSat::create([
                'ten_mau' => $validated['ten_mau'],
                'ma_doituong' => $validated['ma_doituong'],
                'mota' => $validated['mota'],
                'nguoi_tao_id' => Auth::id(),
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
        $mauKhaoSat->load(['nhomCauHoi.cauHoi.phuongAnTraLoi', 'doiTuong']);
        $doiTuongs = DoiTuongKhaoSat::where('trangthai', 1)->get();

        return view('admin.mau-khao-sat.edit', compact('mauKhaoSat', 'doiTuongs'));
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

            // Xóa nhóm câu hỏi
            $mauKhaoSat->nhomCauHoi()->delete();

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
            $newMau->nguoi_tao_id = auth()->id();
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