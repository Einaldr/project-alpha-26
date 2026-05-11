<?php

namespace App\Http\Controllers\Api;

use App\Enum\RolePermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupMemberRequest;
use App\Http\Resources\GroupMemberResource;
use App\Mail\GroupInvitationMail;
use App\Models\Group;
use App\Models\GroupInvitation;
use App\Models\GroupMember;
use App\Models\GroupRole;
use App\Models\User;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

/**
 * GroupMemberController
 * 
 * Manages the lifecycle of group members.
 * Handles displaying group members, their details as well as invite flow.
 */
class GroupMemberController extends Controller
{
    use HandlesStealthAuth;

    /**
     * Display a paginated list of group members of a given group.
     * 
     * Like groupcontroller's index method, it implements high-performance
     * fuzzy search using PostgreSQL's pg_trgm extension.
     * 
     * @param Group $group
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Group $group, Request $request): ResourceCollection
    {
        $this->authrizeStealth($group, 'anyView', "You don't have access to the group members.");

        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
        ]);

        $per_page = $request->integer('per_page', 15);

        $query = $group->members()->with(['user', 'roles']);

        if ($search = $request->search) {
            $query->join('users', 'group_members.user_id', '=', 'users.id')
                  ->whereRaw('name % ?', [$search])
                  ->orderByRaw('similarity(name, ?) DESC', [$search])
                  ->select('group_members.*');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return GroupMemberResource::collection($query->paginate($per_page)->appends($request->query()));
    }

    /**
     * Display individual group member information like their roles.
     * 
     * @param Group $group
     * @param GroupMember $groupMember
     * @param Request $request
     * @return GroupMemberResource
     */
    public function show(Group $group, GroupMember $groupMember, Request $request): GroupMemberResource
    {
        $this->authrizeStealth($group, 'view', "You don't have access to the group member.");

        return new GroupMemberResource($groupMember);
    }

    /**
     * Invite a new user to the group or team.
     * 
     * @param StoreGroupMemberRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function invite(StoreGroupMemberRequest $request, Group $group): JsonResponse
    {
        $this->authrizeStealth($group, 'invite', "You don't have permission to invite members.");

        $email = $request->email;
        $roleIds = $request->roles ?? [];

        if (empty($roleIds)) {
            $defaultRole = $group->roles()->where('name', 'Member')->first();
            $roleIds = [$defaultRole->id];
        }

        $invitation = GroupInvitation::create([
            'group_id' => $group->id,
            'email' => $request->email,
            'role_ids' => $request->roles ?? [$group->roles()->where('name', 'Member')->first()->id],
        ]);

        $expiration = now()->addDays(3);

        $url = URL::temporarySignedRoute('invitations.accept', $expiration, ['invitation'=>$invitation->id]);

        $queryString = parse_url($url, PHP_URL_QUERY);

        $frontendUrl = rtrim((string) config('settings.frontend_url'), '/') . '/invites/accept/' . $invitation->id . '?' . $queryString;

        Mail::to($request->email)->send(new GroupInvitationMail($group, $frontendUrl, $expiration));

        return response()->json(['message' => 'Invitation sent!']);
    }

    /**
     * Accept an invite to a group or team.
     * 
     * @param Request $request
     * @param GroupInvitation $invitation
     * @return JsonResponse
     */
    public function acceptInvite(Request $request, GroupInvitation $invitation): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'The link is tampered with or expired.');
        }

        if ($request->user()->email !== $invitation->email) {
            abort(403, 'This invitation was intended for a different user.');
        }

        $member = GroupMember::create([
            'user_id' => User::where('email', '=', $invitation->email)->id,
            'group_id' => $invitation->group->id,
        ]);

        $member->roles()->attach($invitation->role_ids);

        $invitation->delete();

        return response()->json(['message' => 'Invitation accepted!']);
    }

    /**
     * Kick a user out of a group or team.
     * 
     * It also blocks the request if the given user to kick is an Owner.
     * 
     * @param Group $group
     * @param GroupMember $member
     * @return JsonResponse
     */
    public function kickMember(Group $group, GroupMember $member): JsonResponse
    {
        $this->authrizeStealth($group, 'kick', "You don't have permissions to kick members.");

        if ($member->user_id === $group->owner_id) {
            abort(403, "The group owner cannot be kicked.");
        }

        $userName = $member->user->name;
        $member->delete();

        return response()->json(['message' => $userName . ' has been kicked.']);
    }
}
