<?php

namespace App\Services\WpApiConnector\ConnectorHttpClient;

class ClientConnectorHttpClient extends BaseConnectorHttpClient
{
	protected $apiConfig = [];

	private $token;

	public function __construct()
	{
		$this->apiConfig = config('services.api-client-connector');
		$this->validateConfig($this->apiConfig, 'wp-api-client-connector');
		$client = $this->createClient($this->apiConfig);
		$this->token = $this->getToken($client, $this->apiConfig);

		if ($this->validateToken($client, $this->token)) {
			$this->authorizedHttpClient($this->apiConfig, $this->token);
		}
	}
}
