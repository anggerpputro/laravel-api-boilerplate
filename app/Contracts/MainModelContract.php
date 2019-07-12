<?php 
namespace App\Contracts;

interface MainModelContract {

	public function scopeSearch($query, $string, $field = '');

	public function scopeOrder($query, $field = '', $asc_or_desc = 'asc');

	public function scopePerPage($query, $limit = 30);

}