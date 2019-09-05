<?php
namespace App\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Contracts\MainModelContract;
use App\Traits\MainModelAbilities;

abstract class CoreModel extends Model implements MainModelContract
{
    use SoftDeletes,
        MainModelAbilities;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = []; // empty to make all attributes mass assignable

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['updated_at', 'deleted_at'];
}
