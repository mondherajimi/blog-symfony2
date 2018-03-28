<?php
// src/Sdz/BlogBundle/Beta/BetaListener.php

namespace Sdz\BlogBundle\Beta;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class BetaListener
{
  // La date de fin de la version bêta :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « bêta »
  protected $dateFin;

  public function __construct($dateFin)
  {
    $this->dateFin = new \Datetime($dateFin);
  }

  // Méthode pour ajouter le « bêta » à une réponse
  protected function displayBeta(Response $response, $joursRestant)
  {
    $content = $response->getContent();
  
    // Code à rajouter
    $html = '<span style="color: red; font-size: 0.5em;"> - Beta J-'.(int) $joursRestant.' !</span>';

    // Insertion du code dans la page, dans le <h1> du header
    $content = preg_replace('#<h1>(.*?)</h1>#iU',
                            '<h1>$1'.$html.'</h1>',
                            $content,
                            1);
  
    // Modification du contenu dans la réponse
    $response->setContent($content);
  
    return $response;
  }
  
  public function onKernelResponse(FilterResponseEvent $event)
  {
    // On teste si la requête est bien la requête principale
    // if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
      // return;
    // }

    // On récupère la réponse que le noyau a insérée dans l'évènement
    $response = $event->getResponse();
    
     $joursRestant = $this->dateFin->diff(new \Datetime())->days;

    if ($joursRestant > 0) {
      // On utilise notre méthode « reine »
      $response = $this->displayBeta($event->getResponse(), $joursRestant);
    }
    
    // Puis on insère la réponse modifiée dans l'évènement
    $event->setResponse($response);
	// On stoppe la propagation de l'évènement en cours (ici, kernel.response)
    $event->stopPropagation();
  }
  
  public function onKernelController(FilterControllerEvent $event)
{
  // Vous pouvez récupérer le contrôleur que le noyau avait l'intention d'exécuter
  $controller = $event->getController();

  // Ici vous pouvez modifier la variable $controller, etc.
  // $controller doit être de type PHP callable

  // Si vous avez modifié le contrôleur, prévenez le noyau qu'il faut exécuter le vôtre :
  $event->setController($controller);
}

public function onKernelView(GetResponseForControllerResultEvent $event)
{
  // Récupérez le retour du contrôleur (ce qu'il a mis dans son « return »)
  $val = $event->getControllerResult();

  // Créez une nouvelle réponse
  $response = new Response();

  // Construisez votre réponse comme bon vous semble…

  // Définissez la réponse dans l'évènement, qui la donnera au noyau qui, finalement, l'affichera
  $event->setResponse($response);
}
}