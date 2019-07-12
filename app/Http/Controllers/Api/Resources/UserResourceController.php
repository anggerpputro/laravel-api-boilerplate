<?php
namespace App\Http\Controllers\Api\Resources;

use App\Core\CoreRestController;

use App\User;

class UserResourceController extends CoreRestController
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
