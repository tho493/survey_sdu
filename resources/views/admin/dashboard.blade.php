@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4">Dashboard Quản trị</h1>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Người dùng
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_users']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-people fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Đợt đang hoạt động
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['active_surveys'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Phản hồi tháng này
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_responses']) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-file-text fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Mẫu khảo sát
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['total_templates'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="bi bi-clipboard-data fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Biểu đồ phản hồi -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Phản hồi 7 ngày gần nhất</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="responseChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Thống kê theo đối tượng -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thống kê theo đối tượng</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="objectChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Hoạt động gần đây -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Hoạt động gần đây</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Người thực hiện</th>
                                        <th>Hành động</th>
                                        <th>Thời gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        <tr>
                                            <td>{{ $activity->nguoi_thuchien }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $activity->hanhdong == 'create' ? 'success' : ($activity->hanhdong == 'delete' ? 'danger' : 'info') }}">
                                                    {{ $activity->hanhdong }}
                                                </span>
                                                {{ $activity->bang_thaydoi }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($activity->thoigian)->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Người dùng hoạt động -->
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Người dùng hoạt động</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Họ tên</th>
                                        <th>Quyền</th>
                                        <th>Đăng nhập lần cuối</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeUsers as $user)
                                        <tr>
                                            <td>{{ $user->hoten }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $user->quyen == 'admin' ? 'danger' : ($user->quyen == 'manager' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($user->quyen) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Chưa đăng nhập' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ phản hồi
        const responseCtx = document.getElementById('responseChart').getContext('2d');
        new Chart(responseCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($responseChart['labels']) !!},
                datasets: [{
                    label: 'Số phản hồi',
                    data: {!! json_encode($responseChart['data']) !!},
                    borderColor: 'rgb(78, 115, 223)',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Biểu đồ đối tượng
        const objectCtx = document.getElementById('objectChart').getContext('2d');
        new Chart(objectCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($objectStats->pluck('ten_doituong')) !!},
                datasets: [{
                    data: {!! json_encode($objectStats->pluck('total_responses')) !!},
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
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