<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogService {
	/**
	 * Log a message to the daily log file.
	 *
	 * @param string $message
	 * @param array $context
	 */
	public function infoLog(string $message, array $context = []) {
		Log::info($message, $context);
	}

	public function errorLog(string $message, array $context = []) {
		Log::error($message, $context);
	}
}
