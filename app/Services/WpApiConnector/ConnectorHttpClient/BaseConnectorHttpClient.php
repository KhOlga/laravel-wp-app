<?php

namespace App\Services\WpApiConnector\ConnectorHttpClient;

use App\Traits\{Logable, Validatable};
use GuzzleHttp\Client;
use GuzzleHttp\Exception\{ClientException, GuzzleException};
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class BaseConnectorHttpClient
{
	use Validatable;
	use Logable;

	protected $httpClient;

	public function sendRequest(string $method, string $path, array $params = null)
	{
		try {
			switch ($method) {
				case 'POST':
					$response = $this->httpClient->post($path, [ RequestOptions::FORM_PARAMS => $params ]);
					break;
				case 'GET':
					$response = $this->httpClient->request($method, $path, $params);
					break;
				case 'DELETE':
					$response = $this->httpClient->delete($path);
					break;
				default:
					$this->buildLog(
						'logs/api_connector.log',
						'error',
						"BaseConnectorClient::sendRequest(): HTTP method $method has not been found."
					);
					break;
			}

			return $this->getParcedData($response);
		} catch (GuzzleException $exception) {
			$this->buildLog(
				'logs/api_connector.log',
				'error',
				$exception->getMessage()
			);
		}
	}

	protected function createClient($config): Client
	{
		return new Client([
			'base_uri' => $config['host'],
			'verify' => false
		]);
	}

	protected function getToken($client, $config): string
	{
		$response = $client->post('wp-json/jwt-auth/v1/token', [
			RequestOptions::QUERY => [
				'username' => $config['username'],
				'password' =>  $config['password'],
			],
		]);

		$data = json_decode($response->getBody(), true);

		return $data['token'];
	}

	protected function validateToken($client, $token): bool
	{
		$response = $client->post('wp-json/jwt-auth/v1/token/validate', [
			RequestOptions::HEADERS => [
				'Authorization' => 'Bearer ' . $token
			],
		]);

		$data = json_decode($response->getBody(), true);

		if ($data['data']['status'] !== 200) {
			$this->buildLog(
				'logs/api_connector.log',
				'error',
				'403: jwt_auth_invalid_token'
			);

			return false;
		}

		return true;
	}

	protected function authorizedHttpClient($config, $token): void
	{
		$this->httpClient = new Client([
			'base_uri' => $config['host'],
			'verify' => false,
			'headers' => [
				'Authorization' => 'Bearer ' . $token
			],
		]);
	}

	private function getParcedData(Response $response)
	{
		$body = [];
		$headers = [];

		try {
			$body = json_decode($response->getBody(), true);
			$headers = $response->getHeaders();
		} catch (ClientException $exception) {
			$this->buildLog(
				'logs/api_connector.log',
				'error',
				$exception->getMessage()
			);
		}

		return [
			'body' => $body,
			'headers' => $headers
		];
	}
}
