<?php

namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\ModelBundle\Entity\Position
 *
 * @ORM\Table(name="position")
 * @ORM\Entity
 */
class Position extends Model
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $pos
     *
     * @ORM\Column(name="pos", type="integer", length=10)
     */
    protected $pos;


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
     * Set pos
     *
     * @param integer $pos
     * @return Position
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    
        return $this;
    }

    /**
     * Get pos
     *
     * @return integer 
     */
    public function getPos()
    {
        return $this->pos;
    }

    public function toArray() {
        return array( 'id' => $this->getId(), 'pos' => $this->getPos() );
    }
}