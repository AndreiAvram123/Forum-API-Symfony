<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Image implements \JsonSerializable
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "image_id", type: 'integer')]
    private int $id;

    #[ORM\Column(type: "string",length: 255)]
    private string $url;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Post $post;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }






    public function getId(): ?int
    {
        return $this->id;
    }



    public function jsonSerialize(): ?string
    {
        return $this->getImageURL();
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
