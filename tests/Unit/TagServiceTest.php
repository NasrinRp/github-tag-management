<?php

namespace Tests\Unit;

use App\Models\Tag;
use App\Models\StarredRepository;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TagService $tagService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagService = new TagService();
    }

    /** @test */
    public function it_adds_new_tags_to_a_repository()
    {
        $repository = StarredRepository::factory()->create();
        $tags = ['laravel', 'php'];

        $this->tagService->addTagsToRepository($tags, $repository);

        $this->assertCount(2, $repository->tags);
        $this->assertTrue($repository->tags->contains('name', 'laravel'));
        $this->assertTrue($repository->tags->contains('name', 'php'));
    }

    /** @test */
    public function it_does_not_add_duplicate_tags_to_a_repository()
    {
        $repository = StarredRepository::factory()->create();
        $tag = Tag::create(['name' => 'laravel']);
        $repository->tags()->attach($tag);

        $this->tagService->addTagsToRepository(['laravel'], $repository);

        $this->assertCount(1, $repository->tags);
    }

    /** @test */
    public function it_adds_only_new_tags_to_a_repository()
    {
        $repository = StarredRepository::factory()->create();
        $tags = ['laravel', 'php'];

        $this->tagService->addTagsToRepository($tags, $repository);
        $this->tagService->addTagsToRepository(['php', 'vue'], $repository);

        $this->assertCount(3, $repository->tags);
        $this->assertTrue($repository->tags->contains('name', 'vue'));
    }
}

