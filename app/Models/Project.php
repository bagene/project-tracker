<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Http\Resources\ProjectResource;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property string $client_name
 * @property string $project_name
 * @property string|null $description
 * @property ProjectStatus $status
 * @property ProjectPriority $priority
 * @property Carbon $start_date
 * @property Carbon $due_date
 * @property-read Carbon $created_at
 * @property-read Carbon $updated_at
 */
#[UseResource(ProjectResource::class)]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * @return array<string, string|\UnitEnum>
     */
    public function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'priority' => ProjectPriority::class,
            'start_date' => 'datetime',
            'due_date' => 'datetime',
        ];
    }
}
