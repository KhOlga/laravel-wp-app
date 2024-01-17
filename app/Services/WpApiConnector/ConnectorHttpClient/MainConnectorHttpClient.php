<?php

namespace App\Services\WpApiConnector\ConnectorHttpClient;

class MainConnectorHttpClient extends BaseConnectorHttpClient
{
	protected $apiConfig = [];

	private $token;

	public function __construct()
	{
		$this->apiConfig = config('services.api-main-connector');
		$this->validateConfig($this->apiConfig, 'wp-api-main-connector');
		$client = $this->createClient($this->apiConfig);
		$this->token = $this->getToken($client, $this->apiConfig);

		if ($this->validateToken($client, $this->token)) {
			$this->authorizedHttpClient($this->apiConfig, $this->token);
		}
	}
}
