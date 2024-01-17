<?php

namespace App\Services\WpApiConnector;

use App\Services\WpApiConnector\ConnectorHttpClient\{ClientConnectorHttpClient, MainConnectorHttpClient};
use Illuminate\Support\ServiceProvider;

class ApiConnectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('wp-api-main-connector', function () {
			return new MainConnectorHttpClient();
		});

		$this->app->singleton('wp-api-client-connector', function (array $config) {
			return new ClientConnectorHttpClient($config);
		});
    }
}
