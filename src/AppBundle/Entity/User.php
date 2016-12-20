<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @ORM\ManyToOne(targetEntity="Bar", inversedBy="user")
     * @ORM\JoinColumn(name="bar_id", referencedColumnName="id")
     */
    private $bar;

    /**
     * @ORM\OneToMany(targetEntity="Reception", mappedBy="user", cascade={"persist", "remove"})
     */
    private $reception;

    /**
     * @ORM\OneToMany(targetEntity="WriteOff", mappedBy="user", cascade={"persist", "remove"})
     */
    private $writeoff;

    /**
     * @ORM\OneToMany(targetEntity="Check", mappedBy="user", cascade={"persist", "remove"})
     */
    private $check;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set bar
     *
     * @param \AppBundle\Entity\Bar $bar
     *
     * @return User
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

    /**
     * Add reception
     *
     * @param \AppBundle\Entity\Reception $reception
     *
     * @return User
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
     * Add writeoff
     *
     * @param \AppBundle\Entity\Writeoff $writeoff
     *
     * @return User
     */
    public function addWriteoff(\AppBundle\Entity\Writeoff $writeoff)
    {
        $this->writeoff[] = $writeoff;

        return $this;
    }

    /**
     * Remove writeoff
     *
     * @param \AppBundle\Entity\Writeoff $writeoff
     */
    public function removeWriteoff(\AppBundle\Entity\Writeoff $writeoff)
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
     * Add check
     *
     * @param \AppBundle\Entity\Check $check
     *
     * @return User
     */
    public function addCheck(\AppBundle\Entity\Check $check)
    {
        $this->check[] = $check;

        return $this;
    }

    /**
     * Remove check
     *
     * @param \AppBundle\Entity\Check $check
     */
    public function removeCheck(\AppBundle\Entity\Check $check)
    {
        $this->check->removeElement($check);
    }

    /**
     * Get check
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCheck()
    {
        return $this->check;
    }
}
