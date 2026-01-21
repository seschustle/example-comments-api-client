<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;
use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use seschustle\ExampleCommentsApiClient\Exception\DTOCreationException;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;

/**
 * Main comments API client class.
 */
class Client
{
    public const BASE_URI = 'https://example.com';

    private $apiToken;

    /**
     * Class constructor.
     * 
     * @param string $apiToken API token.
     * @param ClientInterface|null $httpClient PSR-18 HTTP client. Will try to be discovered if null.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 Request factory. Will try to be discovered if null.
     * @param StreamFactoryInterface|null $streamFactory PSR-17 Stream factory. Will try to be discovered if null.
     */
    public function __construct(
        string $apiToken,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?StreamFactoryInterface $streamFactory = null
        )
    {
        $this->apiToken = $apiToken;
        $this->httpClient ??= Psr18ClientDiscovery::find();
        $this->requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactory ??= Psr17FactoryDiscovery::findStreamFactory();
    }

    /**
     * Get all comments.
     *
     * @return Comment[]
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function getAll(): array
    {   
        try {
            return array_map(
                static fn (array $commentData): Comment => Comment::fromArray($commentData),
                $this->makeRequest('GET', '/comments')
            );
        } catch (DTOCreationException $ex) {
            throw new InvalidApiResponseException('Failed to create Comment DTO from a reponse data', $ex->getCode(), $ex);
        }
    }

    /**
     * Create a new comment.
     *
     * @param array $fields Comment data with 'name' and 'text' keys.
     * 
     * @return Comment Created comment.
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function createComment(array $fields): Comment
    {
        try {
            return Comment::fromArray($this->makeRequest('POST', '/comments', ['body' => $fields]));
        } catch (DTOCreationException $ex) {
            throw new InvalidApiResponseException('Failed to create Comment DTO from a reponse data', $ex->getCode(), $ex);
        }
    }

    /**
     * Update an existing comment.
     *
     * @param int $id Comment ID.
     * @param array $fields Comment fields to update.
     * 
     * @return Comment Updated comment.
     * 
     * @throws InvalidApiResponseException When comment DTO creation fails.
     */
    public function updateComment(int $id, array $fields): Comment
    {
        try {
            return Comment::fromArray($this->makeRequest('PUT', "/comments/$id", ['body' => $fields]));
        } catch (DTOCreationException $ex) {
            throw new InvalidApiResponseException('Failed to create Comment DTO from a reponse data', $ex->getCode(), $ex);
        }
    }

    /**
     * Make HTTP request with optional JSON body.
     *
     * @param string $method HTTP Request method.
     * @param string $path Request path.
     * @param array $options Request options. Currently only supports JSON body.
     * 
     * @return array Decoded response data.
     * 
     * @throws ApiRequestException When HTTP request fails.
     * @throws InvalidApiResponseException When response status is not 2xx or JSON is invalid.
     */
    private function makeRequest(string $method, string $path, array $options = []): array
    {
        try {
            $response = $this->httpClient->sendRequest($this->prepareRequest($method, $path, $options));
        } catch (ClientExceptionInterface $e) {
            throw new ApiRequestException('Failed to send HTTP request', 0, $e);
        }
 
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ApiRequestException(
                sprintf(
                    'API returned HTTP %d status code. Response: %s',
                    $statusCode, 
                    json_decode($response->getBody()->getContents(), true)
                ),
                $statusCode,
            );
        }

        $responseData = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response received from API');
        }

        return $responseData;
    }

    /**
     * Prepare HTTP request with headers and optional JSON body.
     *
     * @param string $method Request method.
     * @param string $path URI path.
     * @param mixed $options HTTP request options.
     *
     * @return RequestInterface Prepared request.
     */
    private function prepareRequest(string $method, string $path, array $options = []): RequestInterface
    {
        $request = $this
            ->requestFactory
            ->createRequest($method, self::BASE_URI . $path)
            ->withHeader('Authorization', 'Bearer ' . $this->apiToken)
            ->withHeader('Content-Type', 'application/json');

        if ($options['body'] !== null) {
            $request = $request->withBody($this->streamFactory->createStream(json_encode($options['body'])));
        }

        return $request;
    }
}
