<?php
// Quick cleanup script to drop all non-core tables so migrate can run clean
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$core = ['migrations', 'users', 'password_reset_tokens', 'failed_jobs', 'personal_access_tokens'];

DB::statement('SET FOREIGN_KEY_CHECKS=0');
$tables = DB::select('SHOW TABLES');
foreach ($tables as $t) {
    $name = current((array) $t);
    if (!in_array($name, $core)) {
        Schema::dropIfExists($name);
        echo "Dropped: $name\n";
    }
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Also clean the migrations table of our pending entries so they can re-run cleanly
DB::table('migrations')->where('migration', 'like', '2026_%')->delete();
echo "\nCleared 2026 migration records.\nDone! Now run: php artisan migrate --seed\n";
