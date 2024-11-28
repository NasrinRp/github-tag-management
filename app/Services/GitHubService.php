<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'https://api.github.com';
    }

    /**
     * Fetch starred repositories for a given GitHub username.
     *
     * @param string $username
     * @return array
     * @throws Exception
     */
    //todo: check rate limit
    public function fetchStarredRepositories(string $username): array
    {
        $url = "{$this->baseUrl}/users/{$username}/starred";

        // Disable SSL verification (for development only)
        $response = Http::withOptions(['verify' => env('APP_ENV') !== 'local'])->get($url);

        if ($response->failed()) {
            $statusCode = $response->status();
            $errorMessage = $this->handleError($statusCode);

            throw new \Exception($errorMessage);
        }

        return $response->json();
    }

    /**
     * Handle GitHub API errors based on status code.
     *
     * @param int $statusCode
     * @return string
     */
    //todo: make error handling better
    protected function handleError(int $statusCode): string
    {
        return match ($statusCode) {
            403 => 'Rate limit exceeded. Please try again later.',
            404 => 'GitHub user not found.',
            default => 'An error occurred while fetching data from GitHub.',
        };
    }
}

