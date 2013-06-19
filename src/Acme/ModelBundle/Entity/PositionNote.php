<?php

namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\ModelBundle\Entity\PositionNote
 *
 * @ORM\Table(name="position_note")
 * @ORM\Entity
 */
class PositionNote extends Model
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
     * @ORM\OneToOne(targetEntity="Note", inversedBy="position")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id")
     */
    private $note;

    /**
     * Set note
     *
     * @param Acme\ModelBundle\Entity\Note $note
     * @return PositionNote
     */
    public function setNote(\Acme\ModelBundle\Entity\Note $note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Get note
     *
     * @return Acme\ModelBundle\Entity\Note 
     */
    public function getNote()
    {
        return $this->note;
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