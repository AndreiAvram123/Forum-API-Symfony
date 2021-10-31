<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
#[UniqueEntity(fields: ['email'],message: "There is already an account with this email")]
#[UniqueEntity(fields: ['displayName'],message: "There is already an account with this username")]

class User implements  UserInterface, JsonSerializable, PasswordAuthenticatedUserInterface
{

    #[Pure] public function __construct()
    {
        $this->roles = ['user'];
        $this->comments = new ArrayCollection();
        $this->receiver = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->createdPosts = new ArrayCollection();
        $this->favoritePosts = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->chats = new ArrayCollection();
        $this->sentFriendRequests = new ArrayCollection();
        $this->receivedFriendRequests = new ArrayCollection();
        $this->profilePicture = null;
    }

    #[ORM\Column(type: "string",unique: true)]
    private string $hashedPassword;


    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;


    #[ORM\Column(type: "string",unique: true)]
    #[Assert\Email]
    private string $email;

    #[ORM\Column(type : 'json')]
    private array $roles = [];


   // #[ORM\OneToMany(mappedBy: "sender", targetEntity: Message::class, orphanRemoval: true)]

    private ArrayCollection $receiver;
//
//    /**
//     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="sender", orphanRemoval=true )
//     */
    private ArrayCollection $sentMessages;


  //  #[ORM\OneToMany(mappedBy: "user", targetEntity: Post::class, cascade: ["remove"], orphanRemoval: true)]
    private ArrayCollection $createdPosts;

 //   #[ORM\ManyToMany(targetEntity: Post::class)]
    private ArrayCollection $favoritePosts;

    #[ORM\Column(type: "string")]
    private ?string $displayName;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private  $friends;

   // #[ORM\ManyToMany(targetEntity: Chat::class,mappedBy: "users")]
    private ArrayCollection $chats;

    #[ORM\Column(type: "string",nullable: true)]

    private ?string $profilePicture;

    /**
     * @ORM\OneToMany(targetEntity=FriendRequest::class, mappedBy="sender", orphanRemoval=true)
     */
    private  $sentFriendRequests;

    /**
     * @ORM\OneToMany(targetEntity=FriendRequest::class, mappedBy="receiver", orphanRemoval=true)
     */
    private  $receivedFriendRequests;

    #[ORM\OneToMany(mappedBy: 'creator', targetEntity: Comment::class, orphanRemoval: true)]
    private  $comments;

    public function getUserIdentifier():string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }





    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->hashedPassword;
    }
    public function setPassword(string $hashedPassword){
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * @see UserInterface
     */
    public function getSalt():string
    {
        return "123456789";
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $sentMessage): self
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages[] = $sentMessage;
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): self
    {
        if ($this->sentMessages->contains($sentMessage)) {
            $this->sentMessages->removeElement($sentMessage);
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }


    #[ArrayShape(
        ['id' => "integer", 'displayName' => "string", 'email' => "string", 'profilePicture' => "null|string"])]
    public function jsonSerialize():array
    {
        return [
            'id' => $this->getId(),
            'displayName' => $this->getDisplayName(),
            'email' => $this->getUsername(),
            'profilePicture' => $this->getProfilePicture()
        ];
    }


    /**
     * @return Collection
     */
    public function getCreatedPosts(): Collection
    {
        return $this->createdPosts;
    }

    /**
     * @return Collection
     */
    public function getReceiver(): Collection
    {
        return $this->receiver;
    }

    public function addReceiver(Message $receiver): self
    {
        if (!$this->receiver->contains($receiver)) {
            $this->receiver[] = $receiver;
            $receiver->setSender($this);
        }

        return $this;
    }

    public function removeReceiver(Message $receiver): self
    {
        if ($this->receiver->contains($receiver)) {
            $this->receiver->removeElement($receiver);
            // set the owning side to null (unless already changed)
            if ($receiver->getSender() === $this) {
                $receiver->setSender(null);
            }
        }

        return $this;
    }

    public function addCreatedPost(Post $createdPost): self
    {
        if (!$this->createdPosts->contains($createdPost)) {
            $this->createdPosts[] = $createdPost;
            $createdPost->setUser($this);
        }

        return $this;
    }

    public function removeCreatedPost(Post $createdPost): self
    {
        if ($this->createdPosts->contains($createdPost)) {
            $this->createdPosts->removeElement($createdPost);
            // set the owning side to null (unless already changed)
            if ($createdPost->getUser() === $this) {
                $createdPost->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFavoritePosts(): Collection
    {
        return $this->favoritePosts;
    }

    public function addFavoritePost(Post $favoritePost): self
    {
        if (!$this->favoritePosts->contains($favoritePost)) {
            $this->favoritePosts[] = $favoritePost;
        }

        return $this;
    }

    public function removeFavoritePost(Post $favoritePost): self
    {
        if ($this->favoritePosts->contains($favoritePost)) {
            $this->favoritePosts->removeElement($favoritePost);
        }

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): self
    {
        if (!$this->friends->contains($friend)) {
            $this->friends[] = $friend;
        }

        return $this;
    }

    public function removeFriend(self $friend): self
    {
        if ($this->friends->contains($friend)) {
            $this->friends->removeElement($friend);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->addUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            $chat->removeUser($this);
        }

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }


    /**
     * @return Collection
     */
    public function getSentFriendRequests(): Collection
    {
        return $this->sentFriendRequests;
    }

    public function addSentFriendRequest(FriendRequest $sentFriendRequest): self
    {
        if (!$this->sentFriendRequests->contains($sentFriendRequest)) {
            $this->sentFriendRequests[] = $sentFriendRequest;
            $sentFriendRequest->setSender($this);
        }

        return $this;
    }

    public function removeSentFriendRequest(FriendRequest $sentFriendRequest): self
    {
        if ($this->sentFriendRequests->contains($sentFriendRequest)) {
            $this->sentFriendRequests->removeElement($sentFriendRequest);
            // set the owning side to null (unless already changed)
            if ($sentFriendRequest->getSender() === $this) {
                $sentFriendRequest->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReceivedFriendRequests(): Collection
    {
        return $this->receivedFriendRequests;
    }

    public function addReceivedFriendRequest(FriendRequest $receivedFriendRequest): self
    {
        if (!$this->receivedFriendRequests->contains($receivedFriendRequest)) {
            $this->receivedFriendRequests[] = $receivedFriendRequest;
            $receivedFriendRequest->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedFriendRequest(FriendRequest $receivedFriendRequest): self
    {
        if ($this->receivedFriendRequests->contains($receivedFriendRequest)) {
            $this->receivedFriendRequests->removeElement($receivedFriendRequest);
            // set the owning side to null (unless already changed)
            if ($receivedFriendRequest->getReceiver() === $this) {
                $receivedFriendRequest->setReceiver(null);
            }
        }

        return $this;
    }


}
