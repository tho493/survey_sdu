<?php

namespace App\Http\Controllers;

use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use App\Models\PhieuKhaoSatChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhaoSatController extends Controller
{
    public function index()
    {
        $dotKhaoSats = DotKhaoSat::with(['mauKhaoSat.doiTuong'])
            ->where('trangthai', 'active')
            ->whereDate('tungay', '<=', now())
            ->whereDate('denngay', '>=', now())
            ->get();

        return view('khao-sat.index', compact('dotKhaoSats'));
    }

    public function show(DotKhaoSat $dotKhaoSat)
    {
        if (!$dotKhaoSat->isActive()) {
            return redirect()->route('khao-sat.index')
                ->with('error', 'Đợt khảo sát không hoạt động');
        }

        $mauKhaoSat = $dotKhaoSat->mauKhaoSat->load([
            'nhomCauHoi.cauHoi' => function ($query) {
                $query->where('trangthai', 1)->orderBy('thutu');
            },
            'nhomCauHoi.cauHoi.phuongAnTraLoi' => function ($query) {
                $query->orderBy('thutu');
            }
        ]);

        return view('khao-sat.show', compact('dotKhaoSat', 'mauKhaoSat'));
    }

    public function store(Request $request, DotKhaoSat $dotKhaoSat)
    {
        if (!$dotKhaoSat->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Đợt khảo sát không hoạt động'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Tạo phiếu khảo sát
            $phieuKhaoSat = PhieuKhaoSat::create([
                'dot_khaosat_id' => $dotKhaoSat->id,
                'ma_nguoi_traloi' => $request->ma_nguoi_traloi,
                'metadata' => $request->metadata,
                'trangthai' => 'draft',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Lưu câu trả lời
            foreach ($request->cau_tra_loi as $cauHoiId => $traLoi) {
                $data = [
                    'phieu_khaosat_id' => $phieuKhaoSat->id,
                    'cauhoi_id' => $cauHoiId
                ];

                if (is_array($traLoi)) {
                    // Multiple choice
                    $data['giatri_json'] = json_encode($traLoi);
                } elseif (is_numeric($traLoi)) {
                    // Single choice hoặc rating
                    $data['phuongan_id'] = $traLoi;
                } else {
                    // Text, date
                    $data['giatri_text'] = $traLoi;
                }

                PhieuKhaoSatChiTiet::create($data);
            }

            // Cập nhật trạng thái hoàn thành
            $phieuKhaoSat->update([
                'trangthai' => 'completed',
                'thoigian_hoanthanh' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi khảo sát thành công',
                'redirect' => route('khao-sat.thank-you')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function thankYou()
    {
        return view('khao-sat.thank-you');
    }
}