<?php
// src/Sdz/BlogBundle/Event/TestSubscriber.php

namespace Sdz\BlogBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class TestSubscriber implements EventSubscriberInterface
{
  // La méthode de l'interface que l'on doit implémenter, à définir en static
  static public function getSubscribedEvents()
  {
    // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
    return array(
      'kernel.response' => 'onKernelResponse',
      'store.order'     => 'onStoreOrder',
    );
  }

  public function onKernelResponse(FilterResponseEvent $event)
  {
    // …
  }

  public function onStoreOrder(FilterOrderEvent $event)
  {
    // …
  }
}