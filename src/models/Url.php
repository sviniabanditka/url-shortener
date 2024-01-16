<?php

namespace App\Models;

use PDO;

class Url
{
    private PDO $db;

    private string $identifier;
    private string $original_url;
    private string $expired_at;
    private int $clicks = 0;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function setOriginalUrl(string $original_url): self
    {
        $this->original_url = $original_url;
        return $this;
    }

    public function setExpiredAt(string $expired_at): self
    {
        $this->expired_at = $expired_at;
        return $this;
    }

    public function getClicks(): int
    {
        return $this->clicks;
    }

    public function getOriginalUrl(): string
    {
        return $this->original_url;
    }

    public function setClicks(int $clicks): self
    {
        $this->clicks = $clicks;
        return $this;
    }

    public function createUrl(): self
    {
        $stmt = $this->db->prepare("INSERT INTO url (identifier, original_url, expired_at) VALUES (:identifier, :original_url, :expired_at);");
        $stmt->bindValue(':identifier', $this->identifier);
        $stmt->bindValue(':original_url', $this->original_url);
        $stmt->bindValue(':expired_at', $this->expired_at);
        $stmt->execute();
        return (new self($this->db))->setIdentifier($this->identifier)
            ->setOriginalUrl($this->original_url)
            ->setExpiredAt($this->expired_at);
    }

    public function findByIdentifier(string $identifier): self|false
    {
        $expired_at = date("Y-m-d H:i:s");
        $stmt = $this->db->prepare("SELECT * FROM url WHERE identifier = :identifier AND expired_at >= :expired_at");
        $stmt->bindValue(':identifier', $identifier);
        $stmt->bindValue(':expired_at', $expired_at);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!empty($row)) {
            return (new self($this->db))->setIdentifier($row['identifier'])
                ->setOriginalUrl($row['original_url'])
                ->setExpiredAt($row['expired_at'])
                ->setClicks($row['clicks']);
        }
        return false;
    }

    public function incrementClicks(): self
    {
        $this->clicks += 1;
        $stmt = $this->db->prepare("UPDATE url SET clicks = :clicks WHERE identifier = :identifier");
        $stmt->bindValue(':identifier', $this->identifier);
        $stmt->bindValue(':clicks', $this->clicks);
        $stmt->execute();
        return (new self($this->db))->setIdentifier($this->identifier)
            ->setOriginalUrl($this->original_url)
            ->setExpiredAt($this->expired_at)
            ->setClicks($this->clicks);
    }
}
