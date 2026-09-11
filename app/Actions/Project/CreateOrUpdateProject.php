<?php

declare(strict_types=1);

namespace App\Actions\Project;

use App\Models\Project;
use App\Values\ProjectValue;

final readonly class CreateOrUpdateProject
{
    public function handle(ProjectValue $value, Project $project = new Project): Project
    {
        $project->client_name = $value->clientName;
        $project->project_name = $value->projectName;
        $project->description = $value->description;
        $project->status = $value->status;
        $project->priority = $value->priority;
        $project->start_date = $value->startDate;
        $project->due_date = $value->dueDate;

        $project->save();

        return $project->refresh();
    }
}
