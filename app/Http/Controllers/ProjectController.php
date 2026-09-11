<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Project\CreateOrUpdateProject;
use App\Actions\Project\ListProjects;
use App\Http\Requests\ProjectFilterRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Throwable
     */
    public function index(ListProjects $action, ProjectFilterRequest $request): ResourceCollection
    {
        return $action->handle($request->toValue());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrUpdateProject $action, StoreProjectRequest $request): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Project created successfully',
            'data' => $action->handle($request->toValue()),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): JsonResource
    {
        return $project->toResource();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        CreateOrUpdateProject $action,
        UpdateProjectRequest $request,
        Project $project
    ): JsonResponse {
        return new JsonResponse([
            'message' => 'Project updated successfully',
            'data' => $action->handle($request->toValue(), $project),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return new JsonResponse([
            'message' => 'Project deleted successfully',
        ]);
    }
}
