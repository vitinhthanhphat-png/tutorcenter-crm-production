<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — TutorCenter</title>
    <meta name="description" content="Hệ thống Quản lý Trung tâm Gia sư">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-900 antialiased" style="font-family:'Inter',sans-serif">

<div class="flex h-screen overflow-hidden">

    {{-- ============================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================ --}}
    <aside class="flex flex-col w-60 flex-shrink-0 bg-white border-r border-gray-100 z-10">
        
        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-5 border-b border-gray-100">
            <div class="w-7 h-7 bg-red-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="font-family:'Space Grotesk',sans-serif">TC</div>
            <span class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">TutorCenter</span>
        </a>

        {{-- Tenant Name --}}
        @if(auth()->user()?->tenant)
        <div class="px-5 py-3 bg-red-50 border-b border-red-100">
            <p class="text-xs text-red-700 font-medium truncate">{{ auth()->user()->tenant->name }}</p>
        </div>
        @endif

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-0.5">
            @php
            $role = auth()->user()->role ?? '';
            $isMgr    = in_array($role, ['super_admin', 'center_manager']);
            $isAcct   = $role === 'accountant';
            $isOps    = in_array($role, ['operations', 'accountant', 'center_manager', 'super_admin']);
            $isTeacher= in_array($role, ['teacher', 'tutor']);

            $nav = array_filter([
                ['route' => 'dashboard',        'label' => 'Dashboard',
                 'icon'  => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                 'show'  => true],

                ['route' => 'leads.index',      'label' => 'CRM Leads',
                 'icon'  => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                 'show'  => $isOps],

                ['route' => 'students.index',   'label' => 'Học sinh',
                 'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                 'show'  => !$isTeacher],

                ['route' => 'classes.index',    'label' => 'Lớp học',
                 'icon'  => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
                 'show'  => true],

                ['route' => 'calendar.index',   'label' => 'TKB / Lịch học',
                 'icon'  => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                 'show'  => true],

                ['route' => 'finance.invoices', 'label' => 'Tài chính',
                 'icon'  => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                 'show'  => $isMgr || $isAcct],

                ['route' => 'cashbook.index',   'label' => 'Sổ Thu Chi',
                 'icon'  => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
                 'show'  => $isMgr || $isAcct],

                ['route' => 'payroll.index',    'label' => 'Bảng Lương',
                 'icon'  => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                 'show'  => $isMgr || $isAcct],

                ['route' => 'finance.report',   'label' => 'Báo cáo',
                 'icon'  => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                 'show'  => $isMgr || $isAcct],

                ['route' => 'export.index',     'label' => 'Xuất Dữ Liệu',
                 'icon'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
                 'show'  => $isMgr || $isAcct],

                 ['route' => 'grades.list', 'label' => 'Bảng Điểm',
                  'icon'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                  'show'  => $isMgr || $isTeacher],
            ], fn($i) => $i['show']);
            @endphp


            @foreach($nav as $item)
                @php $active = request()->routeIs(rtrim($item['route'],'index').'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm rounded-sm transition-all duration-150
                          {{ $active ? 'bg-red-50 text-red-600 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $active ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach

            {{-- ═══ Student / Parent Portal ═══ --}}
            @if(in_array(auth()->user()->role ?? '', ['student', 'parent']))
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="px-3 mb-2 text-xs font-semibold text-gray-300 uppercase tracking-wide">Cổng Học sinh</p>
                @foreach([
                    ['route' => 'portal.index',      'label' => 'Trang của tôi',   'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'portal.attendance', 'label' => 'Điểm danh',       'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['route' => 'portal.invoices',   'label' => 'Học phí',         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ] as $pi)
                @php $piActive = request()->routeIs($pi['route']); @endphp
                <a href="{{ route($pi['route']) }}"
                   class="group flex items-center gap-3 px-3 py-2.5 text-sm rounded-sm transition-all duration-150
                          {{ $piActive ? 'bg-green-50 text-green-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $piActive ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $pi['icon'] }}"/>
                    </svg>
                    {{ $pi['label'] }}
                </a>
                @endforeach
            </div>
            @endif

            {{-- ═══ Super Admin Panel ═══ --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="px-3 mb-2 text-xs font-semibold text-gray-300 uppercase tracking-wide">Admin Panel</p>

                @php
                $adminNav = [
                    ['route' => 'admin.index',             'label' => 'System Overview', 'icon' => 'M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18'],
                    ['route' => 'admin.tenants',           'label' => 'Tenants',         'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['route' => 'admin.branches',          'label' => 'Chi nhánh',       'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'admin.users',             'label' => 'Users',           'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['route' => 'admin.dispatch-requests', 'label' => 'Ph\u00ea duy\u1ec7t \u0110P',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['route' => 'admin.assignments',       'label' => 'Assignments',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['route' => 'admin.audit',             'label' => 'Audit Log',       'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['route' => 'admin.settings',          'label' => 'Settings',        'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
                @endphp

                @foreach($adminNav as $item)
                @php $adminActive = request()->routeIs($item['route'].'*'); @endphp
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 px-3 py-2 text-sm rounded-sm transition-all duration-150
                          {{ $adminActive ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $adminActive ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-400' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
                @endforeach
            </div>
            @endif
        </nav>

        {{-- User footer --}}
        <div class="flex items-center gap-3 px-4 py-4 border-t border-gray-100">
            <div class="w-8 h-8 rounded-full bg-gray-900 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                {{ strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400 truncate">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Đăng xuất" class="text-gray-400 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- ============================================ --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ============================================ --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Top bar --}}
        <header class="flex items-center justify-between px-8 py-4 bg-white border-b border-gray-100 flex-shrink-0">
            <div>
                @isset($heading)
                    <h1 class="text-lg font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">{{ $heading }}</h1>
                @endisset
                @isset($subheading)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $subheading }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                @isset($actions)
                    {{ $actions }}
                @endisset
                <div class="w-px h-5 bg-gray-200"></div>
                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::today()->format('d/m/Y') }}</span>
            </div>
        </header>

        {{-- Page slot --}}
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>

</div>

@livewireScripts
@stack('scripts')
</body>
</html>

