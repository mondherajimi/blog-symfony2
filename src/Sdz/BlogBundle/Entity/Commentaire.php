<?php
// src/Sdz/BlogBundle/Entity/Commentaire.php

namespace Sdz\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sdz\BlogBundle\Validator\AntiFlood;

/**
 * Sdz\BlogBundle\Entity\Commentaire
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sdz\BlogBundle\Entity\CommentaireRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Commentaire
{
  /**
   * @ORM\ManyToOne(targetEntity="Sdz\BlogBundle\Entity\Article", inversedBy="commentaires")
   * @ORM\JoinColumn(nullable=false)
   */
  private $article;
  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $auteur
   *
   * @ORM\Column(name="auteur", type="string", length=255)
   */
  private $auteur;

  /**
   * @var text $contenu
   *
   * @ORM\Column(name="contenu", type="text")
   * @AntiFlood(message="Mon message personnalisé")
   */
  private $contenu;

  /**
   * @var datetime $date
   *
   * @ORM\Column(name="date", type="datetime")
   */
  private $date;

  public function __construct()
  {
    $this->date = new \Datetime();
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
   * Set auteur
   *
   * @param string $auteur
   */
  public function setAuteur($auteur)
  {
    $this->auteur = $auteur;
  }

  /**
   * Get auteur
   *
   * @return string
   */
  public function getAuteur()
  {
  return $this->auteur;
  }

  /**
   * Set contenu
   *
   * @param text $contenu
   */
  public function setContenu($contenu)
  {
  $this->contenu = $contenu;
  }

  /**
   * Get contenu
   *
   * @return text
   */
  public function getContenu()
  {
    return $this->contenu;
  }

  /**
   * Set date
   *
   * @param datetime $date
   */
  public function setDate(\Datetime $date)
  {
    $this->date = $date;
  }

  /**
   * Get date
   *
   * @return datetime
   */
  public function getDate()
  {
    return $this->date;
  }

    /**
     * Set article
     *
     * @param \Sdz\BlogBundle\Entity\Article $article
     * @return Commentaire
     */
    public function setArticle(\Sdz\BlogBundle\Entity\Article $article)
    {
        $this->article = $article;
    
        return $this;
    }

    /**
     * Get article
     *
     * @return \Sdz\BlogBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }
	
	/**
   * @ORM\prePersist
   */
  public function increase()
  {
    $nbCommentaires = $this->getArticle()->getNbCommentaires();
    $this->getArticle()->setNbCommentaires($nbCommentaires+1);
  }

  /**
   * @ORM\preRemove
   */
  public function decrease()
  {
    $nbCommentaires = $this->getArticle()->getNbCommentaires();
    $this->getArticle()->setNbCommentaires($nbCommentaires-1);
  }
}