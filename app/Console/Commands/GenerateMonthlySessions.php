<?php

namespace App\Console\Commands;

use App\Models\ClassRoom;
use App\Services\SessionGeneratorService;
use Illuminate\Console\Command;

class GenerateMonthlySessions extends Command
{
    protected $signature   = 'sessions:generate
                                {--month= : Month in Y-m format, e.g. 2026-04. Defaults to current month.}
                                {--class= : Only generate for a specific class ID.}';

    protected $description = 'Generate class sessions from schedule rules for a given month.';

    public function handle(SessionGeneratorService $generator): int
    {
        $monthInput = $this->option('month') ?? now()->format('Y-m');

        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
            $end   = $start->copy()->endOfMonth();
        } catch (\Exception) {
            $this->error("Invalid month format. Use Y-m (e.g. 2026-04).");
            return self::FAILURE;
        }

        $this->info("Generating sessions: {$start->toDateString()} → {$end->toDateString()}");

        $classId = $this->option('class');

        if ($classId) {
            $class = ClassRoom::find($classId);
            if (!$class) {
                $this->error("Class ID {$classId} not found.");
                return self::FAILURE;
            }
            $count = $generator->generateForClass($class, $start->toDateString(), $end->toDateString());
            $this->info("✔ {$class->name}: {$count} sessions created.");
        } else {
            $report = $generator->generateForAllActive($start->toDateString(), $end->toDateString());
            $this->table(['Lớp học', 'Tạo mới'], collect($report)->map(fn($c, $n) => [$n, $c])->values()->toArray());
            $this->info('✔ Total: ' . array_sum($report) . ' sessions generated.');
        }

        return self::SUCCESS;
    }
}
