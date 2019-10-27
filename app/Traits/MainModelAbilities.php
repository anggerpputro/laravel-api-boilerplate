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
    public function scopeSearch($query, $string, $field = '', $mode = 'or')
    {
        $arr_date_fields = ['created_at', 'updated_at', 'deleted_at'];

        $string_like = '%'.$string.'%';
        if (strpos($string, '%') !== false) {
            $string_like = $string;
        }

        if (! empty($field)) {
            if (in_array($field, $arr_date_fields)) {
                return $this->getQueryDateSearch($query, $string, $field);
            }

            if ($mode == 'or') {
                return $q->orWhere($this->getTable().'.'.$field, 'LIKE', $string_like);
            } else {
                return $q->where($this->getTable().'.'.$field, 'LIKE', $string_like);
            }
        } else {
            $primary = $this->getKeyName();
            $cols = $this->getTableColumns();

            return $query->where(function ($q) use ($mode, $primary, $cols, $string_like, $arr_date_fields) {
                if ($mode == 'or') {
                    foreach (array_diff($cols, $arr_date_fields) as $col) {
                        if ($col !== $primary) {
                            $q->orWhere($this->getTable().'.'.$col, 'LIKE', $string_like);
                        }
                    }
                } else {
                    foreach (array_diff($cols, $arr_date_fields) as $col) {
                        if ($col !== $primary) {
                            $q->where($this->getTable().'.'.$col, 'LIKE', $string_like);
                        }
                    }
                }
            });
        }
    }

    public function scopeSearchExact($query, $string, $field = '', $mode = 'or')
    {
        $arr_date_fields = ['created_at', 'updated_at', 'deleted_at'];

        if (! empty($field)) {
            if (in_array($field, $arr_date_fields)) {
                return $this->getQueryDateSearch($query, $string, $field);
            }

            if ($mode == 'or') {
                return $query->orWhere($this->getTable().'.'.$field, '=', $string);
            } else {
                return $query->where($this->getTable().'.'.$field, '=', $string);
            }
        } else {
            $primary = $this->getKeyName();
            $cols = $this->getTableColumns();

            return $query->where(function ($q) use ($mode, $primary, $cols, $string, $arr_date_fields) {
                if ($mode == 'or') {
                    foreach (array_diff($cols, $arr_date_fields) as $col) {
                        if ($col !== $primary) {
                            $q->orWhere($this->getTable().'.'.$col, '=', $string);
                        }
                    }
                } else {
                    foreach (array_diff($cols, $arr_date_fields) as $col) {
                        if ($col !== $primary) {
                            $q->where($this->getTable().'.'.$col, '=', $string);
                        }
                    }
                }
            });
        }
    }

    public function scopeSearchMultiple($query, $string = [], $field = [], $mode = 'or')
    {
        return $query->where(function ($q) use ($mode, $string, $field) {
            foreach ($string as $i => $string_item) {
                if ($string_item != '') {
                    if (! isset($field[$i])) {
                        throw new \Exception("Please complete your search field!");
                    }
                    $field_item = $field[$i];

                    $string_like = '%'.$string_item.'%';
                    if (strpos($string_item, '%') !== false) {
                        $string_like = $string_item;
                    }

                    if ($mode == 'or') {
                        $q = $q->orWhere($this->getTable().'.'.$field_item, 'LIKE', $string_like);
                    } else {
                        $q = $q->where($this->getTable().'.'.$field_item, 'LIKE', $string_like);
                    }
                }
            }
        });
    }

    public function scopeSearchExactMultiple($query, $string = [], $field = [], $mode = 'or')
    {
        return $query->where(function ($q) use ($mode, $string, $field) {
            foreach ($string as $i => $string_item) {
                if ($string_item != '') {
                    if (! isset($field[$i])) {
                        throw new \Exception("Please complete your search field!");
                    }
                    $field_item = $field[$i];

                    if ($mode == 'or') {
                        $q = $q->orWhere($this->getTable().'.'.$field_item, '=', $string_item);
                    } else {
                        $q = $q->where($this->getTable().'.'.$field_item, '=', $string_item);
                    }
                }
            }
        });
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
