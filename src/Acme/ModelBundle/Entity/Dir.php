<?php
namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="dir")
 * @ORM\Entity(repositoryClass="Acme\ModelBundle\Repository\DirRepository")
 */
class Dir extends Model {
	/**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(type="integer", length=10)
     */
    protected $user_id;
    /**
     * @ORM\Column(type="text")
     */
    protected $title;
    /**
     * @ORM\Column(type="integer", length=10)
     */
    protected $pid;

    /**
     * @ORM\OneToOne(targetEntity="PositionDir", mappedBy="dir")
     */
    protected $position;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="dir")
     */
    protected $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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
     * Set user_id
     *
     * @param integer $userId
     * @return Dir
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Dir
     */
    public function setTitle($title)
    {
        $title = trim(strip_tags($title));
        $this->title = $title ? $title : "Новый раздел";
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return Dir
     */
    public function setPid($pid)
    {
        $this->pid = (int)$pid;
    
        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set position
     *
     * @param Acme\ModelBundle\Entity\PositionDir $position
     * @return Dir
     */
    public function setPosition(\Acme\ModelBundle\Entity\PositionDir $position)
    {
        $this->position = $position;
        $this->position->setDir($this);

        return $this;
    }

    /**
     * Get position
     *
     * @return Acme\ModelBundle\Entity\PositionDir 
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function toArray() {
        $array = parent::toArray();
        $p = $this->getPosition();
        $array['position'] = $this->getPosition()->toArray();
        unset($array['notes']);
        return $array;
    }

    /**
     * Add notes
     *
     * @param Acme\ModelBundle\Entity\Note $notes
     * @return Dir
     */
    public function addNote(\Acme\ModelBundle\Entity\Note $notes)
    {
        $this->notes[] = $notes;
    
        return $this;
    }

    /**
     * Remove notes
     *
     * @param Acme\ModelBundle\Entity\Note $notes
     */
    public function removeNote(\Acme\ModelBundle\Entity\Note $notes)
    {
        $this->notes->removeElement($notes);
    }

    /**
     * Get notes
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    public function getType() {
        return 'dir';
    }
}