<?php
// src/Sdz/UserBundle/DataFixtures/ORM/Users.php

namespace Sdz\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sdz\UserBundle\Entity\User;

class Users implements FixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Les noms d'utilisateurs � cr�er
    $noms = array('winzou', 'John', 'Talus');

    foreach ($noms as $i => $nom) {
      // On cr�e l'utilisateur
      $users[$i] = new User;

      // Le nom d'utilisateur et le mot de passe sont identiques
      $users[$i]->setUsername($nom);
      $users[$i]->setPassword($nom);

      // Le sel et les r�les sont vides pour l'instant
      $users[$i]->setSalt('');
      $users[$i]->setRoles(array());

      // On le persiste
      $manager->persist($users[$i]);
    }

    // On d�clenche l'enregistrement
    $manager->flush();
  }
}