<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
        ]);

        $per_page = $request->integer('per_page', 15);

        $query = Group::public(Auth::id());

        if ($search = $request->search) {
            $query->whereRaw('name % ?', [$search])
                  ->orderByRaw('similarity(name, ?) DESC', [$search]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return GroupResource::collection(
            $query->paginate($per_page)->appends($request->query())
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGroupRequest $request): GroupResource
    {
        if ($request->filled('parent_id')) {
            $parent = Group::findOrFail($request->parent_id);
            Gate::authorize('createTeam', $parent);
        }

        $data = $request->validated();
        $data['owner_id'] = $request->user()->id;

        $group = Group::create($data);

        if ($request->hasFile('icon')) {
            $group->saveCustomIcon($request->file('icon'));
        } else {
            $group->generateDefaultIcon();
        }

        return new GroupResource($group);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): GroupResource
    {
        if (!Gate::allows('view', $group)) {
            abort(404);
        }
        return new GroupResource($group->load('parent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGroupRequest $request, Group $group): GroupResource
    {
        Gate::authorize('update', $group);

        $group->update($request->validated());

        if ($request->hasFile('icon')) {
            $group->saveCustomIcon($request->file('icon'));
        }

        return new GroupResource($group->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group): JsonResponse
    {
        Gate::authorize('delete', $group);

        $group->delete();

        return response()->json(['message' => 'Successfully deleted'], 200);
    }

    public function myGroups(Request $request): ResourceCollection
    {
        $groups = Group::memberOf($request->user())
                       ->with('parent')
                       ->latest()
                       ->get();
        
        return GroupResource::collection($groups);
    }
}
