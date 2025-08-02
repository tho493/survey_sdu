<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống khảo sát</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .survey-card {
            transition: transform 0.2s;
            cursor: pointer;
        }

        .survey-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 mb-3">Hệ thống khảo sát</h1>
            <p class="lead">Chào mừng bạn đến với hệ thống khảo sát trực tuyến</p>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-speedometer2"></i> Vào trang quản trị
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-light">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập quản trị
                </a>
            @endauth
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Các đợt khảo sát đang diễn ra</h2>
                <p class="text-muted">Vui lòng chọn khảo sát phù hợp với bạn để tham gia</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tìm kiếm khảo sát..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row" id="surveyList">
            @forelse($dotKhaoSats as $dot)
                <div class="col-md-6 col-lg-4 mb-4 survey-item">
                    <div class="card h-100 survey-card" onclick="window.location.href='{{ route('khao-sat.show', $dot) }}'">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary">
                                    {{ $dot->mauKhaoSat->doiTuong->ten_doituong }}
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-calendar-check"></i> Đang mở
                                </span>
                            </div>

                            <h5 class="card-title">{{ $dot->ten_dot }}</h5>

                            @if($dot->mota)
                                <p class="card-text text-muted small">{{ Str::limit($dot->mota, 100) }}</p>
                            @endif

                            <div class="mt-3">
                                <div class="d-flex justify-content-between text-muted small">
                                    <span>
                                        <i class="bi bi-calendar"></i>
                                        {{ $dot->tungay->format('d/m/Y') }}
                                    </span>
                                    <span>
                                        <i class="bi bi-calendar-x"></i>
                                        {{ $dot->denngay->format('d/m/Y') }}
                                    </span>
                                </div>

                                @php
                                    $daysLeft = now()->diffInDays($dot->denngay, false);
                                @endphp

                                @if($daysLeft <= 3 && $daysLeft >= 0)
                                    <div class="alert alert-warning py-1 px-2 mt-2 mb-0 small">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        Còn {{ $daysLeft }} ngày
                                    </div>
                                @endif
                            </div>

                            <div class="text-center mt-3">
                                <span class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-right-circle"></i> Tham gia khảo sát
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i>
                        Hiện tại không có đợt khảo sát nào đang diễn ra.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center text-muted">
            <p class="mb-0">&copy; {{ date('Y') }} Hệ thống khảo sát. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const surveyItems = document.querySelectorAll('.survey-item');

            surveyItems.forEach(item => {
                const title = item.querySelector('.card-title').textContent.toLowerCase();
                const badge = item.querySelector('.badge').textContent.toLowerCase();

                if (title.includes(searchTerm) || badge.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>