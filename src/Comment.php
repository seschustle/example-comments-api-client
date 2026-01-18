<?php
/**
 * @author Pavel Lovkii <plovkiy@yandex.ru>
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://github.com/seschustle/example-comments-api-client
 */

declare(strict_types=1);

namespace seschustle\ExampleCommentsApiClient;

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
        private ?string $name,
        private ?string $text
    ) {}

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
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
     * @param array $data ['id' => int, 'name' => string|null, 'text' => string|null]
     *
     * @return Comment
     *
     * @throws \InvalidArgumentException On invalid ID.
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['id'])) {
            throw new \InvalidArgumentException('Missing comment ID');
        }
        if ((int) $data['id'] <= 0) {
            throw new \InvalidArgumentException('ID must be a positive integer');
        }
        return new self(
            (int) $data['id'],
            $data['name'] ?? null,
            $data['text'] ?? null
        );
    }
}
