<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\FileExtension;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

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
    public function store(StoreGroupRequest $request)
    {
        $data = $request->safe()->except('icon');

        $group = Group::create($data);

        if ($request->hasFile('icon')) {
            $fileName = 'icon.webp';
            $path = "groups/{$group->id}/{$fileName}";

            $image = ImageManager::usingDriver(Driver::class)->decode($request->file('icon'))
                                                             ->cover(400, 400);

            $encoded = $image->encodeUsingFileExtension(FileExtension::WEBP);

            Storage::disk('public')->put($path, $encoded);

            $group->update(['icon_path' => $path]);
        }

        return response()->json($group, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        //
    }
}
