<?php

namespace seschustle\ExampleCommentsApiClient;

use CommentsApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CommentsApiClient
{
    private Client $httpClient;
    private string $baseUrl;

    public function __construct(string $baseUrl = 'https://example.com')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30.0,
        ]);
    }

    /**
     * Get all comments
     *
     * @return Comment[]
     * @throws CommentsApiException
     */
    public function getComments(): array
    {
        try {
            $response = $this->httpClient->get('/comments');
            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array($data)) {
                throw new CommentsApiException('Invalid response format from API');
            }

            $comments = [];
            foreach ($data as $commentData) {
                $comments[] = Comment::fromArray($commentData);
            }

            return $comments;
        } catch (GuzzleException $e) {
            throw new CommentsApiException('Failed to fetch comments: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create a new comment
     *
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws CommentsApiException
     */
    public function createComment(string $name, string $text): Comment
    {
        try {
            $response = $this->httpClient->post('/comments', [
                'json' => [
                    'name' => $name,
                    'text' => $text,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array($data)) {
                throw new CommentsApiException('Invalid response format from API');
            }

            return Comment::fromArray($data);
        } catch (GuzzleException $e) {
            throw new CommentsApiException('Failed to create comment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update an existing comment
     *
     * @param string $id
     * @param string $name
     * @param string $text
     * @return Comment
     * @throws CommentsApiException
     */
    public function updateComment(string $id, string $name, string $text): Comment
    {
        try {
            $response = $this->httpClient->put("/comments/{$id}", [
                'json' => [
                    'name' => $name,
                    'text' => $text,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!is_array($data)) {
                throw new CommentsApiException('Invalid response format from API');
            }

            return Comment::fromArray($data);
        } catch (GuzzleException $e) {
            throw new CommentsApiException('Failed to update comment: ' . $e->getMessage(), 0, $e);
        }
    }
}
