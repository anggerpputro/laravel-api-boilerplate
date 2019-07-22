<?php
namespace App\Core;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use App\Core\CoreRestController;
use App\Core\CoreResourceService;
use App\Contracts\ApiResourceControllerContract as ResourceContract;

abstract class CoreRestResourceController extends CoreRestController implements ResourceContract
{
    protected $merge_store_data_with = [];
    protected $merge_update_data_with = [];

    public function __construct(CoreModel $model = null, CoreResourceService $service = null, CoreRestResponse $response = null)
    {
        if (is_null($service)) {
            $service = new CoreResourceService($model, $this->with, $this->orderBy);
        }

        parent::__construct($model, $service, $response);
    }


    /**
     * Display a listing of the resource.
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
        return $this->responseSuccessOrException(
            $this->service->listAll()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @authenticated
     * @response 200 {"status": 200, "message": "", "data": {"saved":1}}
     * @response 500 {"status":500, "message":"Server Error!", "data":{"error":"Error message"}}
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \DB::beginTransaction();
        try {
            $validator = $this->validateStoreRequest($request);

            if ($validator->fails()) {
                return $this->responseBadRequest([
                    'errors' => $validator->errors(),
                ]);
            }

            $this->beforeStoreHooks($request);

            $saved_data = $this->service->store($request->all(), $this->merge_store_data_with);

            $this->afterStoreHooks($saved_data, $request);

            \DB::commit();

            return $this->responseSuccess([
                'saved' => $saved_data
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->responseException($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @authenticated
     * @response 200 {
     *  "id": 1,
     *  "foo": "bar"
     * }
     * @response 400 {"status":400, "message":"Bad Request!", "data":{"error":"ID Not Found!"}}
     * @response 500 {"status":500, "message":"Server Error!", "data":{"error":"Error message"}}
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->service->findOrFail($id, $this->addWithOnShow);
        } catch (ModelNotFoundException $e) {
            return $this->responseBadRequest([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            return $this->responseException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @authenticated
     * @response 200 {"status": 200, "message": "", "data": {"saved":1}}
     * @response 400 {"status":400, "message":"Bad Request!", "data":{"error":"ID Not Found!"}}
     * @response 500 {"status":500, "message":"Server Error!", "data":{"error":"Error message"}}
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \DB::beginTransaction();
        try {
            $validator = $this->validateUpdateRequest($request, $id);

            if ($validator->fails()) {
                return $this->responseBadRequest([
                    'errors' => $validator->errors(),
                ]);
            }

            $this->beforeUpdateHooks($request, $id);

            $saved_data = $this->service->update($request->all(), $id, $this->merge_update_data_with);

            $this->afterUpdateHooks($saved_data, $request, $id);

            \DB::commit();

            return $this->responseSuccess([
                'saved' => $saved_data
            ]);
        } catch (ModelNotFoundException $e) {
            \DB::rollback();
            return $this->responseBadRequest([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->responseException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @authenticated
     * @response 200 {"status": 200, "message": "", "data": {"deleted":1}}
     * @response 400 {"status":400, "message":"Bad Request!", "data":{"error":"ID Not Found!"}}
     * @response 500 {"status":500, "message":"Server Error!", "data":{"error":"Error message"}}
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->service->delete($id);

            return $this->responseSuccess([
                'deleted' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->responseBadRequest([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            return $this->responseException($e);
        }
    }


    /**
     * =========================
     * HOOKS
     * ---------
    **/
    protected function beforeStoreHooks($request)
    {
    }

    protected function afterStoreHooks($savedData, $request)
    {
    }

    protected function beforeUpdateHooks($request, $id)
    {
    }

    protected function afterUpdateHooks($savedData, $request, $id)
    {
    }
}
