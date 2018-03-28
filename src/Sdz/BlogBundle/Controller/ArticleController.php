<?php
// src/Sdz/BlogBundle/Controller/ArticleController.php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

public function voirAction($id)
{
  $em      = $this->getDoctrine()->getManager();
  $article = $em->find('Sdz\BlogBundle\Entity\Article', $id);

  if (null !== $article) {
    throw $this->createNotFoundException('L\'article demandé [id='.$id.'] n\'existe pas.');
  }

  // Ici seulement votre vrai code…

  return $this->render('SdzBlogBundle:Blog:voir.html.twig', array('article' => $article));
}