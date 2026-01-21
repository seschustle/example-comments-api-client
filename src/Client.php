<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use GuzzleHttp\Client as GuzzleClient;
use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;

/**
 * Main comments API client class.
 */
class Client
{
    public const BASE_URI = 'https://89b6a81e-5e8f-4acf-a069-72d9e03e90d8.mock.pstmn.io';

    private GuzzleClient $httpClient;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->httpClient = new GuzzleClient(['base_uri' => self::BASE_URI]);
    }

    /**
     * Get all comments.
     *
     * @return Comment[]
     */
    public function getAll(): array
    {
        $response = $this->httpClient->get('/comments');
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
        $response = $this->httpClient->post('/comments', [
            'json' => [
                'name' => $name,
                'text' => $text,
            ],
        ]);
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
        $response = $this->httpClient->post('/comments', [
            'json' => $data,
        ]);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidApiResponseException('Invalid JSON response');
        }

        return Comment::fromArray($responseData);
    }
}
