<?php
// src/Sdz/BlogBundle/ParamConverter/TestParamConverter.php

namespace Sdz\BlogBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class TestParamConverter implements ParamConverterInterface
{
  protected $class;
  protected $repository;

  public function __construct($class, EntityManager $em)
  {
    $this->class      = $class;
    $this->repository = $em->getRepository($class);
  }

  function supports(ConfigurationInterface $configuration)
  {
    // $conf->getClass() contient la classe de l'argument dans la m�thode du contr�leur
    // On teste donc si cette classe correspond � notre classe Site, contenue dans $this->class
    return $configuration->getClass() == $this->class;
  }

  function apply(Request $request, ConfigurationInterface $configuration)
  {
    // On r�cup�re l'entit� Site correspondante
    $site = $this->repository->findOneByHostname($request->getHost());

    // On d�finit ensuite un attribut de requ�te du nom de $conf->getName()
    // et contenant notre entit� Site
    $request->attributes->set($configuration->getName(), $site);

    // On retourne true pour qu'aucun autre ParamConverter ne soit utilis� sur cet argument
    // Je pense notamment au ParamConverter de Doctrine qui risque de vouloir s'appliquer !
    return true;
  }
}