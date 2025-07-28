<?php

namespace App\Exports;

use App\Models\DotKhaoSat;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class KhaoSatExport
{
    protected $dotKhaoSat;

    public function __construct(DotKhaoSat $dotKhaoSat)
    {
        $this->dotKhaoSat = $dotKhaoSat;
    }

    /**
     * maatwebsite/excel v1.1 expects you to return a closure for each sheet
     */
    public function sheets()
    {
        $sheets = [];

        // Tổng quan sheet
        $sheets['Tổng quan'] = function ($sheet) {
            $tongQuanSheet = new TongQuanSheet($this->dotKhaoSat);
            $sheet->appendRow($tongQuanSheet->headings());
            foreach ($tongQuanSheet->collection() as $row) {
                $sheet->appendRow($row);
            }
        };

        // Chi tiết sheet
        $sheets['Chi tiết phiếu'] = function ($sheet) {
            $chiTietSheet = new ChiTietSheet($this->dotKhaoSat);
            $sheet->appendRow($chiTietSheet->headings());
            foreach ($chiTietSheet->collection() as $row) {
                $sheet->appendRow((array) $row);
            }
        };

        // Thêm sheet cho từng câu hỏi (nếu có class CauHoiSheet)
        if (isset($this->dotKhaoSat->mauKhaoSat->cauHoi)) {
            foreach ($this->dotKhaoSat->mauKhaoSat->cauHoi as $index => $cauHoi) {
                $sheetName = 'Câu hỏi ' . ($index + 1);
                $sheets[$sheetName] = function ($sheet) use ($cauHoi, $index) {
                    if (class_exists('\App\Exports\CauHoiSheet')) {
                        $cauHoiSheet = new \App\Exports\CauHoiSheet($this->dotKhaoSat, $cauHoi, $index + 1);
                        if (method_exists($cauHoiSheet, 'headings')) {
                            $sheet->appendRow($cauHoiSheet->headings());
                        }
                        foreach ($cauHoiSheet->collection() as $row) {
                            $sheet->appendRow((array) $row);
                        }
                    }
                };
            }
        }

        return $sheets;
    }
}

class TongQuanSheet
{
    protected $dotKhaoSat;

    public function __construct($dotKhaoSat)
    {
        $this->dotKhaoSat = $dotKhaoSat;
    }

    public function collection()
    {
        return [
            ['Tên đợt khảo sát', $this->dotKhaoSat->ten_dot],
            ['Mẫu khảo sát', $this->dotKhaoSat->mauKhaoSat->ten_mau],
            ['Đối tượng', $this->dotKhaoSat->mauKhaoSat->doiTuong->ten_doituong],
            ['Thời gian', $this->dotKhaoSat->tungay->format('d/m/Y') . ' - ' . $this->dotKhaoSat->denngay->format('d/m/Y')],
            ['Tổng số phiếu', $this->dotKhaoSat->phieuKhaoSat()->count()],
            ['Phiếu hoàn thành', $this->dotKhaoSat->phieuKhaoSat()->where('trangthai', 'completed')->count()],
            ['Tỷ lệ hoàn thành', $this->dotKhaoSat->getTyLeHoanThanh() . '%']
        ];
    }

    public function headings()
    {
        return ['Thông tin', 'Giá trị'];
    }
}

class ChiTietSheet
{
    protected $dotKhaoSat;

    public function __construct($dotKhaoSat)
    {
        $this->dotKhaoSat = $dotKhaoSat;
    }

    public function collection()
    {
        return DB::table('phieu_khaosat as pk')
            ->where('pk.dot_khaosat_id', $this->dotKhaoSat->id)
            ->select(
                'pk.ma_nguoi_traloi',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pk.metadata, '$.hoten')) as ho_ten"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(pk.metadata, '$.donvi')) as don_vi"),
                'pk.trangthai',
                'pk.thoigian_batdau',
                'pk.thoigian_hoanthanh'
            )
            ->get();
    }

    public function headings()
    {
        return [
            'Mã người trả lời',
            'Họ tên',
            'Đơn vị',
            'Trạng thái',
            'Thời gian bắt đầu',
            'Thời gian hoàn thành'
        ];
    }
}