<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->successResponse(
            GroupResource::collection(Group::all()),
            "groups list",
            200);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(GroupRequest $request)
    {
        $group = Group::create([
            'owner' => $request->user()->id,
            'period_id' => $request->period_id,
            'name' => $request->name,
            'description' => $request->description,
            'active' => true,
        ]);
        return $this->successResponse(
            new GroupResource($group),
            "group created",
            201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return $this->successResponse(GroupResource::make($group),
            "group details",
            200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(GroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        return $this->successResponse(GroupResource::make($group),
            "group updated",
            200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        return $this->successResponse(null,
            "group deleted",
            200);
    }
}
