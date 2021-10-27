<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findRecentMessages(int $chatID): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere("m.chat = :chatID")
            ->setParameter("chatID", $chatID)
            ->orderBy("m.id", "DESC")
            ->setMaxResults(20)
            ->getQuery()
            ->getResult();
    }

    public function findLastChatMessage(int $chatID): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere("m.chat = :chatID")
            ->setParameter("chatID", $chatID)
            ->orderBy("m.id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLastMessage(int $chatID): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere("m.chat = :chatID")
            ->setParameter("chatID", $chatID)
            ->orderBy("m.id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


}
