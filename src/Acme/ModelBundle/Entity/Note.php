<?php

namespace Acme\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\ModelBundle\Entity\Note
 *
 * @ORM\Table(name="note")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Acme\ModelBundle\Repository\NoteRepository")
 */
class Note extends Model
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

     /**
     * @ORM\ManyToOne(targetEntity="Dir", inversedBy="notes")
     * @ORM\JoinColumn(name="dir_id", referencedColumnName="id")
     */
    protected $dir;

    /**
     * @ORM\OneToOne(targetEntity="Position")
     * @ORM\JoinColumn(name="pos_id", referencedColumnName="id")
     */
    protected $position;

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
     * Set title
     *
     * @param string $title
     * @return Note
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
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
     * Set content
     *
     * @param string $content
     * @return Note
     */
    public function setContent($content)
    {
        //todo: использовать htmlentities() и html_entity_decode()
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set dir
     *
     * @param Acme\ModelBundle\Entity\Dir $dir
     * @return Note
     */
    public function setDir(\Acme\ModelBundle\Entity\Dir $dir = null)
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

    public function toArray() {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent()
        );
    }

    /**
     * Set position
     *
     * @param Acme\ModelBundle\Entity\Position $position
     * @return Note
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
}