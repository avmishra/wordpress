<?php

namespace Shoppinglist\ApiBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ShoppinglistItemRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShoppinglistItemRepository extends EntityRepository
{
    /**
     * 
     * not in use
     */
    public function updatePickup($id, $status)
    {
        return $this->getEntityManager()
            ->createQuery('UPDATE '
                    . 'ShoppinglistApiBundle:ShoppinglistItem i SET i.picked = :picked'
                    . 'WHERE i.idShoppinglistItem = :id'
            )->setParameter(':id', $id)
             ->setParameter(':picked', $status)
            ->getResult();
    }
    
    /**
     * This function will return all items of given shoppinglist id
     * 
     * @param int $ids
     * @return array
     */
    public function getItemsOfShoppinglist($ids)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT i, partial s.{idShoppinglist} FROM '
                    . 'ShoppinglistApiBundle:ShoppinglistItem i '
                    . 'JOIN i.fkShoppinglist s WITH s.idShoppinglist IN (:id) '
            )->setParameter(':id', $ids)
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
    
    public function deleteShoppinglistItemByIds($ids)
    {
        return $this->getEntityManager()
            ->createQuery(
                'DELETE '
                . ' FROM ShoppinglistApiBundle:ShoppinglistItem si'
                . ' WHERE si.idShoppinglistItem IN (:ids)'   

            )->setParameter(':ids', $ids)
            ->execute();
    }
}
