<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Hệ thống khảo sát')</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="bi bi-clipboard-data"></i> Hệ thống khảo sát
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-file-earmark-text"></i> Quản lý khảo sát
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('mau-khao-sat.index') }}">
                                        Mẫu khảo sát
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dot-khao-sat.index') }}">
                                        Đợt khảo sát
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('bao-cao.index') }}">
                                <i class="bi bi-graph-up"></i> Báo cáo
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear"></i> Hệ thống
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('users.index') }}">
                                            Tài khoản
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('doi-tuong.index') }}">
                                            Đối tượng khảo sát
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('cau-hinh.index') }}">
                                            Cấu hình
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav">
                    @auth
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-file-earmark-text"></i> Quản lý khảo sát
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('mau-khao-sat.index') }}">
                                            Mẫu khảo sát
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dot-khao-sat.index') }}">
                                            Đợt khảo sát
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('bao-cao.index') }}">
                                    <i class="bi bi-graph-up"></i> Báo cáo
                                </a>
                            </li>
                        </ul>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>

</html>