<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Auth;

class ManagedStaff implements Scope
{
    protected $user;

    /**
     * double try to get Auth::user because for unknown reason doesn't always work
     *
     * ShopScope constructor.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function apply(Builder $builder, Model $model){

    }

}