@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Đợt đang hoạt động</h6>
                                <h2 class="mb-0">{{ $stats['dot_dang_hoat_dong'] }}</h2>
                            </div>
                            <div class="fs-1">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Phiếu tháng này</h6>
                                <h2 class="mb-0">{{ $stats['tong_phieu_thang'] }}</h2>
                            </div>
                            <div class="fs-1">
                                <i class="bi bi-file-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Tỷ lệ hoàn thành</h6>
                                <h2 class="mb-0">{{ $stats['ty_le_hoan_thanh'] }}%</h2>
                            </div>
                            <div class="fs-1">
                                <i class="bi bi-graph-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-uppercase mb-1">Sắp kết thúc</h6>
                                <h2 class="mb-0">{{ $stats['dot_sap_ket_thuc'] }}</h2>
                            </div>
                            <div class="fs-1">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Đợt khảo sát mới nhất -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Đợt khảo sát đang hoạt động</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tên đợt</th>
                                        <th>Đối tượng</th>
                                        <th>Thời gian</th>
                                        <th>Tiến độ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dotKhaoSatMoiNhat as $dot)
                                        <tr>
                                            <td>{{ $dot->ten_dot }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $dot->mauKhaoSat->doiTuong->ten_doituong }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $dot->tungay->format('d/m/Y') }} -
                                                {{ $dot->denngay->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ $dot->getTyLeHoanThanh() }}%">
                                                        {{ $dot->getTyLeHoanThanh() }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('dot-khao-sat.show', $dot) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có đợt khảo sát nào</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thống kê theo đối tượng -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Thống kê theo đối tượng</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartDoiTuong"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart đối tượng
        const ctx = document.getElementById('chartDoiTuong').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($thongKeTheoDoiTuong->pluck('ten_doituong')) !!},
                datasets: [{
                    data: {!! json_encode($thongKeTheoDoiTuong->pluck('tong_phieu')) !!},
                    backgroundColor: [
                        '#0d6efd',
                        '#198754',
                        '#ffc107',
                        '#dc3545',
                        '#6c757d'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush