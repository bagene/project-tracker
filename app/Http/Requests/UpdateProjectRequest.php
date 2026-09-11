<?php

namespace App\Http\Requests;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Values\ProjectValue;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_name' => ['sometimes', 'string'],
            'project_name' => ['sometimes', 'string'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', Rule::enum(ProjectStatus::class)],
            'priority' => ['sometimes', Rule::enum(ProjectPriority::class)],
            'start_date' => ['sometimes', 'date'],
            'due_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function toValue(): ProjectValue
    {
        /**
         * @var array{
         *   client_name?: string,
         *   project_name?: string,
         *   description?: string|null,
         *   status?: string,
         *   priority?: string,
         *   start_date?: string,
         *   due_date?: string
         * } $validated
         */
        $validated = $this->validated();
        /** @var Project $project */
        $project = $this->route('project');

        return new ProjectValue(
            clientName: $validated['client_name'] ?? $project->client_name,
            projectName: $validated['project_name'] ?? $project->project_name,
            description: $validated['description'] ?? $project->description,
            status: isset($validated['status']) ? ProjectStatus::from($validated['status']) : $project->status,
            priority: isset($validated['priority']) ? ProjectPriority::from($validated['priority']) : $project->priority,
            startDate: isset($validated['start_date']) ? Carbon::parse($validated['start_date']) : $project->start_date,
            dueDate: isset($validated['due_date']) ? Carbon::parse($validated['due_date']) : $project->due_date,

        );
    }
}
