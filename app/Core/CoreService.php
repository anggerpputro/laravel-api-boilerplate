<?php
namespace App\Core;

use App\Core\CoreModel;

class CoreService
{
    protected $model;
    protected $with = [];
    protected $orderBy = [];

    protected $modelPrimaryKeyName = "";
    protected $modelTableName = "";

    public function __construct(CoreModel $model, $with = [], $orderBy = [])
    {
        $this->model = $model;
        $this->setWith($with);
        $this->setOrderBy($orderBy);

        $this->modelPrimaryKeyName = $model->getKeyName();
        $this->modelTableName = $model->getTable();
    }

    public function setWith($with)
    {
        $this->with = $with;
    }

    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * Get a listing of the resource.
    **/
    public function listAll($model = null, $disable_search = false)
    {
        try {
            if (! is_null($model)) {
                $this->model = $model;
            }

            if (! empty($this->with)) {
                $this->model = $this->model->with($this->with);
            }

            if (request()->has('search') && !$disable_search) {
                $search = request('search');
                $search_field = request()->has('search_field') ? request('search_field') : '';

                $this->model = $this->model->search($search, $search_field);
            }

            $order = request()->has('order') ? request('order') : $this->modelPrimaryKeyName;
            $atoz = request()->has('atoz') ? request('atoz') : 'asc';

            $this->model = $this->model->order($order, $atoz);

            if (request()->has('page_len') && request('page_len') == 'all') {
                return $this->model->paginate(999);
            }
            return $this->model->paginate(
                request()->has('page_len')
                ? request('page_len')
                : 30
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Store a newly created resource in storage.
    **/
    public function store($requestData, $merge_data_with = [])
    {
        $data = $this->model;
        $data->fill(array_merge($requestData, $merge_data_with));
        $data->save();
        return $data;
    }

    /**
     * Get the specified resource.
    **/
    public function findOrFail($id, $addWith = true)
    {
        if (! empty($this->with) && $addWith) {
            $this->model = $this->model->with($this->with);
        }

        return $this->model->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
    **/
    public function update($requestData, $id, $merge_data_with = [])
    {
        $data = $this->findOrFail($id, false);
        $data->fill(array_merge($requestData, $merge_data_with));
        $data->save();
        return $data;
    }

    /**
     * Remove the specified resource from storage.
    **/
    public function delete($id)
    {
        $data = $this->findOrFail($id, false);
        $data->delete();
        return $data;
    }
}
