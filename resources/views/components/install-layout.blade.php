<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt TutorCenter CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-600 flex items-center justify-center text-white text-sm font-bold font-display">TC</div>
                <span class="text-xl font-bold text-gray-900 font-display">TutorCenter CRM</span>
            </div>
            <p class="text-sm text-gray-500">Trình cài đặt hệ thống</p>
        </div>

        {{-- Steps indicator --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            @php $currentStep = $step ?? 1; @endphp
            @foreach([1=>'Yêu cầu', 2=>'Database', 3=>'Admin', 4=>'Cấu hình', 5=>'Cài đặt'] as $num => $label)
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center text-xs font-semibold
                        {{ $num < $currentStep ? 'bg-green-600 text-white' : ($num == $currentStep ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-400') }}">
                        @if($num < $currentStep) ✓ @else {{ $num }} @endif
                    </div>
                    <span class="text-xs {{ $num == $currentStep ? 'text-gray-900 font-medium' : 'text-gray-400' }} hidden sm:inline">{{ $label }}</span>
                    @if($num < 5)
                        <div class="w-6 h-px {{ $num < $currentStep ? 'bg-green-300' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Card --}}
        <div class="bg-white border border-gray-100 shadow-sm p-8">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <p>⚠ {{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{ $slot }}
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">TutorCenter CRM v1.0 — Hệ thống Quản lý Trung tâm Gia sư</p>
    </div>

</body>
</html>
