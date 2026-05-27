<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuditLogResource;
use App\Models\Group;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuditLogController extends Controller
{
    use HandlesStealthAuth;

    /**
     * Display a paginated list of audit logs for the group.
     */
    public function index(Request $request, Group $group): AnonymousResourceCollection
    {
        $this->authorizeStealth($group, 'viewLogs', 'You do not have permission to view audit logs.');

        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $request->integer('per_page', 50);

        $logs = $group->auditLogs()
            ->with('user')
            ->latest()
            ->paginate($perPage);

        return AuditLogResource::collection($logs);
    }
}
