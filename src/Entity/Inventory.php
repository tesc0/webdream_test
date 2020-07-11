<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inventory
 *
 * @ORM\Table(name="inventory")
 * @ORM\Entity
 */
class Inventory
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="smallint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $productId;

    /**
     * @var int
     *
     * @ORM\Column(name="storage_id", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $storageId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getStorageId(): ?int
    {
        return $this->storageId;
    }

    public function setStorageId(int $storageId): self
    {
        $this->storageId = $storageId;

        return $this;
    }

    public function getInventoryListByStorage($storage_id) {

        if(!empty($storage_id)) {


            $inventory_qb = $this->getDoctrine()
                ->getRepository(Inventory::class)
                ->createQueryBuilder('i')
                ->select('pg.name as productName')
                ->addSelect('p.sku as sku')
                ->leftJoin(Products::class, 'p', 'WITH', 'p.id = i.productId')
                ->leftJoin(Productgroups::class, 'pg', 'WITH', 'pg.id = p.groupId')
                ->where('i.storageId = :storageId')
                ->setParameters(['storageId' => $storage_id]);

            $query = $inventory_qb->getQuery();
            $inventory = $query->execute();

        } else {

            $inventory_qb = $this->getDoctrine()
                ->getRepository(Inventory::class)
                ->createQueryBuilder('i')
                ->select('pg.name as productName')
                ->addSelect('p.sku as sku')
                ->addSelect('s.name as storageName')
                ->leftJoin(Storages::class, 's', 'WITH', 's.id = i.storageId')
                ->leftJoin(Products::class, 'p', 'WITH', 'p.id = i.productId')
                ->leftJoin(Productgroups::class, 'pg', 'WITH', 'pg.id = p.groupId')
            ;

            $query = $inventory_qb->getQuery();
            $inventory = $query->execute();
        }

        return $inventory;
    }

    public function inventoryItemsFrom($from_id)
    {
        $inventory_from_qb = $this->getDoctrine()->getRepository(Inventory::class)->createQueryBuilder("i")
            ->where('i.storageId = :storageId_from')
            ->setParameter(':storageId_from', $from_id)
            ->getQuery();

        return $inventory_from_qb->execute();
    }

    public function moveItemsBetweenStorages($from_id, $to_id)
    {
        $inventory_qb = $this->getDoctrine()->getRepository(Inventory::class)->createQueryBuilder("i")
            ->update(Inventory::class, 'i')
            ->set('i.storageId', ':storageId_to')
            ->where('i.storageId = :storageId_from')
            ->setParameter(':storageId_from', $from_id)
            ->setParameter(':storageId_to', $to_id)
            ->getQuery();

        $inventory_qb->execute();
    }

    public function removeItemsFromStorage($from_id)
    {
        $inventory_qb = $this->getDoctrine()->getRepository(Inventory::class)->createQueryBuilder("i")
            ->delete(Inventory::class, 'i')
            ->where('i.storageId = :storageId')
            ->setParameter(':storageId', $from_id)
            ->getQuery();

        $inventory_qb->execute();
    }

    public function moveItemById($from, $to, $id)
    {
        $inventory_qb = $this->getDoctrine()->getRepository(Inventory::class)->createQueryBuilder("i")
            ->update(Inventory::class, 'i')
            ->set('i.storageId', ':storageId_to')
            ->where('i.storageId = :storageId_from AND i.id = :invId')
            ->setParameter(':storageId_from', $from)
            ->setParameter(':storageId_to', $to)
            ->setParameter(':invId', $id)
            ->getQuery();

        $inventory_qb->execute();
    }

    public function selectInventory($storage_id)
    {
        $inventory_qb = $this->getDoctrine()
            ->getRepository(Inventory::class)
            ->createQueryBuilder('i')
            ->select('i.id')
            ->where('i.storageId = :storageId')
            ->setParameter(':storageId', $storage_id)
            ->getQuery();

        return $inventory_qb->execute();
    }


}
