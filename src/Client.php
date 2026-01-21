<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;

/**
 * Main comments API client class.
 */
class Client
{
    public const BASE_URI = 'https://89b6a81e-5e8f-4acf-a069-72d9e03e90d8.mock.pstmn.io';

    private $apiToken;

    /**
     * Class constructor.
     * 
     * @param string $apiToken API token.
     * 
     * @param ClientInterface|null $httpClient PSR-18 HTTP client. Will try to be discovered if null.
     * @param RequestFactoryInterface|null $requestFactory PSR-17 Request factory. Will try to be discovered if null.
     */
    public function __construct(
        string $apiToken = '',
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null
        )
    {
        $this->apiToken = $apiToken;
    }

    /**
     * Get all comments.
     *
     * @return Comment[]
     */
    public function getAll(): array
    {
        $request = $this->requestFactory->createRequest('GET', self::BASE_URI . '/comments');
        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response');
        }
        
        $comments = [];
        foreach ($responseData as $comment) {
            $comments[] = Comment::fromArray($comment);
        }
            
        return $comments;
    }

    /**
     * Create a new comment.
     *
     * @param string $name Author name.
     * @param string $text Comment text.
     * 
     * @return Comment Created comment.
     */
    public function createComment(string $name, string $text): Comment
    {
        $request = $this
            ->requestFactory
            ->createRequest('POST', self::BASE_URI . '/comments')
            ->withBody(stream_for(json_encode([
                'name' => $name,
                'text' => $text,
            ])));
        $response = $this->httpClient->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response');
        }

        return Comment::fromArray($responseData);
    }

    /**
     * Update an existing comment.
     *
     * @param int $id Comment ID.
     * @param array $data Updated comment date. 
     * 
     * @return Comment Updated comment.
     */
    public function updateComment(int $id, array $data): Comment
    {
        $request = $this
            ->requestFactory
            ->createRequest('PUT', self::BASE_URI . '/comments')
            ->withBody(stream_for(json_encode([
                'name' => $name,
                'text' => $text,
            ])));
        $response = $this->httpClient->sendRequest($request);
        
        $responseData = json_decode($response->getBody()->getContents(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response');
        }

        return Comment::fromArray($responseData);
    }
}
