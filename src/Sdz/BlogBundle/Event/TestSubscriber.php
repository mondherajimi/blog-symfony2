<?php
// src/Sdz/BlogBundle/Event/TestSubscriber.php

namespace Sdz\BlogBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class TestSubscriber implements EventSubscriberInterface
{
  // La m�thode de l'interface que l'on doit impl�menter, � d�finir en static
  static public function getSubscribedEvents()
  {
    // On retourne un tableau � nom de l'�v�nement � => � m�thode � ex�cuter �
    return array(
      'kernel.response' => 'onKernelResponse',
      'store.order'     => 'onStoreOrder',
    );
  }

  public function onKernelResponse(FilterResponseEvent $event)
  {
    // �
  }

  public function onStoreOrder(FilterOrderEvent $event)
  {
    // �
  }
}