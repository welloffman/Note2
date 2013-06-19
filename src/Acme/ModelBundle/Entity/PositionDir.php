<?php

namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\ModelBundle\Entity\PositionDir
 *
 * @ORM\Table(name="position_dir")
 * @ORM\Entity
 */
class PositionDir extends Model
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
     * @ORM\OneToOne(targetEntity="Dir", inversedBy="position")
     * @ORM\JoinColumn(name="dir_id", referencedColumnName="id")
     */
    private $dir;

    /**
     * Set dir
     *
     * @param Acme\ModelBundle\Entity\Dir $dir
     * @return PositionDir
     */
    public function setDir(\Acme\ModelBundle\Entity\Dir $dir)
    {
        $this->dir = $dir;
        return $this;
    }

    /**
     * Get dir
     *
     * @return Acme\ModelBundle\Entity\Dir 
     */
    public function getDir()
    {
        return $this->dir;
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