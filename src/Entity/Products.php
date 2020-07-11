<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Products
 *
 * @ORM\Table(name="products")
 * @ORM\Entity
 */
class Products
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
     * @var string
     *
     * @ORM\Column(name="sku", type="string", length=50, nullable=false)
     */
    private $sku;

    /**
     * @var string|null
     *
     * @ORM\Column(name="barcode", type="string", length=50, nullable=true, options={"default"="NULL"})
     */
    private $barcode = 'NULL';

    /**
     * @var int
     *
     * @ORM\Column(name="dimension_width", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $dimensionWidth = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="dimension_length", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $dimensionLength = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="color", type="smallint", nullable=false, options={"unsigned"=true})
     */
    private $color = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true, options={"default"="NULL","unsigned"=true})
     */
    private $groupId = 'NULL';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getDimensionWidth(): ?int
    {
        return $this->dimensionWidth;
    }

    public function setDimensionWidth(int $dimensionWidth): self
    {
        $this->dimensionWidth = $dimensionWidth;

        return $this;
    }

    public function getDimensionLength(): ?int
    {
        return $this->dimensionLength;
    }

    public function setDimensionLength(int $dimensionLength): self
    {
        $this->dimensionLength = $dimensionLength;

        return $this;
    }

    public function getColor(): ?int
    {
        return $this->color;
    }

    public function setColor(int $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function setGroupId(?int $groupId): self
    {
        $this->groupId = $groupId;

        return $this;
    }


}
