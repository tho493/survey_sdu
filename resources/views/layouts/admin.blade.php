<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Hệ thống khảo sát</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #3730a3;
            --accent-color: #6366f1;
            --text-light: #f3f4f6;
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        #sidebar {
            min-width: 220px;
            max-width: 220px;
            background: rgba(30, 27, 75, 0.7);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            margin: 20px 0 20px 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.4s cubic-bezier(.4, 2, .6, 1);
            overflow: hidden;
            position: fixed;
            height: calc(100vh - 40px);
            z-index: 1000;
        }

        #sidebar .sidebar-header {
            background: transparent;
            border-bottom: none;
            padding: 32px 0 16px 0;
        }

        #sidebar ul.components {
            padding: 0;
        }

        #sidebar ul li {
            margin: 12px 0;
        }

        #sidebar ul li a {
            padding: 14px 24px;
            font-size: 1.1rem;
            border-radius: 16px;
            color: #fff;
            background: transparent;
            transition: all 0.25s;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.15);
            transform: scale(1.04);
        }

        #sidebar ul li a:hover i,
        #sidebar ul li a.active i {
            transform: scale(1.2);
        }

        #sidebar ul li a i {
            font-size: 1.4rem;
            margin-right: 0;
            transition: transform 0.3s;
        }

        /* Ẩn chữ khi thu nhỏ */
        #sidebar.collapsed {
            min-width: 64px;
            max-width: 64px;
            transition: all 0.3s cubic-bezier(.4, 2, .6, 1);
        }

        #sidebar.collapsed~#content {
            margin-left: 84px;
        }

        #sidebar ul li a span {
            transition: opacity 0.2s, margin 0.2s;
            opacity: 1;
            margin-left: 12px;
            white-space: nowrap;
        }

        #sidebar.collapsed ul li a span {
            opacity: 0;
            margin-left: -24px;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 992px) {
            #sidebar {
                min-width: 0;
                max-width: 0;
                border-radius: 0 24px 24px 0;
                margin: 0;
                height: 100vh;
            }

            #sidebar.sidebar-visible {
                min-width: 220px;
                max-width: 220px;
            }
        }

        @media (max-width: 992px) {

            #content,
            #sidebar.collapsed~#content {
                margin-left: 0 !important;
            }
        }

        /* Content */
        #content {
            width: 100%;
            min-height: 100vh;
            margin-left: 240px;
            transition: var(--transition);
        }

        #content.active {
            margin-left: 0;
        }

        .navbar {
            padding: 15px 30px;
            background: #fff;
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, .03);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-btn {
            background: transparent;
            border: none;
            padding: 8px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .navbar-btn:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .profile-dropdown .nav-link {
            padding: 8px 16px;
            border-radius: 50px;
            transition: var(--transition);
            font-weight: 500;
        }

        .profile-dropdown .nav-link:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            padding: 8px;
        }

        .dropdown-item {
            padding: 8px 16px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
        }

        .main-content {
            padding: 30px;
            flex: 1;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1f5f9;
            padding: 20px;
            font-weight: 600;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .05);
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
        }

        .alert-info {
            background-color: #eff6ff;
            color: #1e40af;
        }

        @media (max-width: 992px) {
            #sidebar {
                margin-left: -280px;
            }

            #sidebar.active {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }

            #content.active {
                margin-left: 280px;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(-10px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .alert {
            animation: slideIn 0.3s ease;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="collapsed">
            <!-- <div class="sidebar-header">
                <a href="{{ route('admin.dashboard.index') }}" class="text-white" style="text-decoration: none;">
                    <h4><i class="bi bi-grid-1x2-fill"></i> <span>Admin Panel</span></h4>
                </a>
            </div> -->
            <ul class="list-unstyled components">
                <li>
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="{{ request()->routeIs('admin.dashboard.*') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.mau-khao-sat.index') }}"
                        class="{{ request()->routeIs('admin.mau-khao-sat.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text-fill"></i> <span>Mẫu khảo sát</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dot-khao-sat.index') }}"
                        class="{{ request()->routeIs('admin.dot-khao-sat.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-check-fill"></i> <span>Đợt khảo sát</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.bao-cao.index') }}"
                        class="{{ request()->routeIs('admin.bao-cao.*') ? 'active' : '' }}">
                        <i class="bi bi-graph-up-arrow"></i> <span>Báo cáo</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.users.index') }}"
                        class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge-fill"></i> <span>Người dùng</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.config.index') }}"
                        class="{{ request()->routeIs('admin.config.*') ? 'active' : '' }}">
                        <i class="bi bi-gear-fill"></i> <span>Cấu hình</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.logs.index') }}"
                        class="{{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> <span>Nhật ký</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <!-- Tiêu đề trang web -->
                    <h4 class="navbar-brand">
                        <i class="bi bi-grid-1x2-fill"></i> <span>Admin Panel</span>
                    </h4>

                    <div class="ms-auto profile-dropdown">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 me-2"></i>
                                <span class="fw-medium">{{ auth()->user()->hoten ?? 'Admin' }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.edit', auth()->user()->id) }}">
                                        <i class="bi bi-person me-2"></i> Thông tin cá nhân
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Sidebar thu nhỏ khi load
            $('#sidebar').addClass('collapsed');
            $('#content').addClass('active')
            // Hover để mở rộng
            $('#sidebar').on('mouseenter', function () {
                $(this).removeClass('collapsed');
                $('#content').removeClass('active')
            }).on('mouseleave', function () {
                $(this).addClass('collapsed');
                $('#content').addClass('active')
            });

            // Auto hide alerts after 5 seconds
            setTimeout(function () {
                $('.alert:not(.alert-permanent)').fadeOut('slow', function () {
                    $(this).alert('close');
                });
            }, 5000);

            // Add smooth scrolling
            $('a[href*="#"]').on('click', function (e) {
                if ($(this).attr('href').length > 1 && $(this).attr('href').charAt(0) === '#') {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $($(this).attr('href')).offset().top
                    }, 500, 'linear');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>