<?php

namespace App\Services\WpApiConnector\ApiConnector;

use App\Services\WpApiConnector\ConnectorHttpClient\ClientConnectorHttpClient;
use App\Traits\Logable;

class BaseApiConnector
{
	use Logable;

	protected $apiHttpClient;

	protected const PER_PAGE = 'per_page=100';

	protected const NAMESPACE = 'wp-json/wp/v2';

	public function __construct(array $config = null)
	{
		$this->apiHttpClient = $config === null
			? app('wp-api-main-connector')
			: new ClientConnectorHttpClient($config);
	}

	protected function getPostsList(string $endpoint, string $status = null, string $context = null)
	{
		$statusUrl = $status ? "&status=$status" : '';
		$contextUrl = $context ? "&context=$context" : '';
		$path = self::NAMESPACE . $endpoint . '?' . self::PER_PAGE . $statusUrl . $contextUrl;
		$response = $this->apiHttpClient->sendRequest('GET', $path);

		if ($response && is_array($response) && isset($response['body']) && is_array($response['body'])) {

			$totalPages = $response['headers']['X-WP-TotalPages'][0];

			if ($totalPages > 1) {

				for ($page = 1; $page <= $totalPages; $page++) {
					$path = self::NAMESPACE . $endpoint . '?' . self::PER_PAGE . "&page=$page" . $statusUrl . $contextUrl;
					$response = $this->apiHttpClient->sendRequest('GET', $path);
					if ($response && is_array($response) && isset($response['body']) && is_array($response['body'])) {

						$data = [];
						foreach ($response['body'] as $post) {
							$data[] = $post;
						}
					}
				}

				return $data;
			}

			return $response['body'];
		}
	}

	protected function storePost(string $endpoint, array $data)
	{
		$path = self::NAMESPACE . $endpoint;
		$response = $this->apiHttpClient->sendRequest('POST', $path, $data);

		if ($response && is_array($response) && isset($response['body']) && is_array($response['body'])) {
			//TODO: parse actual response
		}
	}

	protected function updatePost(string $endpoint, array $data)
	{
		$id = $data['id'];
		$path = self::NAMESPACE . $endpoint . "/$id";
		$response = $this->apiHttpClient->sendRequest('POST', $path, $data);

		if ($response && is_array($response) && isset($response['body']) && is_array($response['body'])) {
			//TODO: parse actual response
		}
	}

	protected function deletePost(string $endpoint, array $id)
	{
		$path = self::NAMESPACE . $endpoint . "/$id?force=true";
		$response = $this->apiHttpClient->sendRequest('DELETE', $path);

		if ($response && is_array($response) && isset($response['body']) && is_array($response['body'])) {
			//TODO: parse actual response
		}
	}
}
