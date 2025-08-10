<?php

namespace App\Http\Controllers;

use App\Models\DotKhaoSat;
use App\Models\PhieuKhaoSat;
use App\Models\PhieuKhaoSatChiTiet;
use Illuminate\Http\Request;
use App\Models\CauHoiKhaoSat;
use Illuminate\Support\Facades\DB;

class KhaoSatController extends Controller
{
    public function index()
    {
        $dotKhaoSats = DotKhaoSat::with(['mauKhaoSat'])
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
                ->with('error', 'Đợt khảo sát này hiện không hoạt động hoặc đã kết thúc.');
        }

        $mauKhaoSat = $dotKhaoSat->mauKhaoSat()
            ->with([
                'cauHoi' => function ($query) {
                    $query->where('trangthai', 1)->orderBy('thutu', 'asc');
                },
                'cauHoi.phuongAnTraLoi' => function ($query) {
                    $query->orderBy('thutu', 'asc');
                }
            ])
            ->first();

        // dd($mauKhaoSat->toArray());

        if (!$mauKhaoSat) {
            return redirect()->route('khao-sat.index')
                ->with('error', 'Không tìm thấy mẫu khảo sát cho đợt này.');
        }

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

        // Validate reCAPTCHA
        $request->validate([
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha]
        ], [
            'g-recaptcha-response.required' => 'Vui lòng xác thực reCAPTCHA.'
        ]);

        DB::beginTransaction();
        try {
            // Tạo phiếu khảo sát
            $phieuKhaoSat = PhieuKhaoSat::create([
                'dot_khaosat_id' => $dotKhaoSat->id,
                'ma_nguoi_traloi' => $request->ma_nguoi_traloi,
                'metadata' => collect($request->metadata)->except('thoigian_batdau'),
                'thoigian_batdau' => $request->metadata['thoigian_batdau'] ?? null,
                'trangthai' => 'draft',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Lưu câu trả lời
            foreach ($request->input('cau_tra_loi', []) as $cauHoiId => $traLoi) {
                // Bỏ qua nếu giá trị rỗng
                if (is_null($traLoi) || (is_string($traLoi) && trim($traLoi) === '') || (is_array($traLoi) && empty($traLoi))) {
                    continue;
                }

                $cauHoi = CauHoiKhaoSat::find($cauHoiId);
                if (!$cauHoi)
                    continue;

                $data = [
                    'phieu_khaosat_id' => $phieuKhaoSat->id,
                    'cauhoi_id' => $cauHoiId
                ];

                switch ($cauHoi->loai_cauhoi) {
                    case 'multiple_choice':
                        $dataToInsert = [];
                        foreach ($traLoi as $phuongAnId) {
                            $dataToInsert[] = [
                                'phieu_khaosat_id' => $phieuKhaoSat->id,
                                'cauhoi_id' => $cauHoiId,
                                'phuongan_id' => $phuongAnId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        if (!empty($dataToInsert)) {
                            PhieuKhaoSatChiTiet::insert($dataToInsert);
                        }
                        break;

                    case 'single_choice':
                    case 'likert':
                        $data['phuongan_id'] = $traLoi;
                        PhieuKhaoSatChiTiet::create($data);
                        break;

                    // --- PHẦN SỬA LỖI ---
                    case 'rating':
                    case 'number':
                        $data['giatri_number'] = $traLoi;
                        PhieuKhaoSatChiTiet::create($data);
                        break;

                    case 'date':
                        $data['giatri_date'] = $traLoi;
                        PhieuKhaoSatChiTiet::create($data);
                        break;

                    case 'text':
                    default:
                        $data['giatri_text'] = $traLoi;
                        PhieuKhaoSatChiTiet::create($data);
                        break;
                }
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
                'redirect' => route('thanks')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 400);
        }
    }

    public function thanks()
    {
        return view('thanks');
    }
}