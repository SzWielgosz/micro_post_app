<?php

namespace App\Repository;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuidler;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<MicroPost>
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function findAllWithComments(): array
    {
        return $this->findAllQuery(true)
            ->getQuery()
            ->getResult();
    }

    public function findAllWithCommentsAndLikes(): array
    {
        return $this->findAllQuery(true, true)
            ->getQuery()
            ->getResult();
    }
    
    public function findAllByAuthor(
        int $authorId
    ): array
    {
        return $this->findAllQuery(true, true, true, true)
            ->where('p.author = :author')
            ->setParameter('author', $authorId)
            ->getQuery()
            ->getResult();
    }

    public function findTopLikedPosts(): array
    {
        return $this->findAllQuery(false, true, true, true)
            ->addSelect('COUNT(l.id) AS HIDDEN likes_count')
            ->groupBy('p.id')
            ->orderBy('likes_count', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findFollowedPosts(User $user): array
    {
        return $this->findAllQuery(false, true, true, true)
            ->where('a IN (:follows)')
            ->setParameter('follows', $user->getFollows())
            ->getQuery()
            ->getResult();
    }

    private function findAllQuery(
        bool $withComments = false,
        bool $withLikes = false,
        bool $withAuthors = false,
        bool $withProfiles = false
    ): QueryBuilder {
        $qb = $this->createQueryBuilder('p');

        if ($withComments) {
            $qb->addSelect('c')
                ->leftJoin('p.comments', 'c');
        }

        if ($withLikes) {
            $qb->addSelect('l')
                ->leftJoin('p.likedBy', 'l');
        }

        if ($withAuthors) {
            $qb->addSelect('a')
                ->leftJoin('p.author', 'a');
        }

        if ($withProfiles) {
            $qb->addSelect('up')
                ->leftJoin('a.userProfile', 'up');
        }

        return $qb->orderBy('p.created', 'DESC');
    }


    //    /**
    //     * @return MicroPost[] Returns an array of MicroPost objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?MicroPost
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
