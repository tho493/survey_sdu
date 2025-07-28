<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin Panel</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center"
                href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Admin Panel</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Quản lý</div>

            <li class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.users.index') }}">
                    <i class="bi bi-people"></i>
                    <span>Người dùng</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.doi-tuong.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.doi-tuong.index') }}">
                    <i class="bi bi-diagram-3"></i>
                    <span>Đối tượng khảo sát</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.reports.index') }}">
                    <i class="bi bi-graph-up"></i>
                    <span>Báo cáo thống kê</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Hệ thống</div>

            <li class="nav-item {{ request()->routeIs('admin.config.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.config.index') }}">
                    <i class="bi bi-gear"></i>
                    <span>Cấu hình</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.logs.index') }}">
                    <i class="bi bi-file-text"></i>
                    <span>Nhật ký hệ thống</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="bi bi-list"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <!-- Notifications -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <h6 class="dropdown-header">Thông báo</h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="bi bi-file-text text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">12/12/2023</div>
                                        <span class="font-weight-bold">Có 5 phản hồi mới</span>
                                    </div>
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ auth()->user()->hoten }}
                                </span>
                                <i class="bi bi-person-circle"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person"></i> Thông tin cá nhân
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.logs.user') }}">
                                    <i class="bi bi-list"></i> Nhật ký hoạt động
                                </a>
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Main Content -->
                <div class="container-fluid">
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

                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Hệ thống khảo sát {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="bi bi-chevron-up"></i>
    </a>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>

</html>