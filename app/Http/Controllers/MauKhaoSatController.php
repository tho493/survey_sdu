<?php

namespace App\Http\Controllers;

use App\Models\MauKhaoSat;
use App\Models\DoiTuongKhaoSat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MauKhaoSatController extends Controller
{
    public function index()
    {
        $mauKhaoSats = MauKhaoSat::with(['doiTuong', 'nguoiTao'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mau-khao-sat.index', compact('mauKhaoSats'));
    }

    public function create()
    {
        $doiTuongs = DoiTuongKhaoSat::where('trangthai', 1)->get();

        return view('mau-khao-sat.create', compact('doiTuongs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|max:255',
            'ma_doituong' => 'required|exists:doituong_khaosat,ma_doituong',
            'mota' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $mauKhaoSat = MauKhaoSat::create([
                'ten_mau' => $validated['ten_mau'],
                'ma_doituong' => $validated['ma_doituong'],
                'mota' => $validated['mota'],
                'nguoi_tao_id' => auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('mau-khao-sat.edit', $mauKhaoSat)
                ->with('success', 'Tạo mẫu khảo sát thành công. Hãy thêm câu hỏi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function edit(MauKhaoSat $mauKhaoSat)
    {
        // Loại bỏ authorize()
        $mauKhaoSat->load(['nhomCauHoi.cauHoi.phuongAnTraLoi']);
        $doiTuongs = DoiTuongKhaoSat::where('trangthai', 1)->get();

        return view('mau-khao-sat.edit', compact('mauKhaoSat', 'doiTuongs'));
    }

    public function update(Request $request, MauKhaoSat $mauKhaoSat)
    {
        $validated = $request->validate([
            'ten_mau' => 'required|max:255',
            'mota' => 'nullable'
        ]);

        $mauKhaoSat->update($validated);

        return back()->with('success', 'Cập nhật mẫu khảo sát thành công');
    }

    public function destroy(MauKhaoSat $mauKhaoSat)
    {
        // Kiểm tra có đợt khảo sát nào đang sử dụng không
        if ($mauKhaoSat->dotKhaoSat()->count() > 0) {
            return back()->with('error', 'Không thể xóa mẫu khảo sát đã được sử dụng');
        }

        $mauKhaoSat->delete();

        return redirect()->route('mau-khao-sat.index')
            ->with('success', 'Xóa mẫu khảo sát thành công');
    }

    public function copy(MauKhaoSat $mauKhaoSat)
    {
        DB::beginTransaction();
        try {
            // Call stored procedure
            $newId = DB::select('CALL sp_SaoChepMauKhaoSat(?, ?, ?)', [
                $mauKhaoSat->id,
                $mauKhaoSat->ten_mau . ' (Sao chép)',
                auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('mau-khao-sat.edit', $newId[0]->mau_khaosat_id)
                ->with('success', 'Sao chép mẫu khảo sát thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}