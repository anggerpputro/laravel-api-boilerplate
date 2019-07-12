<?php
namespace App\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Contracts\ApiResourceControllerContract as ResourceContract;
use App\Traits\JSONResponses;

abstract class CoreRestController extends Controller implements ResourceContract
{
    use JSONResponses;

    // protected $repo;
    //
    // public function __construct(CoreRepository $repo) {
    //     $this->repo = $repo;
    // }

    protected $model;
    protected $with = [];
    protected $orderBy = [];

    protected $service;
    protected $response;

    public function __construct(CoreModel $model, CoreService $service = null, CoreRestResponse $response = null)
    {
        $this->model = $model;
        $this->service = $service;
        $this->response = $response;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! empty($this->with)) {
            $this->model = $this->model->with($this->with);
        }

        if (request()->has('search')) {
            $search = request('search');
            $search_field = request()->has('search_field') ? request('search_field') : '';

            $this->model = $this->model->search($search, $search_field);
        }

        $order = request()->has('order') ? request('order') : $this->model->getKeyName();
        $atoz = request()->has('atoz') ? request('atoz') : 'asc';

        $this->model = $this->model->order($order, $atoz);

        return $this->model->paginate(
            request()->has('page_len')
            ? request('page_len')
            : 30
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateStoreRequest($request);

        $saved_data = $this->model->create($request->all());

        return $this->responseSuccess([
            'saved' => $saved_data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $this->responseError([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            return $this->responseError([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateUpdateRequest($request, $id);

        try {
            $data = $this->model->findOrFail($id);
            $data->fill($request->all());
            $saved = $data->save();

            return $this->responseSuccess([
                'saved' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->responseError([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            return $this->responseError([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $data = $this->model->findOrFail($id);
            $data->delete();

            return $this->responseSuccess([
                'deleted' => $data
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->responseError([
                'error' => 'ID:'.$id.' Not Found!'
            ]);
        } catch (\Exception $e) {
            return $this->responseError([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }


    /**
     * =========================
     * VALIDATORS
     * ---------
    **/
    protected function validateStoreRequest($request)
    {
        \Validator::make($request->all(), [

        ])->validate();
    }

    protected function validateUpdateRequest($request, $id)
    {
        \Validator::make($request->all(), [

        ])->validate();
    }
}
