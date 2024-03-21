<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait Logable
{
	public function buildLog(string $path, string $level, string $message): void
	{
		Log::build([
			'driver' => 'single',
			'path' => storage_path($path)
		])->$level($message);

	}

}