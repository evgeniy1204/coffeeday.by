<?php

namespace AppBundle\Repository;

/**
 * StockRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockRepository extends \Doctrine\ORM\EntityRepository
{
   /*
    * Ищем есть ли данный ингредиент в данном баре
    */
    public function getIngredientInBar($ingredient, $bar)
    {
         $qb = $this->createQueryBuilder('p')
                    ->select('p')
                    ->where('p.ingredient = :ingredient')
                    ->andWhere('p.bar = :bar')
                    ->setParameter('ingredient', $ingredient)
                    ->setParameter('bar', $bar);

        return $qb->getQuery()->getResult();
    }
}
