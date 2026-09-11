<?php

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can list projects', function () {
    Project::factory()->count(3)->create();

    $response = $this->getJson('/api/projects');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'client_name',
                    'project_name',
                    'description',
                    'status',
                    'priority',
                    'start_date',
                    'due_date',
                ],
            ],
            'links',
            'meta',
        ]);
});

test('it can create a project', function () {
    $data = [
        'client_name' => 'Acme Corp',
        'project_name' => 'Secret Project',
        'description' => 'A very secret project.',
        'status' => ProjectStatus::PLANNING->value,
        'priority' => ProjectPriority::HIGH->value,
        'start_date' => now()->toDateString(),
        'due_date' => now()->addMonth()->toDateString(),
    ];

    $response = $this->postJson('/api/projects', $data);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Project created successfully',
            'data' => [
                'client_name' => 'Acme Corp',
                'project_name' => 'Secret Project',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'client_name' => 'Acme Corp',
        'project_name' => 'Secret Project',
    ]);
});

test('it can show a project', function () {
    $project = Project::factory()->create();

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $project->id,
                'client_name' => $project->client_name,
            ],
        ]);
});

test('it can update a project', function () {
    $project = Project::factory()->create([
        'client_name' => 'Old Client',
    ]);

    $data = [
        'client_name' => 'New Client',
    ];

    $response = $this->putJson("/api/projects/{$project->id}", $data);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Project updated successfully',
            'data' => [
                'client_name' => 'New Client',
            ],
        ]);

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'client_name' => 'New Client',
    ]);
});

test('it can delete a project', function () {
    $project = Project::factory()->create();

    $response = $this->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Project deleted successfully',
        ]);

    $this->assertDatabaseMissing('projects', [
        'id' => $project->id,
    ]);
});

test('it validates project creation', function (array $data, array $errors) {
    $response = $this->postJson('/api/projects', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors($errors);
})->with([
    'empty data' => [[], ['client_name', 'project_name', 'status', 'priority', 'start_date', 'due_date']],
    'invalid dates' => [
        [
            'client_name' => 'Acme',
            'project_name' => 'Project',
            'status' => ProjectStatus::PLANNING->value,
            'priority' => ProjectPriority::HIGH->value,
            'start_date' => '2026-09-11',
            'due_date' => '2026-09-10',
        ],
        ['due_date'],
    ],
    'invalid status' => [
        [
            'client_name' => 'Acme',
            'project_name' => 'Project',
            'status' => 'invalid-status',
            'priority' => ProjectPriority::HIGH->value,
            'start_date' => '2026-09-11',
            'due_date' => '2026-09-12',
        ],
        ['status'],
    ],
    'invalid priority' => [
        [
            'client_name' => 'Acme',
            'project_name' => 'Project',
            'status' => ProjectStatus::PLANNING->value,
            'priority' => 'invalid-priority',
            'start_date' => '2026-09-11',
            'due_date' => '2026-09-12',
        ],
        ['priority'],
    ],
]);

test('it returns 404 for non-existent project', function () {
    $this->getJson('/api/projects/999')->assertStatus(404);
    $this->putJson('/api/projects/999', ['client_name' => 'New'])->assertStatus(404);
    $this->deleteJson('/api/projects/999')->assertStatus(404);
});

test('it can search and filter projects', function () {
    Project::factory()->create([
        'project_name' => 'Searchable Project',
        'status' => ProjectStatus::COMPLETED->value,
        'priority' => ProjectPriority::HIGH->value,
    ]);
    Project::factory()->create([
        'project_name' => 'Other Project',
        'status' => ProjectStatus::PLANNING->value,
        'priority' => ProjectPriority::LOW->value,
    ]);

    // Search
    $response = $this->getJson('/api/projects?search=Searchable');
    $response->assertStatus(200)->assertJsonCount(1, 'data');

    // Filter by status
    $response = $this->getJson('/api/projects?status='.ProjectStatus::COMPLETED->value);
    $response->assertStatus(200)->assertJsonCount(1, 'data');

    // Filter by priority
    $response = $this->getJson('/api/projects?priority='.ProjectPriority::LOW->value);
    $response->assertStatus(200)->assertJsonCount(1, 'data');

    // Sort
    $response = $this->getJson('/api/projects?sort_by=project_name&sort_dir=desc');
    $response->assertStatus(200);
    $this->assertEquals('Searchable Project', $response->json('data.0.project_name'));
});
