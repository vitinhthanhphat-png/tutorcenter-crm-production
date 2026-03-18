<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\DispatchRequest;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\StaffAssignment;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ──────────────────────────────────────────────
    // SYSTEM OVERVIEW
    // ──────────────────────────────────────────────
    public function index()
    {
        $stats = [
            'tenants'     => Tenant::count(),
            'users'       => User::whereNotNull('tenant_id')->count(),
            'students'    => Student::count(),
            'classrooms'  => ClassRoom::count(),
            'enrollments' => Enrollment::count(),
            'revenue'     => Invoice::sum('amount'),
            'sessions'    => ClassSession::count(),
            'branches'    => Branch::count(),
        ];

        // Monthly revenue — last 6 months
        $monthlyRevenue = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
                'label' => $month->format('M Y'),
                'value' => Invoice::whereYear('transaction_date', $month->year)
                                  ->whereMonth('transaction_date', $month->month)
                                  ->sum('amount'),
            ];
        });

        // Per-tenant breakdown
        $tenants = Tenant::withCount(['users', 'students', 'classrooms', 'branches'])
            ->get()->map(function ($t) {
                $t->revenue = Invoice::where('tenant_id', $t->id)->sum('amount');
                return $t;
            });

        // Recent activity (last 10 users created)
        $recentUsers = User::with('tenant')->latest()->take(5)->get();

        return view('admin.index', compact('stats', 'tenants', 'monthlyRevenue', 'recentUsers'));
    }

    // ──────────────────────────────────────────────
    // TENANTS
    // ──────────────────────────────────────────────
    public function tenants()
    {
        $tenants = Tenant::withCount(['users', 'students', 'classrooms', 'branches'])->latest()->get();
        return view('admin.tenants', compact('tenants'));
    }

    public function tenantShow(Tenant $tenant)
    {
        $tenant->load(['branches', 'users']);
        $classes  = ClassRoom::where('tenant_id', $tenant->id)->with(['course', 'teacher'])->get();
        $students = Student::where('tenant_id', $tenant->id)->latest()->take(20)->get();
        $revenue  = Invoice::where('tenant_id', $tenant->id)->sum('amount');
        $debt     = Enrollment::where('tenant_id', $tenant->id)
                               ->selectRaw('SUM(final_price - paid_amount) as total')->value('total') ?? 0;
        $stats = [
            'branches'    => $tenant->branches->count(),
            'users'       => $tenant->users->count(),
            'classes'     => $classes->count(),
            'students'    => Student::where('tenant_id', $tenant->id)->count(),
            'enrollments' => Enrollment::where('tenant_id', $tenant->id)->count(),
            'revenue'     => $revenue,
            'debt'        => $debt,
        ];
        return view('admin.tenant-show', compact('tenant', 'classes', 'students', 'stats'));
    }

    public function tenantCreate()
    {
        return view('admin.tenant-form', ['tenant' => null]);
    }

    public function tenantStore(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'domain'  => 'required|string|max:100|unique:tenants,domain',
            'status'  => 'in:active,inactive,suspended',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);
        Tenant::create($data);
        return redirect()->route('admin.tenants')->with('success', "✅ Đã tạo tenant '{$data['name']}' thành công.");
    }

    public function tenantEdit(Tenant $tenant)
    {
        return view('admin.tenant-form', compact('tenant'));
    }

    public function tenantUpdate(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:150',
            'domain'  => "required|string|max:100|unique:tenants,domain,{$tenant->id}",
            'status'  => 'in:active,inactive,suspended',
            'phone'   => 'nullable|string|max:30',
            'email'   => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);
        $tenant->update($data);
        return redirect()->route('admin.tenants')->with('success', "✅ Đã cập nhật tenant '{$tenant->name}'.");
    }

    public function tenantToggle(Tenant $tenant)
    {
        $tenant->update(['status' => $tenant->status === 'active' ? 'suspended' : 'active']);
        $msg = $tenant->status === 'active' ? "Đã kích hoạt tenant." : "Đã tạm khóa tenant.";
        return back()->with('success', $msg);
    }

    public function tenantDestroy(Tenant $tenant)
    {
        $tenant->delete();
        return back()->with('success', "Đã xóa tenant.");
    }

    // ──────────────────────────────────────────────
    // BRANCHES (cross-tenant)
    // ──────────────────────────────────────────────
    public function branches(Request $request)
    {
        $query = Branch::with('tenant')->latest();
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%");
        }
        $branches = $query->paginate(25)->appends(request()->query());

        $tenants  = Tenant::orderBy('name')->get();
        return view('admin.branches', compact('branches', 'tenants'));
    }

    public function branchCreate()
    {
        $tenants = Tenant::orderBy('name')->get();
        return view('admin.branch-form', ['branch' => null, 'tenants' => $tenants]);
    }

    public function branchStore(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name'      => 'required|string|max:120',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:30',
        ]);
        Branch::create($data);
        return redirect()->route('admin.branches')->with('success', "Đã tạo chi nhánh.");
    }

    public function branchEdit(Branch $branch)
    {
        $tenants = Tenant::orderBy('name')->get();
        return view('admin.branch-form', compact('branch', 'tenants'));
    }

    public function branchUpdate(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name'      => 'required|string|max:120',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:30',
        ]);
        $branch->update($data);
        return redirect()->route('admin.branches')->with('success', "Đã cập nhật chi nhánh.");
    }

    public function branchDestroy(Branch $branch)
    {
        $branch->delete();
        return back()->with('success', "Đã xóa chi nhánh.");
    }

    // ──────────────────────────────────────────────
    // USERS (xuyên tenant)
    // ──────────────────────────────────────────────
    public function users(Request $request)
    {
        $query = User::with('tenant')->latest();
        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('email', 'like', "%{$request->q}%");
            });
        }
        $users   = $query->paginate(25)->appends(request()->query());

        $tenants = Tenant::orderBy('name')->get();
        $roles   = ['super_admin', 'center_manager', 'accountant', 'teacher'];
        return view('admin.users', compact('users', 'tenants', 'roles'));
    }

    public function userCreate()
    {
        $tenants = Tenant::orderBy('name')->get();
        $roles   = ['super_admin', 'center_manager', 'accountant', 'teacher'];
        return view('admin.user-form', ['user' => null, 'tenants' => $tenants, 'roles' => $roles]);
    }

    public function userStore(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6',
            'role'      => 'required|in:super_admin,center_manager,accountant,teacher',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        User::create($data);
        return redirect()->route('admin.users')->with('success', "Đã tạo user {$data['email']}.");
    }

    public function userEdit(User $user)
    {
        $tenants     = Tenant::orderBy('name')->get();
        $branches    = Branch::orderBy('name')->get();
        $roles       = ['super_admin', 'center_manager', 'branch_manager', 'accountant', 'operations', 'teacher', 'tutor'];
        $assignments = $user->assignments()->with(['tenant', 'branch'])->get();
        return view('admin.user-form', compact('user', 'tenants', 'branches', 'roles', 'assignments'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => "required|email|unique:users,email,{$user->id}",
            'role'      => 'required|in:super_admin,center_manager,accountant,teacher',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.users')->with('success', "Đã cập nhật user.");
    }

    public function userToggle(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể thay đổi trạng thái của chính mình.');
        }
        $user->update(['is_active' => !$user->is_active]);
        $msg = $user->is_active ? "Đã kích hoạt {$user->email}." : "Đã vô hiệu hoá {$user->email}.";
        return back()->with('success', $msg);
    }

    public function userResetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        return back()->with('success', "Đã đặt lại mật khẩu cho {$user->email}.");
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa chính mình.');
        }
        $user->delete();
        return back()->with('success', "Đã xóa user.");
    }

    // ──────────────────────────────────────────────
    // PER-USER ASSIGNMENT MANAGEMENT (from user edit form)
    // ──────────────────────────────────────────────
    public function userAssignmentAdd(Request $request, User $user)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'branch_id' => 'nullable|exists:branches,id',
            'note'      => 'nullable|string|max:255',
        ]);

        if ((int)$data['tenant_id'] === $user->tenant_id && empty($data['branch_id'])) {
            return back()->with('error', 'Đây là Tenant chính của user, không cần tạo thêm.');
        }

        StaffAssignment::updateOrCreate(
            [
                'user_id'   => $user->id,
                'tenant_id' => $data['tenant_id'],
                'branch_id' => $data['branch_id'] ?? null,
            ],
            [
                'status'      => 'active',
                'assigned_by' => Auth::id(),
                'note'        => $data['note'] ?? null,
            ]
        );

        return back()->with('success', '✅ Đã thêm phân công đa trung tâm.');
    }

    public function userAssignmentRemove(User $user, StaffAssignment $assignment)
    {
        abort_if($assignment->user_id !== $user->id, 403, 'Invalid assignment.');
        $assignment->delete();
        return back()->with('success', 'Đã xóa phân công.');
    }

    // ──────────────────────────────────────────────
    // SYSTEM SETTINGS
    // ──────────────────────────────────────────────
    public function settings()
    {
        $info = [
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment'     => app()->environment(),
            'debug'           => config('app.debug'),
            'app_name'        => config('app.name'),
            'timezone'        => config('app.timezone'),
            'db_connection'   => config('database.default'),
            'db_name'         => config('database.connections.' . config('database.default') . '.database'),
            'cache_driver'    => config('cache.default'),
            'queue_driver'    => config('queue.default'),
            'mail_mailer'     => config('mail.default'),
        ];
        return view('admin.settings', compact('info'));
    }

    // ──────────────────────────────────────────────
    // DISPATCH REQUESTS (Cross-Tenant Approvals)
    // ──────────────────────────────────────────────
    public function dispatchRequests()
    {
        $pending = DispatchRequest::with(['requester', 'user', 'targetTenant', 'targetBranch'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $history = DispatchRequest::with(['requester', 'user', 'targetTenant', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->orderByDesc('reviewed_at')
            ->limit(50)
            ->get();

        return view('admin.dispatch-requests', compact('pending', 'history'));
    }

    public function approveRequest(DispatchRequest $dispatchRequest)
    {
        abort_if(!$dispatchRequest->isPending(), 400, 'Yêu cầu không ở trạng thái chờ duyệt.');

        \App\Http\Controllers\DispatchRequestController::createAssignment(
            $dispatchRequest->user,
            [
                'target_tenant_id' => $dispatchRequest->target_tenant_id,
                'target_branch_id' => $dispatchRequest->target_branch_id,
                'role_override'    => $dispatchRequest->role_override,
                'note'             => "Approved via dispatch request #{$dispatchRequest->id}",
            ],
            $dispatchRequest->requester_id,
            Auth::id()
        );

        $dispatchRequest->update([
            'status'      => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => request('review_note'),
        ]);

        return back()->with('success', "✅ Đã phê duyệt điều phối {$dispatchRequest->user->name}.");
    }

    public function rejectRequest(DispatchRequest $dispatchRequest)
    {
        abort_if(!$dispatchRequest->isPending(), 400, 'Yêu cầu không ở trạng thái chờ duyệt.');

        $dispatchRequest->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'review_note' => request('review_note') ?? 'Không phê duyệt.',
        ]);

        return back()->with('success', "❌ Đã từ chối yêu cầu điều phối.");
    }

    // ──────────────────────────────────────────────
    // STAFF ASSIGNMENTS
    // ──────────────────────────────────────────────
    public function assignments()
    {
        $assignments = StaffAssignment::with(['user', 'tenant', 'branch', 'assignedBy'])
            ->orderByDesc('created_at')
            ->paginate(30);

        return view('admin.assignments', compact('assignments'));
    }

    public function revokeAssignment(StaffAssignment $assignment)
    {
        $name = $assignment->user->name ?? 'nhân viên';
        $assignment->delete();
        return back()->with('success', "Đã thu hồi quyền truy cập của {$name}.");
    }
}
