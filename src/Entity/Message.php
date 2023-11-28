<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @ORM\Table(name="messages")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

        /**
     * @ORM\Column(type="string")
     */
    private $contentmessage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datemessage;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messages")
     */
    private $conversation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContentmessage(): ?string
    {
        return $this->contentmessage;
    }

    public function setContentmessage(string $contentmessage): self
    {
        $this->contentmessage = $contentmessage;

        return $this;
    }

    public function getDatemessage(): ?\DateTimeInterface
    {
        return $this->datemessage;
    }

    public function setDatemessage(\DateTimeInterface $datemessage): self
    {
        $this->datemessage = $datemessage;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
