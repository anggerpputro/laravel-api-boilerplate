<?php
namespace App\Http\Controllers\Api\Resources;

use App\Core\CoreRestResourceController;

use App\Libraries\ImageUploader;

use Spatie\Permission\Models\Role;
use App\User;

/**
 * @group Master User
 *
 * APIs for managing user
 */
class UserResourceController extends CoreRestResourceController
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }


    /**
     * Display a listing of the resource of users.
     *
     * Spesial search:
     * - search opd: {"search_field": "opd_name", "search": "search string"}
     * - search role: {"search_field": "role_name", "search": "search string"}
     *
     * @authenticated
     * @bodyParam search string Add to search query
     * @bodyParam search_field string Search field. Defaults to 'id'
     * @bodyParam order string Order by. Defaults to 'id'
     * @bodyParam atoz string Order by asc or desc. Defaults to 'asc'
     * @bodyParam page_len int Page length. Defaults to 30
     * @response 200 {
     *  "current_page": 1,
     *  "data": [
     *      {
     *          "id": 1,
     *          "foo": "bar"
     *      }
     *  ],
     *  "first_page_url": "http://localhost:8000/api/users?page=1",
     *  "from": 1,
     *  "last_page": 1,
     *  "last_page_url": "http://localhost:8000/api/users?page=1",
     *  "next_page_url": null,
     *  "path": "http://localhost:8000/api/users",
     *  "per_page": 30,
     *  "prev_page_url": null,
     *  "to": 1,
     *  "total": 1
     * }
     * @response 500 {"status":500, "message":"Server Error!", "data":{"error":"Error message"}}
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = $this->model;
        $disable_default_search = false;
        if (request()->has('search')) {
            $search = request('search');
            $search_field = request()->has('search_field') ? request('search_field') : '';

            switch ($search_field) {
                case 'role_name':
                    $model_has_roles_table = config('permission.table_names.model_has_roles');
                    $roles_table = config('permission.table_names.roles');

                    $model = $model->leftJoin($model_has_roles_table, $model_has_roles_table.'.model_id', '=', $this->modelTableName.'.id')
                            ->leftJoin($roles_table, $roles_table.'.id', '=', $model_has_roles_table.'.role_id')
                            ->where($roles_table.'.name', 'like', '%'.$search.'%')
                            ->select($this->modelTableName.'.*');

                    $disable_default_search = true;
                    break;
                default:
                    break;
            }
        }

        return $this->responseSuccessOrException(function () use ($model, $disable_default_search) {
            return $this->service->listAll($model, $disable_default_search);
        });
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
            'email' => 'email',
            // 'avatar_file' => 'image',
            'role' => 'required|string',
            // 'opd_id' => 'integer',
        ]);
    }

    protected function validateUpdateRequest($request, $id)
    {
        return \Validator::make($request->all(), [
            'username' => 'required|unique:'.$this->modelTableName.',username,'.$id,
            'email' => 'email',
            // 'avatar_file' => 'image',
            'role' => 'required|string',
            // 'opd_id' => 'integer',
        ]);
    }


    /**
     * =========================
     * HOOKS
     * ---------
    **/
    protected function beforeStoreHooks($request)
    {
        // validate and store avatar
        if ($request->hasFile('avatar_file')) {
            $imageUploader = new ImageUploader($request->file('avatar_file'));

            $image_saved_path = $imageUploader->storeImage('/uploads/avatars/');

            $this->merge_store_data_with = array_merge(
                $this->merge_store_data_with,
                [
                    'avatar' => $image_saved_path,
                ]
            );
            // if ($request->file('avatar_file')->isValid()) {
            // } else {
            //     throw new \Exception("File avatar gagal diupload atau tidak valid!");
            // }
        }
    }

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
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function beforeUpdateHooks($request, $id)
    {
        $this->beforeStoreHooks($request);
        $this->merge_update_data_with = $this->merge_store_data_with;
    }

    protected function afterUpdateHooks($savedData, $request, $id)
    {
        $this->afterStoreHooks($savedData, $request);
    }
}
