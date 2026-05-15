<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELCO - Admin Cabang</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        .swal-elco-popup {
            border-radius: 24px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .swal-elco-confirm {
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
        }
        .swal-elco-cancel {
            border-radius: 12px !important;
            font-weight: 500 !important;
            padding: 10px 24px !important;
            background: #f3f4f6 !important;
            color: #4b5563 !important;
        }
    </style>
</head>
<body class="text-gray-800 antialiased flex h-screen overflow-hidden font-sans">

    {{-- Sidebar Admin --}}
    @include('components.admin.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#F4F6F8]">
        @include('components.admin.navbar')
        <div class="flex-1 overflow-y-auto p-8 pt-0 hide-scrollbar">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>