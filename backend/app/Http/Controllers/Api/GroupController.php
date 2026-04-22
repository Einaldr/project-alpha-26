<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
        ]);

        $per_page = $request->integer('per_page', 15);

        $query = Group::VisibleTo(Auth::id());

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
        $data = $request->safe()->except('icon');

        $group = Group::create($data);

        $fileName = 'icon.webp';

        $path = "groups/{$group->id}/{$fileName}";

        if ($request->hasFile('icon')) {
            $image = ImageManager::usingDriver(Driver::class)->decode($request->file('icon'))
                                                             ->cover(400, 400);
            $encoded = $image->encodeUsingFileExtension(FileExtension::WEBP);

            Storage::disk('public')->put($path, $encoded);
        } else {
            $group->generateDefaultIcon();
        }

        $group->update(['icon_path' => $path]);

        return new GroupResource($group);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group): GroupResource
    {
        return new GroupResource($group->load('parent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group): GroupResource
    {
        Gate::authorize('update', $group);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:5', 'max:255']
        ]);

        $group->update(['name' => $validated->name]);

        return new GroupResource($group);
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
}
