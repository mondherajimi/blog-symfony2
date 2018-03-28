<?php
// src/Sdz/BlogBundle/DataFixtures/ORM/Competences.php

namespace Sdz\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sdz\BlogBundle\Entity\Competence;

class Competences implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de comp�tences � ajouter
    $noms = array('Doctrine', 'Formulaire', 'Twig');

    foreach($noms as $i => $nom)
    {
      // On cr�e la comp�tence
      $liste_competences[$i] = new Competence();
      $liste_competences[$i]->setNom($nom);

      // On la persiste
      $manager->persist($liste_competences[$i]);
    }                            

    // On d�clenche l'enregistrement
    $manager->flush();
  }
}