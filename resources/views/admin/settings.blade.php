<x-app-layout>
    <x-slot name="heading">⚙️ System Settings</x-slot>
    <x-slot name="subheading">Thông tin hệ thống và cấu hình Server</x-slot>

    <div class="p-8 space-y-8 max-w-4xl">

        {{-- App Info --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Ứng dụng</h2>
            </div>
            <table class="w-full">
                @php
                $appRows = [
                    ['App Name',  $info['app_name']],
                    ['Environment', $info['environment']],
                    ['Debug Mode', $info['debug'] ? '⚠️ ON (tắt trên production!)' : '✅ OFF'],
                    ['Timezone', $info['timezone']],
                ];
                @endphp
                @foreach($appRows as [$label, $value])
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm font-medium text-gray-500 w-48">{{ $label }}</td>
                    <td class="px-6 py-3 text-sm text-gray-900 font-mono">{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        {{-- Runtime --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Runtime</h2>
            </div>
            <table class="w-full">
                @php
                $runtimeRows = [
                    ['PHP Version',     $info['php_version']],
                    ['Laravel Version', $info['laravel_version']],
                    ['DB Connection',   $info['db_connection']],
                    ['DB Name',         $info['db_name']],
                    ['Cache Driver',    $info['cache_driver']],
                    ['Queue Driver',    $info['queue_driver']],
                    ['Mail Mailer',     $info['mail_mailer']],
                ];
                @endphp
                @foreach($runtimeRows as [$label, $value])
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm font-medium text-gray-500 w-48">{{ $label }}</td>
                    <td class="px-6 py-3 text-sm text-gray-900 font-mono">{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Artisan Quick Actions</h2>
            </div>
            <div class="p-6 space-y-3">
                <p class="text-sm text-gray-500 mb-4">Chạy lệnh tương ứng qua terminal:</p>
                @php
                $commands = [
                    ['php artisan route:clear',           'Xóa cache routes'],
                    ['php artisan config:clear',          'Xóa cache config'],
                    ['php artisan view:clear',            'Xóa cache views'],
                    ['php artisan cache:clear',           'Xóa application cache'],
                    ['php artisan optimize',              'Rebuild cache (config + routes)'],
                    ['php artisan migrate:status',        'Kiểm tra trạng thái migrations'],
                    ['php artisan sessions:generate',     'Generate sessions tháng hiện tại'],
                    ['php artisan schedule:run',          'Chạy scheduled tasks ngay bây giờ'],
                    ['php artisan migrate:fresh --seed',  'Reset DB + seed demo data'],
                ];
                @endphp
                <div class="space-y-2">
                    @foreach($commands as [$cmd, $desc])
                    <div class="flex items-center gap-4 py-2 border-b border-gray-50">
                        <code class="text-xs bg-gray-900 text-green-400 px-3 py-1.5 font-mono flex-shrink-0">{{ $cmd }}</code>
                        <span class="text-sm text-gray-500">{{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Storage / Server Info --}}
        <div class="bg-white border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Server Info</h2>
            </div>
            <table class="w-full">
                @php
                $serverRows = [
                    ['Server Software', $_SERVER['SERVER_SOFTWARE'] ?? php_uname('s')],
                    ['Server OS', PHP_OS],
                    ['Memory Limit', ini_get('memory_limit')],
                    ['Max Upload Size', ini_get('upload_max_filesize')],
                    ['Max Execution', ini_get('max_execution_time').'s'],
                    ['Loaded Config', php_ini_loaded_file()],
                ];
                @endphp
                @foreach($serverRows as [$label, $value])
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="px-6 py-3 text-sm font-medium text-gray-500 w-48">{{ $label }}</td>
                    <td class="px-6 py-3 text-sm text-gray-700 font-mono truncate max-w-md">{{ $value }}</td>
                </tr>
                @endforeach
            </table>
        </div>

        {{-- Warning if debug on --}}
        @if($info['debug'])
        <div class="bg-amber-50 border border-amber-200 p-4 flex gap-3">
            <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <p class="text-sm font-semibold text-amber-700">Debug Mode đang BẬT</p>
                <p class="text-sm text-amber-600 mt-0.5">Trên môi trường production, hãy đặt <code class="bg-amber-100 px-1">APP_DEBUG=false</code> trong file <code class="bg-amber-100 px-1">.env</code> để tránh lộ thông tin nhạy cảm.</p>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
