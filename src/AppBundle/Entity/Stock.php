<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StockRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Stock
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
     * @var integer
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

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
     * @ORM\ManyToOne(targetEntity="Ingredient", inversedBy="stock")
     * @ORM\JoinColumn(name="ingredient_id", referencedColumnName="id")
     */
    private $ingredient; 

    /**
     * @ORM\OneToMany(targetEntity="WriteOff", mappedBy="stock")
     */
    private $writeoff;

    /**
     * @ORM\OneToMany(targetEntity="Reception", mappedBy="stock")
     */
    private $reception;

    /**
     * @ORM\ManyToOne(targetEntity="Bar", inversedBy="stock")
     * @ORM\JoinColumn(name="bar_id", referencedColumnName="id")
     */
    private $bar;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->writeoff = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reception = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ingredient = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return Stock
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set ingredient
     *
     * @param \AppBundle\Entity\Ingredient $ingredient
     *
     * @return Stock
     */
    public function setIngredient(\AppBundle\Entity\Ingredient $ingredient = null)
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    /**
     * Get ingredient
     *
     * @return \AppBundle\Entity\Ingredient
     */
    public function getIngredient()
    {
        return $this->ingredient;
    }

    /**
     * Add writeoff
     *
     * @param \AppBundle\Entity\WriteOff $writeoff
     *
     * @return Stock
     */
    public function addWriteoff(\AppBundle\Entity\WriteOff $writeoff)
    {
        $this->writeoff[] = $writeoff;

        return $this;
    }

    /**
     * Remove writeoff
     *
     * @param \AppBundle\Entity\WriteOff $writeoff
     */
    public function removeWriteoff(\AppBundle\Entity\WriteOff $writeoff)
    {
        $this->writeoff->removeElement($writeoff);
    }

    /**
     * Get writeoff
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWriteoff()
    {
        return $this->writeoff;
    }

    /**
     * Add reception
     *
     * @param \AppBundle\Entity\Reception $reception
     *
     * @return Stock
     */
    public function addReception(\AppBundle\Entity\Reception $reception)
    {
        $this->reception[] = $reception;

        return $this;
    }

    /**
     * Remove reception
     *
     * @param \AppBundle\Entity\Reception $reception
     */
    public function removeReception(\AppBundle\Entity\Reception $reception)
    {
        $this->reception->removeElement($reception);
    }

    /**
     * Get reception
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReception()
    {
        return $this->reception;
    }

    /**
     * Set bar
     *
     * @param \AppBundle\Entity\Bar $bar
     *
     * @return Stock
     */
    public function setBar(\AppBundle\Entity\Bar $bar = null)
    {
        $this->bar = $bar;

        return $this;
    }

    /**
     * Get bar
     *
     * @return \AppBundle\Entity\Bar
     */
    public function getBar()
    {
        return $this->bar;
    }
}
