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
     * @ORM\Column(type="integer", length=10)
     */
    protected $pid;

    /**
     * @ORM\Column(name="content", type="text")
     */
    protected $content;

     /**
     * @ORM\ManyToOne(targetEntity="Dir", inversedBy="notes")
     * @ORM\JoinColumn(name="pid", referencedColumnName="id")
     */
    protected $dir;

    /**
     * @ORM\OneToOne(targetEntity="PositionNote", mappedBy="note")
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
        $title = trim(strip_tags($title));
        $this->title = $title ? $title : 'Новая запись';
    
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
     * @return Note
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
     * Set content
     *
     * @param string $content
     * @return Note
     */
    public function setContent($content)
    {
        $this->content = trim(strip_tags($content, '<strong><em><span><p><address><pre><h1><h2><h3><h4><h5><h6><br><ul><ol><li><a><div><code>'));
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
            'pid' => $this->getPid(),
            'content' => $this->getContent(),
            'position' => $this->getPosition()->toArray()
        );
    }

    /**
     * Set position
     *
     * @param Acme\ModelBundle\Entity\PositionNote $position
     * @return Note
     */
    public function setPosition(\Acme\ModelBundle\Entity\PositionNote $position = null)
    {
        $this->position = $position;
        $this->position->setNote($this);
        return $this;
    }

    /**
     * Get position
     *
     * @return Acme\ModelBundle\Entity\PositionNote 
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function getType() {
        return 'note';
    }
}