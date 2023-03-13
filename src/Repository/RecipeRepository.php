<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\ORM\Query;
use App\Entity\RecipeSearch;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Recipe>
 *
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function save(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Recipe $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Recipe[] Return the latest recipes
     * @var integer Number de recipes 
     */
    public function findLatest(int $number): array
    {
        return $this->createQueryBuilder('r')
            ->setMaxResults($number)
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;        
    }

    /**
     * @return Recipe[]
     */
    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('r')
            ->join('r.category', 'c')
            ->andWhere("c.name = :cat")
            ->setParameter('cat', $category)
            ->getQuery()
            ->getResult()
        ;      
    }

    /**
     * @return Query
     */
    public function findAllQuery(RecipeSearch $search) : Query
    {
        $query = $this->createQueryBuilder('r')
                ->orderBy('r.createdAt', 'DESC');

        if($search->getNameRecipe())
        {
            $query = $query
                ->andWhere("r.name LIKE :nameRecipe")
                ->setParameter('nameRecipe', '%' . $search->getNameRecipe() . '%');
        }

        if($search->getIngredient())
        {
            $query = $query
                ->join('r.recipeIngredients', 'rI')
                ->join('rI.ingredients', 'i')
                ->andWhere("i.name LIKE :ingredient")
                ->setParameter('ingredient', '%' . $search->getIngredient() . '%');
        }

        if($search->getNameCategory())
        {
            $query = $query
                ->join('r.category', 'c')
                ->andWhere("c.id = :nameCategory")
                ->setParameter('nameCategory', $search->getNameCategory());
        }

        return $query->getQuery();
    }
}
