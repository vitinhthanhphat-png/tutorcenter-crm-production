<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Tenant;

class InstallController extends Controller
{
    /**
     * Step 1: Check server requirements
     */
    public function index()
    {
        $requirements = $this->checkRequirements();
        $allPassed = collect($requirements)->every(fn($r) => $r['status']);
        $permissions = $this->checkPermissions();
        $allPermissions = collect($permissions)->every(fn($p) => $p['status']);

        return view('install.step1-requirements', compact('requirements', 'allPassed', 'permissions', 'allPermissions'));
    }

    /**
     * Step 2: Database configuration form
     */
    public function database()
    {
        return view('install.step2-database');
    }

    /**
     * Step 2: Save database configuration
     */
    public function saveDatabase(Request $request)
    {
        $request->validate([
            'db_host'     => 'required|string',
            'db_port'     => 'required|numeric',
            'db_database' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        // Test connection
        try {
            $pdo = new \PDO(
                "mysql:host={$request->db_host};port={$request->db_port};dbname={$request->db_database}",
                $request->db_username,
                $request->db_password ?? ''
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'db_host' => 'Không thể kết nối database: ' . $e->getMessage(),
            ]);
        }

        // Store in session for step 3
        session([
            'install.db_host'     => $request->db_host,
            'install.db_port'     => $request->db_port,
            'install.db_database' => $request->db_database,
            'install.db_username' => $request->db_username,
            'install.db_password' => $request->db_password ?? '',
        ]);

        return redirect()->route('install.admin');
    }

    /**
     * Step 3: Admin account form
     */
    public function admin()
    {
        if (! session('install.db_host')) {
            return redirect()->route('install.database');
        }
        return view('install.step3-admin');
    }

    /**
     * Step 3: Save admin account
     */
    public function saveAdmin(Request $request)
    {
        $request->validate([
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|max:255',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        session([
            'install.admin_name'     => $request->admin_name,
            'install.admin_email'    => $request->admin_email,
            'install.admin_password' => $request->admin_password,
        ]);

        return redirect()->route('install.settings');
    }

    /**
     * Step 4: App settings form
     */
    public function settings()
    {
        if (! session('install.admin_email')) {
            return redirect()->route('install.admin');
        }
        return view('install.step4-settings');
    }

    /**
     * Step 4: Save app settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'app_name'    => 'required|string|max:255',
            'app_url'     => 'required|url',
            'tenant_name' => 'required|string|max:255',
        ]);

        session([
            'install.app_name'    => $request->app_name,
            'install.app_url'     => $request->app_url,
            'install.tenant_name' => $request->tenant_name,
        ]);

        return redirect()->route('install.finalize');
    }

    /**
     * Step 5: Review & run installation
     */
    public function finalize()
    {
        if (! session('install.app_name')) {
            return redirect()->route('install.settings');
        }

        $config = [
            'db_host'        => session('install.db_host'),
            'db_database'    => session('install.db_database'),
            'db_username'    => session('install.db_username'),
            'admin_name'     => session('install.admin_name'),
            'admin_email'    => session('install.admin_email'),
            'app_name'       => session('install.app_name'),
            'app_url'        => session('install.app_url'),
            'tenant_name'    => session('install.tenant_name'),
        ];

        return view('install.step5-finalize', compact('config'));
    }

    /**
     * Step 5: Execute installation
     */
    public function execute()
    {
        try {
            // 1. Write .env file
            $this->writeEnvFile();

            // 2. Clear config cache so new .env is picked up
            Artisan::call('config:clear');

            // 3. Re-configure database connection at runtime
            config([
                'database.connections.mysql.host'     => session('install.db_host'),
                'database.connections.mysql.port'      => session('install.db_port'),
                'database.connections.mysql.database'  => session('install.db_database'),
                'database.connections.mysql.username'   => session('install.db_username'),
                'database.connections.mysql.password'   => session('install.db_password'),
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // 4. Generate APP_KEY
            Artisan::call('key:generate', ['--force' => true]);

            // 5. Run migrations
            Artisan::call('migrate', ['--force' => true]);

            // 6. Create default tenant
            $tenant = Tenant::create([
                'name'      => session('install.tenant_name'),
                'slug'      => \Illuminate\Support\Str::slug(session('install.tenant_name')),
                'is_active' => true,
            ]);

            // 7. Create super admin
            User::create([
                'name'      => session('install.admin_name'),
                'email'     => session('install.admin_email'),
                'password'  => Hash::make(session('install.admin_password')),
                'role'      => 'super_admin',
                'tenant_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // 8. Mark as installed
            file_put_contents(storage_path('installed'), json_encode([
                'installed_at' => now()->toISOString(),
                'version'      => '1.0.0',
            ]));

            // 9. Clear session install data
            session()->forget(array_filter(array_keys(session()->all()), fn($k) => str_starts_with($k, 'install.')));

            return redirect()->route('install.complete');

        } catch (\Exception $e) {
            return back()->withErrors(['install' => 'Lỗi cài đặt: ' . $e->getMessage()]);
        }
    }

    /**
     * Installation complete page
     */
    public function complete()
    {
        return view('install.complete');
    }

    // ─── Helpers ────────────────────────────

    private function checkRequirements(): array
    {
        return [
            ['name' => 'PHP >= 8.1',        'status' => version_compare(PHP_VERSION, '8.1.0', '>=')],
            ['name' => 'PDO Extension',      'status' => extension_loaded('pdo')],
            ['name' => 'PDO MySQL',          'status' => extension_loaded('pdo_mysql')],
            ['name' => 'OpenSSL',            'status' => extension_loaded('openssl')],
            ['name' => 'Mbstring',           'status' => extension_loaded('mbstring')],
            ['name' => 'Tokenizer',          'status' => extension_loaded('tokenizer')],
            ['name' => 'JSON',               'status' => extension_loaded('json')],
            ['name' => 'cURL',               'status' => extension_loaded('curl')],
            ['name' => 'FileInfo',           'status' => extension_loaded('fileinfo')],
            ['name' => 'GD Library',         'status' => extension_loaded('gd')],
            ['name' => 'BCMath',             'status' => extension_loaded('bcmath')],
            ['name' => 'Ctype',              'status' => extension_loaded('ctype')],
            ['name' => 'XML',                'status' => extension_loaded('xml')],
        ];
    }

    private function checkPermissions(): array
    {
        return [
            ['name' => 'storage/',              'status' => is_writable(storage_path())],
            ['name' => 'storage/framework/',    'status' => is_writable(storage_path('framework'))],
            ['name' => 'storage/logs/',         'status' => is_writable(storage_path('logs'))],
            ['name' => 'bootstrap/cache/',      'status' => is_writable(base_path('bootstrap/cache'))],
            ['name' => '.env writable',         'status' => is_writable(base_path()) || is_writable(base_path('.env'))],
        ];
    }

    private function writeEnvFile(): void
    {
        $template = file_get_contents(base_path('.env.example'));

        $replacements = [
            'APP_NAME=TutorCenter'           => 'APP_NAME="' . session('install.app_name') . '"',
            'APP_ENV=production'             => 'APP_ENV=production',
            'APP_KEY='                       => 'APP_KEY=',
            'APP_DEBUG=false'                => 'APP_DEBUG=false',
            'APP_URL=http://localhost'        => 'APP_URL=' . session('install.app_url'),
            'DB_HOST=127.0.0.1'              => 'DB_HOST=' . session('install.db_host'),
            'DB_PORT=3306'                   => 'DB_PORT=' . session('install.db_port'),
            'DB_DATABASE=tutorcenter'        => 'DB_DATABASE=' . session('install.db_database'),
            'DB_USERNAME=root'               => 'DB_USERNAME=' . session('install.db_username'),
            'DB_PASSWORD='                   => 'DB_PASSWORD="' . session('install.db_password') . '"',
        ];

        foreach ($replacements as $search => $replace) {
            $template = str_replace($search, $replace, $template);
        }

        file_put_contents(base_path('.env'), $template);
    }
}
