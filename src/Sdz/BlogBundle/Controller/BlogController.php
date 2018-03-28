<?php

// src/Sdz/BlogBundle/Controller/BlogController.php

namespace Sdz\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sdz\BlogBundle\Entity\Article;
use Sdz\BlogBundle\Form\ArticleType;
use Sdz\BlogBundle\Form\ArticleEditType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sdz\BlogBundle\Beta\BetaListener;
use Sdz\BlogBundle\Bigbrother\BigbrotherEvents;
use Sdz\BlogBundle\Bigbrother\MessagePostEvent;
use Sdz\BlogBundle\Event\TestSubscriber;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sdz\BlogBundle\Entity\Site;

class BlogController extends Controller
{
  public function indexAction($page) //, Site $site
  {
  // On récupère le gestionnaire d'évènements
$dispatcher = $this->get('event_dispatcher');

// On instancie notre souscripteur
$subscriber = new TestSubscriber();

// Et on le déclare au gestionnaire d'évènements
$dispatcher->addSubscriber($subscriber);

  // On instancie notre listener
$betaListener = new BetaListener('2014-01-11');

// On récupère le gestionnaire d'évènements, qui heureusement est un service !
$dispatcher = $this->get('event_dispatcher');

// On dit au gestionnaire d'exécuter la méthode onKernelResponse de notre listener
// Lorsque l'évènement kernel.response est déclenché
$dispatcher->addListener('kernel.response', array($betaListener, 'onKernelResponse'));
// $dispatcher->addListener('kernel.response', array($betaListener, 'onKernelResponse'), 2);

    // On ne sait pas combien de pages il y a
    // Mais on sait qu'une page doit être supérieure ou égale à 1
    // Bien sûr pour le moment on ne se sert pas (encore !) de cette variable
    if ($page < 1) {
      // On déclenche une exception NotFoundHttpException 
      // Cela va afficher la page d'erreur 404
      // On pourra la personnaliser plus tard
      throw $this->createNotFoundException('Page inexistante (page = '.$page.')');
    }

    // Pour récupérer la liste de tous les articles : on utilise findAll()
    $articles = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('SdzBlogBundle:Article')
                     // ->findAll();
					 // ->getArticles();
					 ->getArticles(3, $page); // 3 articles par page : c'est totalement arbitraire !

    // L'appel de la vue ne change pas
    return $this->render('SdzBlogBundle:Blog:index.html.twig', array(
      'articles' => $articles,
      'page'       => $page,
      'nombrePage' => ceil(count($articles)/3)
    ));
  }

  public function voirAction(Article $article)
  {
    // On récupère l'EntityManager
    $em = $this->getDoctrine()
               ->getManager();
    
    // Pour récupérer un article unique : on utilise find()
    // $article = $em->getRepository('SdzBlogBundle:Article')
                  // ->find($id);

    // if ($article === null) {
      // throw $this->createNotFoundException('Article[id='.$id.'] inexistant.');
    // }

    // On récupère les articleCompetence pour l'article $article
    // $liste_articleCompetence = $em->getRepository('SdzBlogBundle:ArticleCompetence')
                                  // ->findByArticle($article->getId());

    // Puis modifiez la ligne du render comme ceci, pour prendre en compte les variables :
    // return $this->render('SdzBlogBundle:Blog:voir.html.twig', array(
      // 'article'                 => $article,
      // 'liste_articleCompetence' => $liste_articleCompetence,
      // Pas besoin de passer les commentaires à la vue, on pourra y accéder via {{ article.commentaires }}
      // 'liste_commentaires'   => $article->getCommentaires()
    // ));
	    // À ce stade, la variable $article contient une instance de la classe Article
    // Avec l'id correspondant à l'id contenu dans la route !

    // On récupère ensuite les articleCompetence pour l'article $article
    // On doit le faire à la main pour l'instant, car la relation est unidirectionnelle
    // C'est-à-dire que $article->getArticleCompetences() n'existe pas !
    $listeArticleCompetence = $this->getDoctrine()
                                   ->getManager()
                                   ->getRepository('SdzBlogBundle:ArticleCompetence')
                                   ->findByArticle($article->getId());

	
    return $this->render('SdzBlogBundle:Blog:voir.html.twig', array(
      'article'                 => $article,
      // 'listeArticleCompetence'  => $listeArticleCompetence
    ));
  }

