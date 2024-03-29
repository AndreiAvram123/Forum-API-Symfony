<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment implements \JsonSerializable
{


    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private int $id;


    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull]
    private DateTimeInterface $commentDate;

    #[ORM\Column(type: "text")]
    #[NotBlank]
    #[Assert\NotNull]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private $creator;


    #[ArrayShape(['id' => "int", 'postID' => "int", 'date' => "int", 'content' => "string", 'user' => "\App\Entity\User|null"])] public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'postID'=>$this->getPost()->getId(),
            'date' => $this->getCommentDate()->getTimestamp(),
            'content' => $this->getContent(),
            'user' => $this->getUser()
        ];
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentDate(): ?\DateTimeInterface
    {
        return $this->commentDate;
    }

    public function setCommentDate(\DateTimeInterface $commentDate): self
    {
        $this->commentDate = $commentDate;

        return $this;
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

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

}
