@extends('layouts.app')

@section('title', 'Báo cáo khảo sát')

@section('content')
    <div class="container">
        <h1>Báo cáo: {{ $dotKhaoSat->ten_dot }}</h1>

        <!-- Thống kê tổng quan -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $tongQuan['tong_phieu'] }}</h3>
                        <p class="text-muted mb-0">Tổng số phiếu</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $tongQuan['phieu_hoan_thanh'] }}</h3>
                        <p class="text-muted mb-0">Hoàn thành</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $tongQuan['ty_le'] }}%</h3>
                        <p class="text-muted mb-0">Tỷ lệ hoàn thành</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3>{{ $tongQuan['thoi_gian_tb'] }}</h3>
                        <p class="text-muted mb-0">Thời gian TB</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết từng câu hỏi -->
        @foreach($dotKhaoSat->mauKhaoSat->cauHoi as $cauHoi)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ $loop->iteration }}. {{ $cauHoi->noidung_cauhoi }}</h5>
                </div>
                <div class="card-body">
                    @if(isset($thongKeCauHoi[$cauHoi->id]))
                        @if($thongKeCauHoi[$cauHoi->id]['type'] == 'chart')
                            <canvas id="chart-{{ $cauHoi->id }}" height="100"></canvas>
                        @elseif($thongKeCauHoi[$cauHoi->id]['type'] == 'text')
                            <ul>
                                @foreach($thongKeCauHoi[$cauHoi->id]['data'] as $text)
                                    <li>{{ $text->giatri_text }}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <a href="{{ route('bao-cao.export', $dotKhaoSat) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Xuất Excel
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="bi bi-printer"></i> In báo cáo
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Vẽ biểu đồ cho từng câu hỏi
        @foreach($dotKhaoSat->mauKhaoSat->cauHoi as $cauHoi)
            @if(isset($thongKeCauHoi[$cauHoi->id]) && $thongKeCauHoi[$cauHoi->id]['type'] == 'chart')
                const ctx{{ $cauHoi->id }} = document.getElementById('chart-{{ $cauHoi->id }}').getContext('2d');
                const data{{ $cauHoi->id }} = @json($thongKeCauHoi[$cauHoi->id]['data']);

                new Chart(ctx{{ $cauHoi->id }}, {
                    type: '{{ in_array($cauHoi->loai_cauhoi, ["likert", "rating"]) ? "bar" : "pie" }}',
                    data: {
                        labels: data{{ $cauHoi->id }}.map(item => item.noidung),
                        datasets: [{
                            label: 'Số lượng',
                            data: data{{ $cauHoi->id }}.map(item => item.so_luong),
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF',
                                '#FF9F40'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: '{{ in_array($cauHoi->loai_cauhoi, ["likert", "rating"]) ? "none" : "right" }}'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const item = data{{ $cauHoi->id }}[context.dataIndex];
                                        return item.noidung + ': ' + item.so_luong + ' (' + item.ty_le + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        @endforeach
    </script>
@endpush