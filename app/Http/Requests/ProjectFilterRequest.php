<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Values\ProjectFilterValue;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectFilterRequest extends FormRequest
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
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::enum(ProjectStatus::class)],
            'priority' => ['nullable', Rule::enum(ProjectPriority::class)],
            'sort_by' => ['nullable', 'string', Rule::in(['created_at', 'project_name', 'client_name', 'due_date'])],
            'sort_dir' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    public function toValue(): ProjectFilterValue
    {
        /** @var 'asc'|'desc' $sortDir */
        $sortDir = $this->string('sort_dir')->value() ?: 'desc';

        return new ProjectFilterValue(
            search: $this->string('search')->trim()->value() ?: null,
            status: $this->string('status')->value() ?: null,
            priority: $this->string('priority')->value() ?: null,
            sortBy: $this->string('sort_by')->value() ?: 'created_at',
            sortDir: $sortDir,
        );
    }
}
