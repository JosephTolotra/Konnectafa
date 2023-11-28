<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function findConversationByparticipant(int $otherUserId, int $myId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
           ->select('c.id')
           ->innerJoin('c.participant', 'p')
           ->where(
                  $qb->expr()->orX(
                       $qb->expr()->eq('p.user', ':me'),
                       $qb->expr()->eq('p.user', ':otherUser')
                     )
                   )
           ->groupBy('p.conversation')
           ->having(
            $qb->expr()->eq(
                $qb->expr()->count('p.conversation'),
                2
            )
        )
        ->setParameters([
            'me' => $myId,
            'otherUser' => $otherUserId
        ])
    ;
     return $qb->getQuery()->getResult();
    }

    

    public function findConversationByUser(int $UserId)
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select(
                'otherUser.username',
                'otherUser.id',
                'otherUser.profilepicture',
                'c.id As conversationId',
                
            )
            ->innerJoin(
                'c.participant', 'p', Join::WITH,
                $qb->expr()->neq('p.user', ':user')
            )
            ->innerJoin(
                'c.participant', 'me', Join::WITH,
                $qb->expr()->eq('me.user', ':user')
            )
           
            ->innerJoin('me.user', 'meUser')
            ->innerJoin('p.user', 'otherUser')
            ->where('meUser.id = :user')
        
            ->setParameter('user', $UserId)
        ;


        return $qb->getQuery()->getResult();
    }

}





    // /**
    //  * @return Conversation[] Returns an array of Conversation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

