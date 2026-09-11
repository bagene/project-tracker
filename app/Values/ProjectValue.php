<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Illuminate\Support\Carbon;

final readonly class ProjectValue
{
    public function __construct(
        public string $clientName,
        public string $projectName,
        public ?string $description,
        public ProjectStatus $status,
        public ProjectPriority $priority,
        public Carbon $startDate,
        public Carbon $dueDate
    ) {
        //
    }
}
