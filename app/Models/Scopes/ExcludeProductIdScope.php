<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ExcludeProductIdScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('product_id', '!=', 1);
    }
}