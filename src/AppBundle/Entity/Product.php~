<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     * @ORM\Column(name="cost", type="decimal", precision=10, scale=2)
     */
    private $cost;

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
     * @ORM\ManyToMany(targetEntity="Recept", mappedBy="products", cascade={"persist", "remove"})
     */
    private $recept;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="product")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Bin", mappedBy="product", cascade={"persist", "remove"})
     */
    private $bin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recept = new \Doctrine\Common\Collections\ArrayCollection();
        $this->bin = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * Set cost
     *
     * @param string $cost
     *
     * @return Product
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Add recept
     *
     * @param \AppBundle\Entity\Recept $recept
     *
     * @return Product
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
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add bin
     *
     * @param \AppBundle\Entity\Bin $bin
     *
     * @return Product
     */
    public function addBin(\AppBundle\Entity\Bin $bin)
    {
        $this->bin[] = $bin;

        return $this;
    }

    /**
     * Remove bin
     *
     * @param \AppBundle\Entity\Bin $bin
     */
    public function removeBin(\AppBundle\Entity\Bin $bin)
    {
        $this->bin->removeElement($bin);
    }

    /**
     * Get bin
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBin()
    {
        return $this->bin;
    }
}
