<?php
// src/Sdz/BlogBundle/Bigbrother/CensureListener.php

namespace Sdz\BlogBundle\Bigbrother;
use Symfony\Component\Security\Core\User\UserInterface;

class CensureListener
{
  // Liste des id des utilisateurs � surveiller
  protected $liste;
  protected $mailer;

  public function __construct(array $liste, \Swift_Mailer $mailer)
  {
    $this->liste  = $liste;
    $this->mailer = $mailer;
  }

  // M�thode � reine � 1
  protected function sendEmail($message, UserInterface $user)
  {
    $message = \Swift_Message::newInstance()
        ->setSubject("Nouveau message d'un utilisateur surveill�")
        ->setFrom('admin@votresite.com')
        ->setTo('admin@votresite.com')
        ->setBody("L'utilisateur surveill� '".$user->getUsername()."' a post� le message suivant : '".$message."'");

    $this->mailer->send($message);
  }

  // M�thode � reine � 2
  protected function censureMessage($message)
  {
    // Ici, totalement arbitraire :
    $message = str_replace(array('top secret', 'mot interdit'), '', $message);

    return $message;
  }

  // M�thode � technique � de liaison entre l'�v�nement et la fonctionnalit� reine
  public function onMessagePost(MessagePostEvent $event)
  {
    // On active la surveillance si l'auteur du message est dans la liste
    if (in_array($event->getUser()->getId(), $this->liste)) {
      // On envoie un e-mail � l'administrateur
      $this->sendEmail($event->getMessage(), $event->getUser());

      // On censure le message
      $message = $this->censureMessage($event->getMessage());
      // On enregistre le message censur� dans l'event
      $event->setMessage($message);
    }
  }
}