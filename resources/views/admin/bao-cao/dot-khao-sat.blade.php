@extends('layouts.admin')

@section('title', 'Báo cáo: ' . $dotKhaoSat->ten_dot)

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('bao-cao.index') }}">Báo cáo</a></li>
                <li class="breadcrumb-item active">{{ $dotKhaoSat->ten_dot }}</li>
            </ol>
        </nav>

        {{-- Header Báo cáo --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">{{ $dotKhaoSat->ten_dot }}</h1>
                <p class="text-muted mb-0">
                    Đối tượng: <strong>{{ $dotKhaoSat->mauKhaoSat->doiTuong->ten_doituong ?? 'N/A' }}</strong> |
                    Thời gian: <strong>{{ $dotKhaoSat->tungay->format('d/m/Y') }} -
                        {{ $dotKhaoSat->denngay->format('d/m/Y') }}</strong>
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('bao-cao.export', ['dotKhaoSat' => $dotKhaoSat, 'format' => 'excel']) }}"
                    class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                </a>
                <a href="{{ route('bao-cao.export', ['dotKhaoSat' => $dotKhaoSat, 'format' => 'pdf']) }}"
                    class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                </a>
            </div>
        </div>

        {{-- Thống kê tổng quan --}}
        <div class="row mb-4">
            {{-- Cards thống kê như trong dashboard --}}
        </div>

        {{-- Biểu đồ phản hồi theo ngày --}}
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Phản hồi theo ngày</h6>
            </div>
            <div class="card-body">
                <canvas id="dailyResponseChart" height="100"></canvas>
            </div>
        </div>

        {{-- Thống kê chi tiết từng câu hỏi --}}
        <h3 class="h4 mb-3">Phân tích câu trả lời</h3>
        @forelse($dotKhaoSat->mauKhaoSat->cauHoi->sortBy('thutu') as $index => $cauHoi)
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Câu {{ $index + 1 }}: {{ $cauHoi->noidung_cauhoi }}
                    </h6>
                    <small class="text-muted">{{ $thongKeCauHoi[$cauHoi->id]['total'] }} lượt trả lời</small>
                </div>
                <div class="card-body">
                    @php $stats = $thongKeCauHoi[$cauHoi->id]; @endphp

                    @if($stats['type'] == 'chart' && !$stats['data']->isEmpty())
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="chart-cauhoi-{{ $cauHoi->id }}" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Phương án</th>
                                                <th class="text-center">Số lượng</th>
                                                <th class="text-center">Tỷ lệ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stats['data'] as $item)
                                                <tr>
                                                    <td>{{ $item->noidung ?? 'Không xác định' }}</td>
                                                    <td class="text-center">{{ $item->so_luong }}</td>
                                                    <td class="text-center">{{ $item->ty_le }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @elseif($stats['type'] == 'text' && !$stats['data']->isEmpty())
                        <ul class="list-group">
                            @foreach($stats['data'] as $item)
                                <li class="list-group-item">{{ $item->giatri_text }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted text-center">Chưa có dữ liệu cho câu hỏi này.</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-muted">Mẫu khảo sát này chưa có câu hỏi nào.</p>
        @endforelse

        {{-- Danh sách chi tiết phiếu trả lời --}}
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Danh sách phiếu đã hoàn thành</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã người trả lời</th>
                                <th>Họ tên</th>
                                <th>Đơn vị</th>
                                <th>Thời gian hoàn thành</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($danhSachPhieu as $phieu)
                                <tr>
                                    <td>{{ $phieu->ma_nguoi_traloi }}</td>
                                    <td>{{ $phieu->metadata['hoten'] ?? 'N/A' }}</td>
                                    <td>{{ $phieu->metadata['donvi'] ?? 'N/A' }}</td>
                                    <td>{{ $phieu->thoigian_hoanthanh ? $phieu->thoigian_hoanthanh->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" title="Xem chi tiết phiếu">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Chưa có phiếu nào được hoàn thành.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $danhSachPhieu->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Biểu đồ phản hồi theo ngày
            const dailyCtx = document.getElementById('dailyResponseChart')?.getContext('2d');
            if (dailyCtx) {
                const dailyData = @json($thongKeTheoNgay);
                new Chart(dailyCtx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(item => new Date(item.ngay).toLocaleDateString('vi-VN')),
                        datasets: [{
                            label: 'Số phiếu hoàn thành',
                            data: dailyData.map(item => item.so_luong),
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78, 115, 223, 0.1)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: true }
                });
            }

            // Biểu đồ cho từng câu hỏi
            @foreach($dotKhaoSat->mauKhaoSat->cauHoi as $cauHoi)
                @php $stats = $thongKeCauHoi[$cauHoi->id]; @endphp
                @if($stats['type'] == 'chart' && !$stats['data']->isEmpty())
                    const ctx{{ $cauHoi->id }} = document.getElementById('chart-cauhoi-{{ $cauHoi->id }}')?.getContext('2d');
                    if (ctx{{ $cauHoi->id }}) {
                        new Chart(ctx{{ $cauHoi->id }}, {
                            type: 'pie', // hoặc 'bar'
                            data: {
                                labels: {!! json_encode($stats['data']->pluck('noidung')) !!},
                                datasets: [{
                                    data: {!! json_encode($stats['data']->pluck('so_luong')) !!},
                                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } }
                            }
                        });
                    }
                @endif
            @endforeach
                });
    </script>
@endpush