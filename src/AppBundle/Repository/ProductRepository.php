<?php

namespace AppBundle\Repository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends \Doctrine\ORM\EntityRepository
{
	/*
    * Получаем все существующие рецепта
    */
    public function findAllRecept()
    {
         $qb = $this->createQueryBuilder('p')
                    ->select('p')
                    ->innerJoin('p.recept', 'r');

        return $qb->getQuery()->getResult();
    }

    /*
    * Получаем рецепт конкеретного продуктв
    */
    public function findReceptProduct($id)
    {
         $qb = $this->createQueryBuilder('p')
                    ->select('p')
                    ->where('p.id = :id')
                    ->setParameter('id', $id)
                    ->innerJoin('p.recept', 'r');

        return $qb->getQuery()->getResult();
    }

	/*
    * Получаем продукты без рецепта
    */
    public function findAllNoRecept($test)
    {
         $qb = $this->createQueryBuilder('p')
                    ->select('p')
                    ->where('p.id NOT IN (:id)')
                    ->setParameter('id', $test);

        return $qb->getQuery()->getResult();
    }
}