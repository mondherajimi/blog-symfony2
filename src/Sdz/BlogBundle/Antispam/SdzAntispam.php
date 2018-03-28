<?php
// src/Sdz/BlogBundle/Antispam/SdzAntispam.php

namespace Sdz\BlogBundle\Antispam;

class SdzAntispam extends \Twig_Extension
{
 protected $mailer;
  protected $locale;
  protected $nbForSpam;

  public function __construct(\Swift_Mailer $mailer,  $nbForSpam) //$locale,
  {
    $this->mailer    = $mailer;
    // $this->locale    = $locale;
    $this->nbForSpam = (int) $nbForSpam;
	
	// $antispam = new \Sdz\BlogBundle\Antispam\SdzAntispam($doctrine, 3);
    // $antispam->setLocale($locale);
  }
 public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * V�rifie si le texte est un spam ou non
   * Un texte est consid�r� comme spam � partir de 3 liens
   * ou adresses e-mails dans son contenu
   *
   * @param string $text
   */
  public function isSpam($text)
  {
    // On utilise maintenant l'argument $this->nbForSpam et non plus le � 3 � en dur :
    return ($this->countLinks($text) + $this->countMails($text)) >= $this->nbForSpam;
  }

  // � on pourrait �galement utiliser $this->mailer pour pr�venir d'un spam l'administrateur par exemple

  /**
   * Compte les URL de $text
   *
   * @param string $text
   */
  private function countLinks($text)
  {
    preg_match_all(
      '#(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?#i',
      $text,
      $matches);

    return count($matches[0]);
  }

  /**
   * Compte les e-mails de $text
   *
   * @param string $text
   */
  private function countMails($text)
  {
    preg_match_all(
      '#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}#i',
      $text,
      $matches);

    return count($matches[0]);
  }
  
  /*
   * Twig va ex�cuter cette m�thode pour savoir quelle(s) fonction(s) ajoute notre service
   */
  public function getFunctions()
  {
    return array(
      'checkIfSpam' => new \Twig_Function_Method($this, 'isSpam')
    );
  }

  /*
   * La m�thode getName() identifie votre extension Twig, elle est obligatoire
   */
  public function getName()
  {
    return 'SdzAntispam';
  }

}