<?php

namespace App\Http\Controllers;

use App\Models\StarredRepository;
use App\Services\GitHubService;
use App\Services\TagService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StarredRepositoryController extends Controller
{
    protected GitHubService $gitHubService;
    protected TagService $tagService;

    public function __construct(GitHubService $gitHubService, TagService $tagService)
    {
        $this->gitHubService = $gitHubService;
        $this->tagService = $tagService;
    }

    /**
     * Fetches and saves a user's starred repositories to the database.
     *
     * @param String $username
     * @return JsonResponse
     */
    public function getStarredRepositories(String $username): JsonResponse
    {
        try {
            $repositories = $this->gitHubService->fetchStarredRepositories($username);

            // Atomically save or update starred repositories for a given user.
            // repository_id and username together form a unique constraint.
            DB::transaction(function () use ($repositories, $username) {
                foreach ($repositories as $repo) {
                    StarredRepository::query()->updateOrCreate(
                        [
                            'repository_id' => $repo['id'],
                            'username' => $username,
                        ],
                        [
                            'name' => $repo['name'],
                            'description' => $repo['description'],
                            'url' => $repo['html_url'],
                            'language' => $repo['language'],
                        ]
                    );
                }
            });

            $userStarredRepositories = StarredRepository::query()
                ->where('username', $username)
                ->get([
                    'name',
                    'description',
                    'url',
                    'language',
                ]);

            return response()->json([
                'message' => 'Repositories fetched and saved successfully',
                'username' => $username,
                'starred_repositories' => $userStarredRepositories,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @param Request $request
     * @param String $username
     * @return JsonResponse
     */
    public function searchRepositoriesByTag(Request $request, String $username): JsonResponse
    {
        $tagQuery = $request->query('tag');

        $repositories = $this->tagService->searchRepositoriesByTag($username, $tagQuery);

        return response()->json($repositories);
    }
}

