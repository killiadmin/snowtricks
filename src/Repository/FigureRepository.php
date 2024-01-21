<?php

namespace App\Repository;

use App\Entity\Figure;
use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Figure>
 *
 * @method Figure|null find($id, $lockMode = null, $lockVersion = null)
 * @method Figure|null findOneBy(array $criteria, array $orderBy = null)
 * @method Figure[]    findAll()
 * @method Figure[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FigureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Figure::class);
    }

    /**
     * Function to return all figures in the database
     * @return Figure[] Returns an array of Figure objects in blocks of 15
     */
    public function selectAllFigures(int $page, int $limit): array
    {
        $figures = $this->createQueryBuilder('f')
            ->orderBy('f.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $mediaRepository = $this->getEntityManager()->getRepository(Media::class);

        foreach ($figures as $figure) {
            $medias = $mediaRepository->createQueryBuilder('m')
                ->where('m.med_figure_associated = :figureEntity')
                ->setParameter('figureEntity', $figure)
                ->getQuery()
                ->getResult();

            foreach ($medias as $media) {
                $figure->addMedia($media);
            }
        }

        return $figures;
    }

    /**
     * Function allowing you to select a figure by filtering by the slug
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug): ?Figure
    {
        return $this->createQueryBuilder('f')
            ->where('f.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function add(Figure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Figure $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Figure[] Returns an array of Figure objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Figure
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
