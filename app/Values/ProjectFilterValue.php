<?php

declare(strict_types=1);

namespace App\Values;

final readonly class ProjectFilterValue
{
    /**
     * @param string|null $search
     * @param string|null $status
     * @param string|null $priority
     * @param string $sortBy
     * @param 'asc'|'desc' $sortDir
     */
    public function __construct(
        public ?string $search = null,
        public ?string $status = null,
        public ?string $priority = null,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
    ) {
        //
    }
}
