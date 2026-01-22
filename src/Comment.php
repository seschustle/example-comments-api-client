<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

use seschustle\ExampleCommentsApiClient\Exception\DTOCreationException;

/**
 * Simple DTO for comments.
 */
class Comment
{
    /**
     * Class constructor.
     */
    public function __construct(
        private int $id,
        private string $name,
        private string $text
    ) {}

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Transform DTO object to a regular array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'text' => $this->text,
        ];
    }

    /**
     * Create DTO object from a data array
     *
     * @param array $data ['id' => int, 'name' => string, 'text' => string]
     *
     * @return Comment
     *
     * @throws DTOCreationException On invalid comment data.
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['id']) || (int) $data['id'] <= 0) {
            throw new DTOCreationException('ID must be a positive integer');
        }
        if (empty($data['name']) || empty($data['text'])) {
            throw new DTOCreationException('Missing comment name or text');
        }

        return new self(
            (int) $data['id'],
            $data['name'],
            $data['text']
        );
    }
}
