<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message implements \JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sentMessages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sender;


    /**
     * @ORM\ManyToOne(targetEntity=Chat::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chat;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $localID;

    /**
     * @ORM\ManyToMany(targetEntity=User::class)
     */
    private $seenBy;

    public function __construct()
    {
        $this->seenBy = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }


    public function jsonSerialize()
    {

        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'chatID' => $this->getChat()->getId(),
            'sender' => $this->getSender(),
            'date' => $this->getDate()->getTimestamp(),
            'content' => $this->getContent(),
            'localID' => $this->localID,
            'seenBy' => (sizeof($this->seenBy) > 0)
        ];
    }


    public function getChat(): ?Chat
    {
        return $this->chat;
    }

    public function setChat(?Chat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLocalID(): ?string
    {
        return $this->localID;
    }

    public function setLocalID(?string $localID): self
    {
        $this->localID = $localID;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getSeenBy(): Collection
    {
        return $this->seenBy;
    }

    public function addSeenBy(User $seenBy): self
    {
        if (!$this->seenBy->contains($seenBy)) {
            $this->seenBy[] = $seenBy;
        }

        return $this;
    }

    public function removeSeenBy(User $seenBy): self
    {
        if ($this->seenBy->contains($seenBy)) {
            $this->seenBy->removeElement($seenBy);
        }

        return $this;
    }


}
