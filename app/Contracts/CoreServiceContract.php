<?php
namespace App\Contracts;

/**
 * CoreServiceContract
 */
interface CoreServiceContract
{
    /**
     * Get a listing of the resource.
    **/
    public function listAll($model = null, $disable_search = false);

    /**
     * Store a newly created resource in storage.
    **/
    public function store($requestData, $merge_data_with = []);

    /**
     * Get the specified resource.
    **/
    public function findOrFail($id, $addWith = true);

    /**
     * Get the specified resource.
    **/
    public function firstOrFail($model, $addWith = true);

    /**
     * Update the specified resource in storage.
    **/
    public function update($requestData, $id, $merge_data_with = []);

    /**
     * Remove the specified resource from storage.
    **/
    public function delete($id);
}