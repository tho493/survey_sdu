<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title', "Trang chủ") - Hệ thống khảo sát trực tuyến </title>

    {{-- Tailwind via CDN for quick prototyping; replace with @vite for production --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        /* Optional: custom container width to feel closer to the screenshot */
        .container-narrow {
            max-width: 1100px
        }

        .shadow-soft {
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06)
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
        }

        @yield('style')
    </style>
</head>

<body class="bg-white text-slate-800">
    {{-- Top bar --}}
    <header class="bg-[#1f66b3] text-white">
        <div class="mx-auto container-narrow px-4">
            <div class="flex items-center justify-between py-3">
                <div class="flex items-center gap-3">
                    {{-- Logo placeholder --}}
                    <div class="h-10 w-10 rounded-full bg-white/95 grid place-items-center">
                        <span><img src="../image/logo.png" alt=""></span>
                    </div>
                    <p class="hidden sm:block text-sm md:text-base font-semibold tracking-wide">
                        CHẤT LƯỢNG TOÀN DIỆN · HỢP TÁC SÂU RỘNG · PHÁT TRIỂN BỀN VỮNG
                    </p>
                </div>
                <nav>
                    <a href="https://saodo.edu.vn/vi/about/Gioi-thieu-ve-truong-Dai-hoc-Sao-Do.html"
                        class="text-white/90 hover:text-white text-sm font-medium">GIỚI THIỆU</a>
                </nav>
            </div>
        </div>
    </header>

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="border-t border-slate-200 py-6">
        <div class="mx-auto container-narrow px-4 text-center text-slate-500 text-sm">
            © {{ date('Y') }} Trường Đại học Sao Đỏ · Hệ thống khảo sát trực tuyến
        </div>
    </footer>
</body>

</html>