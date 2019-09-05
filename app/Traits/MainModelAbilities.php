<?php
namespace App\Traits;

use App\Contracts\MainModelContract;

trait MainModelAbilities
{

    /**
     * =========================
     * PUBLIC METHODS
     * ---------
    **/
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }


    /**
     * =========================
     * MAIN SCOPES
     * ---------
    **/
    public function scopeSearch($query, $string, $field = '')
    {
        $arr_date_fields = ['created_at', 'updated_at', 'deleted_at'];

        if (! empty($field)) {
            if (in_array($field, $arr_date_fields)) {
                return $this->getQueryDateSearch($query, $string, $field);
            }

            return $query->orWhere($this->getTable().'.'.$field, 'LIKE', '%'.$string.'%');
        } else {
            $primary = $this->getKeyName();
            $cols = $this->getTableColumns();

            return $query->where(function ($q) use ($primary, $cols, $string, $arr_date_fields) {
                foreach (array_diff($cols, $arr_date_fields) as $col) {
                    if ($col !== $primary) {
                        $q->orWhere($this->getTable().'.'.$col, 'LIKE', '%'.$string.'%');
                    }
                }
            });
        }
    }

    public function scopeOrder($query, $field = '', $asc_or_desc = 'asc')
    {
        if (! empty($field)) {
            return $query->orderBy($this->getTable().'.'.$field, $asc_or_desc);
        } else {
            return $query->orderBy($this->getTable().'.'.$this->primaryKey, $asc_or_desc);
        }
    }

    public function scopePerPage($query, $limit = 30)
    {
        return $query->limit($limit);
    }


    /**
     * =========================
     * PROTECTED METHODS
     * ---------
    **/
    protected function getQueryDateSearch(&$query, $search, $search_field)
    {
        $strtotime = strtotime($search);
        $year = date('Y', $strtotime);
        $month = date('m', $strtotime);

        switch (strlen($search)) {
            case 10:
                return $query->whereDate($this->getTable().'.'.$search_field, $search);
            case 7:
                return $query->whereYear($this->getTable().'.'.$search_field, $year)
                            ->whereMonth($this->getTable().'.'.$search_field, $month);
            case 4:
                return $query->whereYear($this->getTable().'.'.$search_field, $year);
            default:
                return $query;
        }
    }
}
