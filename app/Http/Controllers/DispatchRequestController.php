<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DispatchRequest;
use App\Models\StaffAssignment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchRequestController extends Controller
{
    /** Requests created by the current manager */
    public function index()
    {
        $requests = DispatchRequest::with(['user', 'targetTenant', 'targetBranch', 'reviewer'])
            ->where('requester_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dispatch-requests.index', compact('requests'));
    }

    /** Form to create a new dispatch request */
    public function create()
    {
        // Managers can only dispatch from their own tenant's staff
        $user = Auth::user();

        $staff = User::withoutTenantScope()
            ->where('tenant_id', $user->tenant_id)
            ->where('id', '!=', $user->id)
            ->whereNotIn('role', ['super_admin'])
            ->orderBy('name')
            ->get();

        // All tenants and branches (to dispatch TO)
        $tenants  = Tenant::withoutTenantScope()->where('status', 'active')->orderBy('name')->get();
        $branches = Branch::withoutTenantScope()->orderBy('name')->get();

        return view('dispatch-requests.create', compact('staff', 'tenants', 'branches'));
    }

    /** Store dispatch request or auto-approve if same tenant */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'target_tenant_id' => 'required|exists:tenants,id',
            'target_branch_id' => 'nullable|exists:branches,id',
            'role_override'    => 'nullable|string|max:50',
            'note'             => 'nullable|string|max:1000',
        ]);

        $requester = Auth::user();
        $targetUser = User::findOrFail($data['user_id']);

        // Same-tenant: manager can self-approve
        if ((int)$data['target_tenant_id'] === $requester->tenant_id) {
            $this->createAssignment($targetUser, $data, $requester->id, $requester->id);

            return redirect()->route('dispatch-requests.index')
                ->with('success', "✅ Đã điều phối {$targetUser->name} vào chi nhánh cùng trung tâm.");
        }

        // Cross-tenant: create pending request for Super Admin
        DispatchRequest::create([
            'requester_id'     => $requester->id,
            'user_id'          => $data['user_id'],
            'target_tenant_id' => $data['target_tenant_id'],
            'target_branch_id' => $data['target_branch_id'] ?? null,
            'role_override'    => $data['role_override'] ?? null,
            'note'             => $data['note'] ?? null,
            'status'           => 'pending',
        ]);

        return redirect()->route('dispatch-requests.index')
            ->with('success', "📨 Yêu cầu điều phối {$targetUser->name} đã gửi. Chờ Super Admin phê duyệt.");
    }

    /** Cancel a pending request */
    public function cancel(DispatchRequest $dispatchRequest)
    {
        abort_if($dispatchRequest->requester_id !== Auth::id(), 403);
        abort_if(!$dispatchRequest->isPending(), 400, 'Chỉ có thể hủy yêu cầu đang chờ duyệt.');

        $dispatchRequest->update(['status' => 'cancelled']);

        return back()->with('success', '⊘ Đã hủy yêu cầu điều phối.');
    }

    /** Static helper: create a StaffAssignment from dispatch data */
    public static function createAssignment(User $user, array $data, int $assignedBy, int $approvedBy): StaffAssignment
    {
        return StaffAssignment::updateOrCreate(
            [
                'user_id'   => $user->id,
                'tenant_id' => $data['target_tenant_id'],
                'branch_id' => $data['target_branch_id'] ?? null,
            ],
            [
                'role_override' => $data['role_override'] ?? null,
                'status'        => 'active',
                'assigned_by'   => $assignedBy,
                'note'          => $data['note'] ?? null,
            ]
        );
    }
}
