<x-app-layout>
    <x-slot name="heading">Lead — {{ $lead->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('leads.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Leads</a>
        <a href="{{ route('leads.edit', $lead) }}" class="ml-3 text-sm text-indigo-600 hover:text-indigo-700">Sửa</a>
        @if(!$lead->converted_to_student_id)
        <form method="POST" action="{{ route('leads.convert', $lead) }}" class="inline ml-3">
            @csrf
            <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1 hover:bg-green-700"
                    onclick="return confirm('Chuyển thành Học sinh và tạo hồ sơ?')">→ Chuyển thành Học sinh</button>
        </form>
        @endif
    </x-slot>

    <div class="p-6 max-w-lg space-y-4">
        <div class="bg-white border border-gray-100 divide-y divide-gray-50">
            @foreach([
                ['Trạng thái',     '<span class="px-2 py-0.5 text-xs '.$lead->statusColor().'">'.$lead->statusLabel().'</span>'],
                ['SĐT',            $lead->phone ?? '—'],
                ['Email',          $lead->email ?? '—'],
                ['Phụ huynh',      $lead->parent_name ?? '—'],
                ['Nguồn',          $lead->source ?? '—'],
                ['Khóa quan tâm',  $lead->interested_course ?? '—'],
                ['Hẹn gặp',        $lead->follow_up_at?->format('d/m/Y') ?? '—'],
                ['Phụ trách',      $lead->assignedTo?->name ?? '—'],
                ['Chi nhánh',      $lead->branch?->name ?? '—'],
                ['Ghi chú',        $lead->note ?? '—'],
            ] as [$label, $value])
            <div class="flex px-5 py-3 gap-4">
                <span class="text-xs text-gray-400 w-32 flex-shrink-0 pt-0.5">{{ $label }}</span>
                <span class="text-sm text-gray-800">{!! $value !!}</span>
            </div>
            @endforeach
            @if($lead->converted_to_student_id)
            <div class="flex px-5 py-3 gap-4">
                <span class="text-xs text-gray-400 w-32 flex-shrink-0 pt-0.5">Học sinh</span>
                <a href="{{ route('students.show', $lead->student) }}" class="text-sm text-green-600 hover:underline">
                    {{ $lead->student?->name }} ✓
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
