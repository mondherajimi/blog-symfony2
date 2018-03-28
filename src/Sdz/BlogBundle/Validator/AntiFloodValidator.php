<?php
// src/Sdz/BlogBundle/Validator/AntiFloodValidator.php

namespace Sdz\BlogBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class AntiFloodValidator extends ConstraintValidator
{
  private $request;
  private $em;

  // Les arguments d�clar�s dans la d�finition du service arrivent au constructeur
  // On doit les enregistrer dans l'objet pour pouvoir s'en resservir dans la m�thode validate()
  public function __construct(Request $request, EntityManager $em)
  {
    $this->request = $request;
    $this->em      = $em;
  }
  
  public function validate($value, Constraint $constraint)
  {
    // On r�cup�re l'IP de celui qui poste
    $ip = $this->request->server->get('REMOTE_ADDR');

    // On v�rifie si cette IP a d�j� post� un message il y a moins de 15 secondes
    $isFlood = $this->em->getRepository('SdzBlogBundle:Commentaire')
                        ->isFlood($ip, 15); // Bien entendu, il faudrait �crire cette m�thode isFlood, c'est pour l'exemple
    // Pour l'instant, on consid�re comme flood tout message de moins de 3 caract�res
    if (strlen($value) < 3 && $isFlood) {
      // C'est cette ligne qui d�clenche l'erreur pour le formulaire, avec en argument le message
      $this->context->addViolation($constraint->message);
	  //$this->context->addViolation($constraint->message, array('%string%' => $value));
    }
  }
}