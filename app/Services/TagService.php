<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\StarredRepository;
use Illuminate\Database\Eloquent\Collection;

class TagService
{
    /**
     * Add new tag for a repository
     *
     * @param array $tags
     * @param StarredRepository $repository
     * @return void
     */
    public function addTagsToRepository(array $tags, StarredRepository $repository): void
    {
        $existingTags = Tag::query()->whereIn('name', $tags)->get();
        $existingTagNames = $existingTags->pluck('name')->toArray();

        $newTags = array_diff($tags, $existingTagNames);

        if (!empty($newTags)) {
            Tag::insert(array_map(fn($tag) => ['name' => $tag], $newTags));
            $newTagModels = Tag::query()->whereIn('name', $newTags)->get();
            $existingTags = $existingTags->merge($newTagModels);
        }

        $repository->tags()->syncWithoutDetaching($existingTags->pluck('id')->toArray());
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
