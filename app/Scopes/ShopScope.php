<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ShopScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Don't restrict during console commands without authenticated user
        if (app()->runningInConsole() && !auth()->check()) {
            return;
        }

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->isSuperAdmin()) {
                // If super_admin has switched to a specific active shop in session
                $activeShopId = session('active_shop_id');
                if (!empty($activeShopId) && $activeShopId !== 'all') {
                    $builder->where($model->getTable() . '.shop_id', $activeShopId);
                }
            } else {
                // Shop admin or employee is strictly confined to their own shop
                $builder->where($model->getTable() . '.shop_id', $user->shop_id);
            }
        }
    }
}
