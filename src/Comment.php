<?php

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
        private readonly int $id,
        private readonly ?string $name,
        private readonly ?string $text
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
     * @param array $data
     *
     * @return Comment
     *
     * @throws \Exception On missing ID.
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['id'])) {
            throw new \Exception('Missing comment ID');
        }
        return new self(
            (int) ($data['id']),
            $data['name'] ?? null,
            $data['text'] ?? null
        );
    }
}
