<?php

namespace App\Entity;

class ShopLink
{
    private ?int $id;

    private string $text;

    private array $filters;

    private string $url;

    private bool $active;

    public function __construct(?int $id, string $text)
    {
        $this->id = $id;
        $this->text = $text;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters, string $key): self
    {
        if ($this->active) {
            // remove link from currently applied filters
            $filters[$key] = array_diff($filters[$key], [$this->id]);
        } else {
            // add link to filters
            $filters[$key][] = $this->id;
        }

        $this->filters = $filters;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(array $filters): self
    {
        if (in_array($this->id, $filters)) {
            $this->active = true;
        } else {
            $this->active = false;
        }

        return $this;
    }
}
