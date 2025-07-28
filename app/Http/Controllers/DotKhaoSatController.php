<?php

namespace App\Http\Controllers;

use App\Models\DotKhaoSat;
use App\Models\MauKhaoSat;
use App\Models\NamHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DotKhaoSatController extends Controller
{
    public function index(Request $request)
    {
        $query = DotKhaoSat::with(['mauKhaoSat.doiTuong', 'namHoc'])
            ->withCount([
                'phieuKhaoSat',
                'phieuKhaoSat as phieu_hoan_thanh' => function ($q) {
                    $q->where('trangthai', 'completed');
                }
            ]);

        // Filters
        if ($request->filled('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }

        if ($request->filled('namhoc_id')) {
            $query->where('namhoc_id', $request->namhoc_id);
        }

        if ($request->filled('search')) {
            $query->where('ten_dot', 'like', '%' . $request->search . '%');
        }

        $dotKhaoSats = $query->orderBy('created_at', 'desc')->paginate(10);
        $namHocs = NamHoc::orderBy('namhoc', 'desc')->get();

        return view('dot-khao-sat.index', compact('dotKhaoSats', 'namHocs'));
    }

    public function create()
    {
        $mauKhaoSats = MauKhaoSat::where('trangthai', 'active')->get();
        $namHocs = NamHoc::where('trangthai', 1)->orderBy('namhoc', 'desc')->get();

        return view('dot-khao-sat.create', compact('mauKhaoSats', 'namHocs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ten_dot' => 'required|max:255',
            'mau_khaosat_id' => 'required|exists:mau_khaosat,id',
            'namhoc_id' => 'required|exists:namhoc,id',
            'tungay' => 'required|date',
            'denngay' => 'required|date|after:tungay',
            'mota' => 'nullable'
        ]);

        DB::beginTransaction();
        try {
            $result = DB::select('CALL sp_TaoDotKhaoSat(?, ?, ?, ?, ?, ?)', [
                $validated['ten_dot'],
                $validated['mau_khaosat_id'],
                $validated['namhoc_id'],
                $validated['tungay'],
                $validated['denngay'],
                auth()->id()
            ]);

            DB::commit();

            return redirect()
                ->route('dot-khao-sat.show', $result[0]->dot_khaosat_id)
                ->with('success', 'Tạo đợt khảo sát thành công');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show(DotKhaoSat $dotKhaoSat)
    {
        $dotKhaoSat->load(['mauKhaoSat.doiTuong', 'namHoc']);

        // Thống kê
        $thongKe = [
            'tong_phieu' => $dotKhaoSat->phieuKhaoSat()->count(),
            'phieu_hoan_thanh' => $dotKhaoSat->phieuKhaoSat()->where('trangthai', 'completed')->count(),
            'ty_le' => $dotKhaoSat->getTyLeHoanThanh()
        ];

        // Thống kê theo đơn vị
        $thongKeTheoDonVi = DB::select('CALL sp_ThongKeTheoDonVi(?)', [$dotKhaoSat->id]);

        return view('dot-khao-sat.show', compact('dotKhaoSat', 'thongKe', 'thongKeTheoDonVi'));
    }

    public function activate(DotKhaoSat $dotKhaoSat)
    {
        // Loại bỏ authorize()
        if ($dotKhaoSat->trangthai !== 'draft') {
            return back()->with('error', 'Chỉ có thể kích hoạt đợt khảo sát ở trạng thái nháp');
        }

        $dotKhaoSat->update(['trangthai' => 'active']);

        return back()->with('success', 'Kích hoạt đợt khảo sát thành công');
    }

    public function close(DotKhaoSat $dotKhaoSat)
    {
        // Loại bỏ authorize()
        $dotKhaoSat->update(['trangthai' => 'closed']);

        return back()->with('success', 'Đóng đợt khảo sát thành công');
    }
}