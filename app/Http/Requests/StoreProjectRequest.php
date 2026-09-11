<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Values\ProjectValue;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
            'client_name' => ['required', 'string'],
            'project_name' => ['required', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'priority' => ['required', Rule::enum(ProjectPriority::class)],
            'start_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function toValue(): ProjectValue
    {
        /**
         * @var array{
         *   client_name: string,
         *   project_name: string,
         *   description?: string|null,
         *   status: string,
         *   priority: string,
         *   start_date: string,
         *   due_date: string
         * } $validated
         */
        $validated = $this->validated();

        return new ProjectValue(
            clientName: $validated['client_name'],
            projectName: $validated['project_name'],
            description: $validated['description'] ?? null,
            status: ProjectStatus::from($validated['status']),
            priority: ProjectPriority::from($validated['priority']),
            startDate: Carbon::parse($validated['start_date']),
            dueDate: Carbon::parse($validated['due_date']),
        );
    }
}
