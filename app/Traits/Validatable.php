<?php

namespace App\Traits;

use InvalidArgumentException;

trait Validatable
{
	public function validateConfig($config, $serviceName): void
	{
		foreach ($config as $key => $value) {
			if (!isset($key) || empty($value)) {
				throw new InvalidArgumentException("Config $key => $value for [$serviceName] service is not valid.");
			}
		}
	}
}
