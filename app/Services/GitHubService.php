<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GitHubService
{
    protected string $baseUrl;
    protected int $perPage;

    public function __construct()
    {
        $this->baseUrl = 'https://api.github.com';
        $this->perPage = 100;
    }

    /**
     * Fetch all starred repositories for a given GitHub username.
     *
     * @param string $username
     * @return array
     * @throws Exception
     */
    public function fetchStarredRepositories(string $username): array
    {
        $url = "{$this->baseUrl}/users/{$username}/starred";
        $headers = $this->getAuthorizationHeaders();
        $repositories = [];
        $page = 1;

        // Fetch repositories in batches with pagination
        do {
            $response = $this->sendRequest("{$this->baseUrl}/users/{$username}/starred", [
                'per_page' => $this->perPage,
                'page' => $page,
            ]);

            $repositories = array_merge($repositories, $response->json());

            $this->checkRateLimit($response);

            $page++;
        } while (count($response->json()) === $this->perPage);

        return $repositories;
    }

    /**
     * Send a GET request to the GitHub API.
     *
     * @param string $url
     * @param array $queryParams
     * @return Response
     * @throws Exception
     */
    protected function sendRequest(string $url, array $queryParams): Response
    {
        $response = Http::withOptions(['verify' => env('APP_ENV') !== 'local'])
            ->withHeaders($this->getAuthorizationHeaders())
            ->get($url, $queryParams);

        if ($response->failed()) {
            $statusCode = $response->status();
            throw new Exception($this->handleError($statusCode, $response));
        }

        return $response;
    }

    /**
     * Get authorization headers to increase rate limit from 60 per hour to 5000 per hour.
     *
     * @return array
     */
    protected function getAuthorizationHeaders(): array
    {
        $headers = [];
        if ($token = config('services.github.token')) {
            $headers['Authorization'] = "token {$token}";
        }
        return $headers;
    }

    /**
     * Handle GitHub API errors based on status code and response.
     *
     * @param int $statusCode
     * @param Response $response
     * @return string
     */
    protected function handleError(int $statusCode, Response $response): string
    {
        return match ($statusCode) {
            403 => $response->header('X-RateLimit-Remaining') === '0'
                ? 'Rate limit exceeded. Please wait and try again later.'
                : 'Access forbidden. Please check your token or permissions.',
            404 => 'GitHub user not found.',
            default => 'An error occurred while fetching data from GitHub: ' . $response->body(),
        };
    }

    /**
     * Check the remaining rate limit from the response headers.
     *
     * @param Response $response
     * @throws Exception
     */
    protected function checkRateLimit(Response $response): void
    {
        $remaining = (int) $response->header('X-RateLimit-Remaining', 1);
        if ($remaining === 0) {
            throw new Exception('Rate limit exceeded. Please wait before making more requests.');
        }
    }
}
