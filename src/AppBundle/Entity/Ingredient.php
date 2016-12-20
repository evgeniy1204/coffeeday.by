<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="ingredient")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IngredientRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Ingredient
{
   /**
    * @var int
    *
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;


    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="ingredient", cascade={"persist", "remove"})
     */
    private $stock;

    /**
     * @ORM\OneToMany(targetEntity="Recept", mappedBy="ingredient", cascade={"persist", "remove"})
     */
    private $recept;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stock = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Ingredient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Ingredient
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


 /**
     * @ORM\PrePersist
     */
    public function setCreated()
    {
        $this->created = new \DateTime();

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdated()
    {
        $this->updated = new \DateTime();

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Add stock
     *
     * @param \AppBundle\Entity\Stock $stock
     *
     * @return Ingredient
     */
    public function addStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stock[] = $stock;

        return $this;
    }

    /**
     * Remove stock
     *
     * @param \AppBundle\Entity\Stock $stock
     */
    public function removeStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stock->removeElement($stock);
    }

    /**
     * Get stock
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Add recept
     *
     * @param \AppBundle\Entity\Recept $recept
     *
     * @return Ingredient
     */
    public function addRecept(\AppBundle\Entity\Recept $recept)
    {
        $this->recept[] = $recept;

        return $this;
    }

    /**
     * Remove recept
     *
     * @param \AppBundle\Entity\Recept $recept
     */
    public function removeRecept(\AppBundle\Entity\Recept $recept)
    {
        $this->recept->removeElement($recept);
    }

    /**
     * Get recept
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRecept()
    {
        return $this->recept;
    }

    /**
     * Set stock
     *
     * @param \AppBundle\Entity\Stock $stock
     *
     * @return Ingredient
     */
    public function setStock(\AppBundle\Entity\Stock $stock = null)
    {
        $this->stock = $stock;

        return $this;
    }
}
