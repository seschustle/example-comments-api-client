<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient\Http;

use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP client wrapper for making API requests.
 */
class CommentsApiClient
{
    private Client $client;

    /**
     * Class constructor.
     *
     * @param string $baseUri Base URI for API requests
     * @param array $config Additional Guzzle client configuration
     */
    public function __construct(string $baseUri, array $config = [])
    {
        $defaultConfig = [
            'base_uri' => rtrim($baseUri, '/'),
            'timeout' => 30.0,
        ];

        $this->client = new Client(array_merge($defaultConfig, $config));
    }

    /**
     * Perform a GET request and decode JSON response.
     *
     * @param string $uri Request URI
     * @param array $options Request options
     * @return array Decoded JSON response
     * @throws ApiRequestException
     */
    public function get(string $uri, array $options = []): array
    {
        try {
            $response = $this->client->get($uri, $options);
            return $this->decodeJsonResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiRequestException('GET request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Perform a POST request and decode JSON response.
     *
     * @param string $uri Request URI
     * @param array $options Request options
     * @return array Decoded JSON response
     * @throws ApiRequestException
     */
    public function post(string $uri, array $options = []): array
    {
        try {
            $response = $this->client->post($uri, $options);
            return $this->decodeJsonResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiRequestException('POST request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Perform a PUT request and decode JSON response.
     *
     * @param string $uri Request URI
     * @param array $options Request options
     * @return array Decoded JSON response
     * @throws ApiRequestException
     */
    public function put(string $uri, array $options = []): array
    {
        try {
            $response = $this->client->put($uri, $options);
            return $this->decodeJsonResponse($response);
        } catch (GuzzleException $e) {
            throw new ApiRequestException('PUT request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Decode JSON response body to array.
     *
     * @param ResponseInterface $response HTTP response
     * @return array
     * @throws \InvalidArgumentException
     */
    private function decodeJsonResponse(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Failed to decode JSON response: ' . json_last_error_msg());
        }

        return $data ?? [];
    }
}
