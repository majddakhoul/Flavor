<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Support\QueryOptions;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected function model(): Model
    {
        return new Category();
    }

    public function tree(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->query()->roots()->with(['children.meals', 'meals'])->orderBy('name')->get();
    }

    public function optionsWithDepth(): array
    {
        $options = [];

        foreach ($this->query()->roots()->with('children')->orderBy('name')->get() as $root) {
            $options[$root->id] = $root->t('name');

            foreach ($root->children as $child) {
                $options[$child->id] = '— ' . $child->t('name');
            }
        }

        return $options;
    }
}
