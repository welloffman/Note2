<?php
namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(targetEntity="Position")
     * @ORM\JoinColumn(name="pos_id", referencedColumnName="id")
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
        $this->user_id = $userId;
    
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
        $this->pid = $pid;
    
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
     * @param Acme\ModelBundle\Entity\Position $position
     * @return Dir
     */
    public function setPosition(\Acme\ModelBundle\Entity\Position $position = null)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return Acme\ModelBundle\Entity\Position 
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function toArray() {
        $array = parent::toArray();
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
}