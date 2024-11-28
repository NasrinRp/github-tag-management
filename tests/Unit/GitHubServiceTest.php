<?php

namespace Tests\Unit;

use App\Services\GitHubService;
use Exception;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Mockery;

class GitHubServiceTest extends TestCase
{
    /** @test */
    public function it_fetches_starred_repositories_for_a_user()
    {
        $mockResponse = [
            [
                'id' => 123,
                'name' => 'repo-1',
                'description' => 'A description',
                'html_url' => 'https://github.com/user/repo-1',
                'language' => 'PHP',
            ],
            [
                'id' => 456,
                'name' => 'repo-2',
                'description' => 'Another description',
                'html_url' => 'https://github.com/user/repo-2',
                'language' => 'JavaScript',
            ],
        ];

        Http::fake([
            'https://api.github.com/users/*/starred*' => Http::response(
                $mockResponse,
                200,
                [
                    'X-RateLimit-Remaining' => 5000,
                    'X-RateLimit-Limit' => 5000,
                    'X-RateLimit-Reset' => time() + 3600,
                ]
            ),
        ]);

        $gitHubService = new GitHubService();
        $repositories = $gitHubService->fetchStarredRepositories('username');

        $this->assertCount(2, $repositories);
        $this->assertEquals('repo-1', $repositories[0]['name']);
        $this->assertEquals('repo-2', $repositories[1]['name']);
    }

    /** @test */
    /** @test */
    public function it_throws_exception_when_rate_limit_exceeded()
    {
        $mockResponse = Http::response([], 403, ['X-RateLimit-Remaining' => '0']);

        Http::fake([
            'https://api.github.com/users/*/starred*' => $mockResponse,
        ]);

        Http::withOptions(['verify' => false]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Rate limit exceeded. Please wait before making more requests.');

        $gitHubService = new GitHubService();
        $gitHubService->fetchStarredRepositories('username');
    }

    /** @test */
    public function it_throws_exception_when_user_not_found()
    {
        $mockResponse = Http::response([], 404);

        Http::fake([
            'https://api.github.com/users/*/starred*' => $mockResponse,
        ]);

        Http::withOptions(['verify' => false]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('GitHub user not found.');

        $gitHubService = new GitHubService();
        $gitHubService->fetchStarredRepositories('nonexistent_user');
    }
}

