<?php
// src/Sdz/BlogBundle/Beta/BetaListener.php

namespace Sdz\BlogBundle\Beta;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class BetaListener
{
  // La date de fin de la version b�ta :
  // - Avant cette date, on affichera un compte � rebours (J-3 par exemple)
  // - Apr�s cette date, on n'affichera plus le � b�ta �
  protected $dateFin;

  public function __construct($dateFin)
  {
    $this->dateFin = new \Datetime($dateFin);
  }

  // M�thode pour ajouter le � b�ta � � une r�ponse
  protected function displayBeta(Response $response, $joursRestant)
  {
    $content = $response->getContent();
  
    // Code � rajouter
    $html = '<span style="color: red; font-size: 0.5em;"> - Beta J-'.(int) $joursRestant.' !</span>';

    // Insertion du code dans la page, dans le <h1> du header
    $content = preg_replace('#<h1>(.*?)</h1>#iU',
                            '<h1>$1'.$html.'</h1>',
                            $content,
                            1);
  
    // Modification du contenu dans la r�ponse
    $response->setContent($content);
  
    return $response;
  }
  
  public function onKernelResponse(FilterResponseEvent $event)
  {
    // On teste si la requ�te est bien la requ�te principale
    // if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
      // return;
    // }

    // On r�cup�re la r�ponse que le noyau a ins�r�e dans l'�v�nement
    $response = $event->getResponse();
    
     $joursRestant = $this->dateFin->diff(new \Datetime())->days;

    if ($joursRestant > 0) {
      // On utilise notre m�thode � reine �
      $response = $this->displayBeta($event->getResponse(), $joursRestant);
    }
    
    // Puis on ins�re la r�ponse modifi�e dans l'�v�nement
    $event->setResponse($response);
	// On stoppe la propagation de l'�v�nement en cours (ici, kernel.response)
    $event->stopPropagation();
  }
  
  public function onKernelController(FilterControllerEvent $event)
{
  // Vous pouvez r�cup�rer le contr�leur que le noyau avait l'intention d'ex�cuter
  $controller = $event->getController();

  // Ici vous pouvez modifier la variable $controller, etc.
  // $controller doit �tre de type PHP callable

  // Si vous avez modifi� le contr�leur, pr�venez le noyau qu'il faut ex�cuter le v�tre :
  $event->setController($controller);
}

public function onKernelView(GetResponseForControllerResultEvent $event)
{
  // R�cup�rez le retour du contr�leur (ce qu'il a mis dans son � return �)
  $val = $event->getControllerResult();

  // Cr�ez une nouvelle r�ponse
  $response = new Response();

  // Construisez votre r�ponse comme bon vous semble�

  // D�finissez la r�ponse dans l'�v�nement, qui la donnera au noyau qui, finalement, l'affichera
  $event->setResponse($response);
}
}