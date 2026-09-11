<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Models\Project;
use App\Values\ProjectFilterValue;
use Illuminate\Http\Resources\Json\ResourceCollection;

final readonly class ListProjects
{
    /**
     * @throws \Throwable
     */
    public function handle(ProjectFilterValue $filters): ResourceCollection
    {
        return Project::query()
            ->when($filters->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('project_name', 'like', "%{$search}%")
                        ->orWhere('client_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->orderBy($filters->sortBy, $filters->sortDir)
            ->paginate()
            ->toResourceCollection();
    }
}
