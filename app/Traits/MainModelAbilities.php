<?php 
namespace App\Traits;

use App\Contracts\MainModelContract;

trait MainModelAbilities {
	
	/**
	 * =========================
	 * PUBLIC METHODS
	 * ---------
	**/
	public function getTableColumns() {
		return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
	}
	

	/**
	 * =========================
	 * MAIN SCOPES
	 * ---------
	**/
	public function scopeSearch($query, $string, $field = '') {
		if( ! empty($field) ) {
			return $query->where(function($q) use ($string, $field) {
				$q->orWhere($this->table.'.'.$field, 'LIKE', '%'.$string.'%');
			});
		}
		else {
			$cols = $this->getTableColumns();
			return $query->where(function($q) use ($cols, $string) {
				foreach($cols as $col) {
					$q->orWhere($this->table.'.'.$col, 'LIKE', '%'.$string.'%');
				}
			});
		}
	}

	public function scopeOrder($query, $field = '', $asc_or_desc = 'asc') {
		if( ! empty($field) ) {
			return $query->orderBy($this->table.'.'.$field, $asc_or_desc);
		}
		else {
			return $query->orderBy($this->table.'.'.$this->primaryKey, $asc_or_desc);
		}
	}

	public function scopePerPage($query, $limit = 30) {
		return $query->limit($limit);
	}

}