  /*
   * @Secure(roles="ROLE_AUTEUR")
   */
  public function ajouterAction()
  {
  // On teste que l'utilisateur dispose bien du rôle ROLE_AUTEUR
    if (!$this->get('security.context')->isGranted('ROLE_AUTEUR')) {
      // Sinon on déclenche une exception « Accès interdit »
      // throw new AccessDeniedHttpException('Accès limité aux auteurs');
    }
    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :
  
    // if ($this->get('request')->getMethod() == 'POST') {
      // Ici, on s'occupera de la création et de la gestion du formulaire
  
      // $this->get('session')->getFlashBag()->add('info', 'Article bien enregistré');
  
      // Puis on redirige vers la page de visualisation de cet article
      // return $this->redirect( $this->generateUrl('sdzblog_voir', array('id' => 1)) );
    // }
  
    // Si on n'est pas en POST, alors on affiche le formulaire
    // return $this->render('SdzBlogBundle:Blog:ajouter.html.twig');
	
	// On crée un objet Article
  $article = new Article();
   $form = $this->createForm(new ArticleType, $article);
  // Ici, on préremplit avec la date d'aujourd'hui, par exemple
// Cette date sera donc préaffichée dans le formulaire, cela facilite le travail de l'utilisateur
// $article->setDate(new \Datetime());

// $article = $this->getDoctrine()
                // ->getRepository('Sdz\BlogBundle\Entity\Article')
                // ->find($id);
  // On crée le FormBuilder grâce à la méthode du contrôleur
  // $formBuilder = $this->createFormBuilder($article);

  // On ajoute les champs de l'entité que l'on veut à notre formulaire
  // $formBuilder
    // ->add('date',        'date')
    // ->add('titre',       'text')
    // ->add('contenu',     'textarea')
    // ->add('auteur',      'text')
    // ->add('publication', 'checkbox');
  // Pour l'instant, pas de commentaires, catégories, etc., on les gérera plus tard

  // À partir du formBuilder, on génère le formulaire
  // $form = $formBuilder->getForm();
      // J'ai raccourci cette partie, car c'est plus rapide à écrire !
    // $form = $this->createFormBuilder($article)
                 // ->add('date',        'date')
                 // ->add('titre',       'text')
                 // ->add('contenu',     'textarea')
                 // ->add('auteur',      'text')
                 // ->add('publication', 'checkbox', array('required' => true))
                 // ->getForm();
  // On récupère la requête
    $request = $this->get('request');

    // On vérifie qu'elle est de type POST
    if ($request->getMethod() == 'POST') {
      // On fait le lien Requête <-> Formulaire
      // À partir de maintenant, la variable $article contient les valeurs entrées dans le formulaire par le visiteur
      $form->bind($request);

      // On vérifie que les valeurs entrées sont correctes
      // (Nous verrons la validation des objets en détail dans le prochain chapitre)
      if ($form->isValid()) {
	   // Ici : On traite manuellement le fichier uploadé
        // $article->getImage()->upload();
		
		// On crée l'évènement avec ses 2 arguments
      $event = new MessagePostEvent($article->getContenu(), $article->getUser());

      // On déclenche l'évènement
      $this->get('event_dispatcher')
           ->dispatch(BigbrotherEvents::onMessagePost, $event);

      // On récupère ce qui a été modifié par le ou les listeners, ici le message
      $article->setContenu($event->getMessage());

	  
        // On l'enregistre notre objet $article dans la base de données
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
		// $em->persist($article->getImage())
        $em->flush();
		
		    // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien ajouté');
		
		 // On redirige vers la page de visualisation de l'article nouvellement créé
        return $this->redirect($this->generateUrl('sdzblog_voir', array('id' => $article->getId())));
		// return $this->redirect($this->generateUrl('sdzblog_accueil', array('page' => 1)));
      }
    }

    // À ce stade :
    // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
    // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau

  // On passe la méthode createView() du formulaire à la vue afin qu'elle puisse afficher le formulaire toute seule
  return $this->render('SdzBlogBundle:Blog:ajouter.html.twig', array(
    'form' => $form->createView(),
  ));
  }

