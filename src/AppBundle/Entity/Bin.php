<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bin")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\BinRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Bin
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
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="bin")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Check", inversedBy="bin", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="check_id", referencedColumnName="id")
     */
    private $check;

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
     * @var integer
     *
     * @ORM\Column(name="is_free", type="integer")
     */
    private $is_free;
    
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
     * Set product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return Bin
     */
    public function setProduct(\AppBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set check
     *
     * @param \AppBundle\Entity\Check $check
     *
     * @return Bin
     */
    public function setCheck(\AppBundle\Entity\Check $check = null)
    {
        $this->check = $check;

        return $this;
    }

    /**
     * Get check
     *
     * @return \AppBundle\Entity\Check
     */
    public function getCheck()
    {
        return $this->check;
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
     * Set isFree
     *
     * @param integer $isFree
     *
     * @return Bin
     */
    public function setIsFree($isFree)
    {
        $this->is_free = $isFree;

        return $this;
    }

    /**
     * Get isFree
     *
     * @return integer
     */
    public function getIsFree()
    {
        return $this->is_free;
    }
}
