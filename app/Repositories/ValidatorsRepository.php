<?php

namespace App\Repositories;

class ValidatorsRepository {
	public static function getTelegramRequestRules() {
		return [
			'update_id' => 'required',
			'message' => 'required|array',
		];
	}  
}