  /**
   * @Secure(roles="ROLE_AUTEUR")
   */
  public function modifierAction(Article $article)
  {
    // On récupère l'EntityManager
    // $em = $this->getDoctrine()
               // ->getEntityManager();

    // On récupère l'entité correspondant à l'id $id
    // $article = $em->getRepository('SdzBlogBundle:Article')
                  // ->find($id);

    // Si l'article n'existe pas, on affiche une erreur 404
    // if ($article == null) {
      // throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
    // }
 // On utiliser le ArticleEditType
    $form = $this->createForm(new ArticleEditType(), $article);

    $request = $this->getRequest();

    if ($request->getMethod() == 'POST') {
      $form->bind($request);

      if ($form->isValid()) {
        // On enregistre l'article
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien modifié');
		
		return $this->redirect($this->generateUrl('sdzblog_voir', array('id' => $article->getId())));
      }
    }

    return $this->render('SdzBlogBundle:Blog:modifier.html.twig', array(
      'form'    => $form->createView(),
      'article' => $article
    ));
  }

    /**
   * @Secure(roles="ROLE_ADMIN")
   */
  public function supprimerAction(Article $article)
  {
    // On récupère l'EntityManager
    // $em = $this->getDoctrine()
               // ->getEntityManager();

    // On récupère l'entité correspondant à l'id $id
    // $article = $em->getRepository('SdzBlogBundle:Article')
                  // ->find($id);
    
    // Si l'article n'existe pas, on affiche une erreur 404
    // if ($article == null) {
      // throw $this->createNotFoundException('Article[id='.$id.'] inexistant');
    // }
	 // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'article contre cette faille
    $form = $this->createFormBuilder()->getForm();

    $request = $this->getRequest();

    // if ($this->get('request')->getMethod() == 'POST') {
      if ($request->getMethod() == 'POST') {
      $form->bind($request);

      if ($form->isValid()) {
        // On supprime l'article
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Article bien supprimé');

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('sdzblog_accueil', array('page' => 1)));
      }
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('SdzBlogBundle:Blog:supprimer.html.twig', array(
      'article' => $article,
      'form'    => $form->createView()
    ));
  }

   public function ajouterCommentaireAction(Article $article)
  {
    $commentaire = new Commentaire;
    $commentaire->setArticle($article);
    $commentaire->setIp($this->getRequest()->server->get('REMOTE_ADDR'));

    $form = $this->getCommentaireForm($article, $commentaire);

    $request = $this->getRequest();

    // Avec la route que l'on a, nous sommes forcément en POST ici, pas besoin de le retester
    $form->bind($request);
    if ($form->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($commentaire);
      $em->flush();

      $this->get('session')->getFlashBag()->add('info', 'Commentaire bien enregistré !');

      // On redirige vers la page de l'article, avec une ancre vers le nouveau commentaire
      return $this->redirect($this->generateUrl('sdzblog_voir', array('slug' => $article->getSlug())).'#comment'.$commentaire->getId());
    }

    $this->get('session')->getFlashBag()->add('error', 'Votre formulaire contient des erreurs');

    // On réaffiche le formulaire sans redirection (sinon on perd les informations du formulaire)
    return $this->forward('SdzBlogBundle:Blog:voir', array(
      'article' => $article,
      'form'    => $form
    ));
  }

