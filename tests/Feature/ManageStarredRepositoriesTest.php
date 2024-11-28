<?php

namespace Tests\Feature;

use App\Models\StarredRepository;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ManageStarredRepositoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_and_saves_starred_repositories()
    {
        $mockResponse = [
            [
                'id' => 123,
                'name' => 'repo-1',
                'description' => 'Test description',
                'html_url' => 'https://github.com/user/repo-1',
                'language' => 'PHP',
            ],
        ];

        Http::fake([
            'https://api.github.com/users/*/starred*' => Http::response($mockResponse, 200, [
                'X-RateLimit-Remaining' => 5000,
                'X-RateLimit-Limit' => 5000,
                'X-RateLimit-Reset' => time() + 3600,
            ]),
        ]);

        $response = $this->postJson('/api/test-user/starred-repositories', ['username' => 'test-user']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Repositories fetched and saved successfully']);

        $this->assertDatabaseHas('starred_repositories', [
            'username' => 'test-user',
            'repository_id' => 123,
            'name' => 'repo-1',
        ]);
    }

    /** @test */
    public function it_adds_tags_to_a_repository()
    {
        $repository = StarredRepository::factory()->create([
            'username' => 'test-user',
            'repository_id' => 123,
            'name' => 'repo-1',
        ]);

        $response = $this->postJson("/api/test-user/starred-repositories/123/tags", [
            'username' => 'test-user',
            'repositoryId' => 123,
            'tags' => ['Laravel', 'PHP'],
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Tags added successfully']);

        $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
        $this->assertDatabaseHas('tags', ['name' => 'PHP']);

        $this->assertDatabaseHas('repository_tag', [
            'starred_repository_id' => $repository->id,
            'tag_id' => Tag::where('name', 'Laravel')->first()->id,
        ]);
    }

    /** @test */
    public function it_filters_repositories_by_tag()
    {
        $repository1 = StarredRepository::factory()->create([
            'username' => 'test-user',
            'repository_id' => 123,
            'name' => 'repo-1',
        ]);
        $repository2 = StarredRepository::factory()->create([
            'username' => 'test-user',
            'repository_id' => 321,
            'name' => 'repo-2',
            ]);

        $repository1->tags()->create(['name' => 'Laravel']);
        $repository2->tags()->create(['name' => 'PHP']);

        $response = $this->getJson('/api/test-user/starred-repositories?username=testuser&tag=Laravel');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'repo-1'])
            ->assertJsonMissing(['name' => 'repo-2']);
    }
}

