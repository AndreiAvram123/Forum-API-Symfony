<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private $postsPerPage = 20;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function fetchRecentPosts()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($this->postsPerPage)
            ->getQuery()
            ->getResult();
    }
    public function findBookmarkedTimes(int $postID){
        $sql = "SELECT COUNT(user_id) AS 'result' FROM user_post WHERE post_id = $postID";
        $result = $this->executePDOStatement($sql);
        return $result['result'];
    }




    private function executePDOStatement($statement){
        $conn = $this->getEntityManager()
            ->getConnection();
        $result = $conn->prepare($statement);
        $result->execute();
        return $result->fetchAssociative();
    }


    public function fetchPage(int $postID)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id < :postID')
            ->orderBy("p.id", "DESC")
            ->setParameter('postID', $postID)
            ->setMaxResults($this->postsPerPage)
            ->getQuery()
            ->getResult();
    }


    public function findByTitle(string $query)
    {
        return $this->createQueryBuilder('p')
            ->select(array("p.postTitle", "p.postImage", "p.id"))
            ->andWhere('p.postTitle LIKE :postTitle')
            ->setParameter('postTitle', $query . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

    }


}
