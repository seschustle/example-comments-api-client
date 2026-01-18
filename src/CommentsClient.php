<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use seschustle\ExampleCommentsApiClient\Exception\ApiRequestException;
use seschustle\ExampleCommentsApiClient\Exception\InvalidApiResponseException;
use seschustle\ExampleCommentsApiClient\CommentsApiClient;

/**
 * Main comments API client class.
 */
class CommentsClient
{
    public const BASE_URI = 'https://89b6a81e-5e8f-4acf-a069-72d9e03e90d8.mock.pstmn.io';

    private CommentsApiClient $httpClient;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->httpClient = new CommentsApiClient(self::BASE_URI);
    }

    /**
     * Get all comments.
     *
     * @return Comment[]
     * 
     * @throws ApiRequestException
     * @throws InvalidApiResponseException
     * @throws \InvalidArgumentException
     */
    public function getAll(): array
    {
        $data = $this->httpClient->get('/comments');

        if (!is_array($data)) {
            throw new InvalidApiResponseException('Invalid response format from API');
        }

        $comments = [];
        foreach ($data as $commentData) {
            $comments[] = Comment::fromArray($commentData);
        }

        return $comments;
    }

    /**
     * Create a new comment
     *
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws ApiRequestException
     * @throws InvalidApiResponseException
     * @throws \InvalidArgumentException
     */
    public function createComment(string $name, string $text): Comment
    {
        $data = $this->httpClient->post('/comments', [
            'json' => [
                'name' => $name,
                'text' => $text,
            ],
        ]);

        if (!is_array($data)) {
            throw new InvalidApiResponseException('Invalid response format from API');
        }

        return Comment::fromArray($data);
    }

    /**
     * Update an existing comment
     *
     * @param int $id
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws ApiRequestException
     * @throws InvalidApiResponseException
     * @throws \InvalidArgumentException
     */
    public function updateComment(int $id, string $name, string $text): Comment
    {
        $data = $this->httpClient->put("/comments/{$id}", [
            'json' => [
                'name' => $name,
                'text' => $text,
            ],
        ]);

        if (!is_array($data)) {
            throw new InvalidApiResponseException('Invalid response format from API');
        }

        return Comment::fromArray($data);
    }
}
