<?php

namespace App\Core;

use App\Core\CoreService;
use App\Core\CoreModel;

class CoreResourceService extends CoreService
{
    public function __construct(CoreModel $model, $with = [], $orderBy = [])
    {
        parent::__construct($model, $with, $orderBy);
    }
}
