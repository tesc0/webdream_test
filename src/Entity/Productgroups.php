<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Productgroups
 *
 * @ORM\Table(name="productgroups")
 * @ORM\Entity
 */
class Productgroups
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true, options={"default"="NULL"})
     */
    private $name = 'NULL';

    /**
     * @var int
     *
     * @ORM\Column(name="brand_id", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $brandId;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=7, scale=2, nullable=false)
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    public function setBrandId(int $brandId): self
    {
        $this->brandId = $brandId;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }


}