  /**
   * @Secure(roles="ROLE_ADMIN")
   */
  public function supprimerCommentaireAction(Commentaire $commentaire)
  {
    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'article contre cette faille
    $form = $this->createFormBuilder()->getForm();

    $request = $this->getRequest();
    if ($request->getMethod() == 'POST') {
      $form->bind($request);

      if ($form->isValid()) { // Ici, isValid ne vérifie donc que le CSRF
        // On supprime l'article
        $em = $this->getDoctrine()->getManager();
        $em->remove($commentaire);
        $em->flush();

        // On définit un message flash
        $this->get('session')->getFlashBag()->add('info', 'Commentaire bien supprimé');

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('sdzblog_voir', array('slug' => $commentaire->getArticle()->getSlug())));
      }
    }

    // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
    return $this->render('SdzBlogBundle:Blog:supprimerCommentaire.html.twig', array(
      'commentaire' => $commentaire,
      'form'        => $form->createView()
    ));
  }
  
  public function menuAction($nombre)
  {
    $liste = $this->getDoctrine()
                  ->getManager()
                  ->getRepository('SdzBlogBundle:Article')
                  ->findBy(
                    array(),          // Pas de critère
                    array('date' => 'desc'), // On trie par date décroissante
                    $nombre,         // On sélectionne $nombre articles
                    0                // À partir du premier
                  );

    return $this->render('SdzBlogBundle:Blog:menu.html.twig', array(
      'liste_articles' => $liste // C'est ici tout l'intérêt : le contrôleur passe les variables nécessaires au template !
    ));
  }
  
   public function testAction()
  {
    $article = new Article;
        
    $article->setDate(new \Datetime());  // Champ « date » OK
    $article->setTitre('abc');           // Champ « titre » incorrect : moins de 10 caractères
    //$article->setContenu('blabla');    // Champ « contenu » incorrect : on ne le définit pas
    $article->setAuteur('A');            // Champ « auteur » incorrect : moins de 2 caractères
        
    // On récupère le service validator
    $validator = $this->get('validator');
        
    // On déclenche la validation
    $liste_erreurs = $validator->validate($article);

    // Si le tableau n'est pas vide, on affiche les erreurs
    if(count($liste_erreurs) > 0) {
      return new Response(print_r($liste_erreurs, true));
    } else {
      return new Response("L'article est valide !");
    }
  }
  
   public function traductionAction($name)
  {
    return $this->render('SdzBlogBundle:Blog:traduction.html.twig', array(
      'name' => $name,
	  'nombre' => 3,
	  'date' => 2014-01-11,
	  'dateFormat' => 'full',
	  'timeFormat' => 'full',
	   'locale' => 'fr'
    ));
	
	// On récupère le service translator
$translator = $this->get('translator');

// Pour traduire dans la locale de l'utilisateur :
$texteTraduit = $translator->trans('Mon message à inscrire dans les logs');

$translator->transchoice($nombre, 'article.nombre');

// Texte simple
// $translator->trans('maChaîne',  array('%placeholder%' => $placeholderValue) , 'domaine', $locale);

// Texte avec gestion de pluriels
// $translator->transchoice($count, 'maChaîne',  array('%placeholder%' => $placeholderValue) , 'domaine', $locale)
  }
  
/**
 * @ParamConverter("date", options={"format": "Y-m-d"})
 */
  public function feedAction()
  {
    $articles = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('SdzBlogBundle:Article')
                     ->getArticles(10, 1);

    $lastArticle = current($articles->getIterator());

    return $this->render('SdzBlogBundle:Blog:feed.xml.twig', array(
      'articles'  => $articles,
      'buildDate' => $lastArticle->getDate()
    ));
  }

  // Méthodes protégées :

  /**
   * Retourne le formulaire d'ajout d'un commentaire
   * @param Article $article
   * @return Form
   */
  protected function getCommentaireForm(Article $article, Commentaire $commentaire = null)
  {
    if (null === $commentaire) {
      $commentaire = new Commentaire;
    }

    // Si l'utilisateur courant est identifié, on l'ajoute au commentaire
    if (null !== $this->getUser()) {
        $commentaire->setUser($this->getUser());
    }

    return $this->createForm(new CommentaireType(), $commentaire);
  }
public function voirListeAction(\Datetime $date)
{
return new Response(print_r($date, true));
}
}