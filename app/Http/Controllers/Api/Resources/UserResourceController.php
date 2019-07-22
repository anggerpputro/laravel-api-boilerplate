<?php
namespace App\Http\Controllers\Api\Resources;

use App\Core\CoreRestResourceController;

use Spatie\Permission\Models\Role;
use App\User;
use App\Models\MsOpd;
use App\Models\RlUserOpd;

/**
 * @group Master User
 *
 * APIs for managing user
 */
class UserResourceController extends CoreRestResourceController
{
    protected $with = ["roles", "dataOpd"];

    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    /**
     * =========================
     * VALIDATORS
     * ---------
    **/
    protected function validateStoreRequest($request)
    {
        return \Validator::make($request->all(), [
            'username' => 'required|unique:'.$this->modelTableName.',username',
            'role' => 'required',
        ]);
    }

    protected function validateUpdateRequest($request, $id)
    {
        return \Validator::make($request->all(), [
            'username' => 'required|unique:'.$this->modelTableName.',username,'.$id,
            'role' => 'required',
        ]);
    }


    /**
     * =========================
     * HOOKS
     * ---------
    **/
    protected function afterStoreHooks($savedData, $request)
    {
        try {
            // save role
            if (request()->has("role")) {
                $req_role = request("role");

                $check_role = Role::findByName($req_role);
                if (!empty($check_role)) {
                    $savedData->syncRoles([$req_role]);
                }
            }

            // save opd
            if (request()->has("opd_id")) {
                $req_opd_id = request("opd_id");
                if ($opd = MsOpd::find($req_opd_id)) {
                    RlUserOpd::updateOrCreate([
                        'user_id' => $savedData->id
                    ], [
                        'opd_id' => $opd->id
                    ]);
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function afterUpdateHooks($savedData, $request, $id)
    {
        $this->afterStoreHooks($savedData, $request);
    }
}
