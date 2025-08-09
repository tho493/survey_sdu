<?php

namespace App\Exports;

use App\Models\DotKhaoSat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KhaoSatExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $dotKhaoSat;
    protected $cauHoiHeaders = [];
    protected $cauHoiCollection;

    public function __construct(DotKhaoSat $dotKhaoSat)
    {
        // Tải trước tất cả dữ liệu cần thiết để tối ưu
        $this->dotKhaoSat = $dotKhaoSat->load([
            'mauKhaoSat.cauHoi' => function ($query) {
                $query->orderBy('thutu');
            },
            'phieuKhaoSat' => function ($query) {
                $query->where('trangthai', 'completed')
                    ->with(['chiTiet.phuongAn']);
            }
        ]);

        // Tạo mảng header cho các câu hỏi
        $this->cauHoiCollection = $this->dotKhaoSat->mauKhaoSat->cauHoi;
        foreach ($this->cauHoiCollection as $index => $cauHoi) {
            $this->cauHoiHeaders[] = "Câu " . ($index + 1) . ": " . $cauHoi->noidung_cauhoi;
        }
    }

    /**
     * @return \Illuminate\Support\Collection // WITH ERROR
     */
    public function collection(): \App\Models\PhieuKhaoSat
    {
        // Trả về collection các phiếu khảo sát đã hoàn thành
        return $this->dotKhaoSat->phieuKhaoSat;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Ghép các header cố định với header câu hỏi
        return array_merge([
            'ID Phiếu',
            'Mã người trả lời',
            'Họ tên',
            'Đơn vị',
            'Email',
            'Thời gian hoàn thành',
        ], $this->cauHoiHeaders);
    }

    /**
     * @param mixed $phieu
     * @return array
     */
    public function map($phieu): array
    {
        // Tạo một mảng các câu trả lời đã được index theo cauhoi_id để tìm kiếm nhanh
        $answersByQuestionId = $phieu->chiTiet->keyBy('cauhoi_id');
        $rowAnswers = [];

        // Lặp qua danh sách câu hỏi để đảm bảo các câu trả lời đúng thứ tự
        foreach ($this->cauHoiCollection as $cauHoi) {
            $answer = $answersByQuestionId->get($cauHoi->id);
            if ($answer) {
                // Sử dụng accessor `getGiaTriAttribute` đã tạo trong model PhieuKhaoSatChiTiet
                $rowAnswers[] = $answer->GiaTri;
            } else {
                $rowAnswers[] = ''; // Để trống nếu không có câu trả lời
            }
        }

        // Ghép thông tin phiếu với các câu trả lời
        return array_merge([
            $phieu->id,
            $phieu->ma_nguoi_traloi,
            $phieu->metadata['hoten'] ?? '',
            $phieu->metadata['donvi'] ?? '',
            $phieu->metadata['email'] ?? '',
            $phieu->thoigian_hoanthanh ? $phieu->thoigian_hoanthanh->format('d/m/Y H:i') : '',
        ], $rowAnswers);
    }

    /**
     * Định dạng style cho file Excel.
     */
    public function styles(Worksheet $sheet)
    {
        // In đậm và tô màu nền cho dòng header
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDDDDDD'],
                ]
            ],
        ];
    }
}