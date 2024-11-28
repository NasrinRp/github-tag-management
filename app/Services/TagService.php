<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\StarredRepository;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    public function addTagsToRepository($tags, StarredRepository $repository): void
    {
        $tagModels = Tag::whereIn('name', $tags)->get();
        $existingTags = $tagModels->pluck('name')->toArray();

        $newTags = array_diff($tags, $existingTags);
        $newTagModels = collect();

        if ($newTags) {
            $newTagModels = Tag::insert(array_map(function ($tag) {
                return ['name' => $tag];
            }, $newTags));
        }

        $tagModels = $tagModels->merge($newTagModels);
        $repository->tags()->syncWithoutDetaching($tagModels->pluck('id')->toArray());
    }

    public function searchRepositoriesByTag($username, $tagQuery): Collection
    {
        return StarredRepository::query()
            ->where('username', $username)
            ->whereHas('tags', function ($query) use ($tagQuery) {
                $query->where('name', 'like', '%' . $tagQuery . '%');
            })
            ->get();
    }
}
