<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function findParticipantByConversationAndUserId(int $conversationId, int $userId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb

            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('p.conversation', ':conversationId'),
                    $qb->expr()->neq('p.user', ':userId')
                )
            )
            ->setParameters([
                'conversationId' => $conversationId,
                'userId' => $userId
            ])
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }


    public function findParticipantByConversationId(int $conversationId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(   
                    $qb->expr()->eq('p.conversation', ':conversationId'),   
            )
            ->setParameter('conversationId',$conversationId)
        ;

        return $qb->getQuery()->getResult();
    }


    public function findConversationByTwoUserId(int $otheruserId, int $userId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
           // ->select('p.conversation')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('p.user', ':otheruserId'),
                    $qb->expr()->eq('p.user', ':userId')
                )
            )
            ->setParameters([
                'otheruserId' => $otheruserId,
                'userId' => $userId
            ])
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }



    // /**
    //  * @return Participant[] Returns an array of Participant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Participant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
