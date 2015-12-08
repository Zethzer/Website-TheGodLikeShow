<?php

namespace Zethzer\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Zethzer\NewsBundle\Entity\News;
use Zethzer\NewsBundle\Entity\Image;
use Zethzer\NewsBundle\Entity\Commentaire;
use Zethzer\NewsBundle\Entity\Categorie;

use Zethzer\NewsBundle\Form\Model\Contact;

use Zethzer\NewsBundle\Form\NewsType;
use Zethzer\NewsBundle\Form\ImageType;
use Zethzer\NewsBundle\Form\CategorieType;
use Zethzer\NewsBundle\Form\CommentaireType;
use Zethzer\NewsBundle\Form\ContactType;

class NewsController extends Controller{

    public function indexAction($page){
    	if($page < 1)
    		throw $this->createNotFoundException('La page n\'existe pas.');

        $nbPerPage=$this->container->getParameter('zethzernews.nbPerPage');

        $em=$this->getDoctrine()->getManager();

        $repositoryNews=$em->getRepository('ZethzerNewsBundle:News');

        $news=$repositoryNews->getNews($page, $nbPerPage);
        $newsSlide=$repositoryNews->getNewsSlide();
        $newsImportant=$repositoryNews->getNewsImportant();

        $comCount = $em->getRepository('ZethzerNewsBundle:Commentaire')->getAllComments();

        $CC=array();
        foreach($comCount as $comC){
        	$CC[]=$comC->getArticle()->getId();
        }
    		
        return $this->render('ZethzerNewsBundle:News:index.html.twig', array(
            'news' => $news,
            'newsSlide' => $newsSlide,
            'newsImportant' => $newsImportant,
            'page' => $page,
            'nbPage' => ceil(count($news) / $nbPerPage) ?: 1,
            'comCount' => $CC
        ));
    }

    /**
     * @ParamConverter("news", options={"mapping": {"slug": "slug"}})
     */
    public function displayAction(News $news, Form $form=null){

        $commentaires=new Commentaire;

        if($this->get('security.context')->isGranted("IS_AUTHENTICATED_REMEMBERED") != null){
        	$user=$this->get('security.context')->getToken()->getUser()->getId();
        }
        else{
        	$user=null;
        }

        $em=$this->getDoctrine()->getManager();

        $commentaires = $em->getRepository('ZethzerNewsBundle:Commentaire')->getByNews($news->getId());

        if ($form == null){
            $form=$this->createForm(new CommentaireType, null);
        }

    	return $this->render('ZethzerNewsBundle:News:display.html.twig', array(
            'news' => $news,
            'form' => $form->createView(),
            'commentaires' => $commentaires,
            'userCo' => $user
            ));
    }

    public function addAction(){
        if (!$this->get('security.context')->isGranted('ROLE_NEWSER')) {
            throw new AccessDeniedHttpException('Accès limité aux newsers !');
        }

        $news=new News;
        
        if ($this->getUser()){
            $news->setUser($this->getUser());
        }

        $form=$this->createForm(new NewsType, $news, array('validation_groups' => array('creation', 'Default')));
    
        $request=$this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bind($request);

            if($form->isValid()){
                $em=$this->getDoctrine()->getManager();
                $em->persist($news);
                $em->flush();

        
                foreach ($form->get('addCategories')->getData() as $categories){
                    if($categories != NULL){
                        $news->addCategory($categories);
                        $em->persist($categories);
                    }
                }
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'News ajoutée !');

                return $this->redirect($this->generateUrl('zethzer_news_display', array('slug' => $news->getSlug())));
            }
        }

    	return $this->render('ZethzerNewsBundle:News:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("news", options={"mapping": {"slug": "slug"}})
     */
    public function modifyAction(News $news){
        if (!$this->get('security.context')->isGranted('ROLE_NEWSER')) {
            throw new AccessDeniedHttpException('Accès limité aux newsers !');
        }

        if ($this->getUser()){
            $news->setUserModif($this->getUser());
        }

        $form=$this->createForm(new NewsType, $news);
        $dateEdition=new \DateTime();
    
        $request=$this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bind($request);

            if($form->isValid()){
                $news->setDateEdition($dateEdition);
                $em=$this->getDoctrine()->getManager();
                $em->persist($news);
                $em->flush();

                foreach ($form->get('addCategories')->getData() as $categories){
                    if($categories != NULL){
                        $news->addCategory($categories);
                        $em->persist($categories);
                    }
                }
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'News modifiée !');

                return $this->redirect($this->generateUrl('zethzer_news_display', array('slug' => $news->getSlug())));
            }
        }
    	return $this->render('ZethzerNewsBundle:News:modify.html.twig', array(
            'form' => $form->createView(),
            'new' => $news
            ));
    }

    /**
     * @ParamConverter("news", options={"mapping": {"slug": "slug"}})
     */
    public function deleteAction(News $news){
        if (!$this->get('security.context')->isGranted('ROLE_NEWSER')) {
            throw new AccessDeniedHttpException('Accès limité aux newsers !');
        }

        $form = $this->createFormBuilder()->getForm();
        $request = $this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $em = $this->getDoctrine()->getManager();
                $em->remove($news);
                $em->flush();

                $this->get('session')->getFlashBag()->add('info', 'News supprimée !');

                return $this->redirect($this->generateUrl('zethzer_news_homepage'));
            }
        }
        return $this->render('ZethzerNewsBundle:News:delete.html.twig', array(
            'form' => $form->createView(),
            'new' => $news
        ));
    }

    public function addCommentaireAction(News $news){
        
        $commentaires=new Commentaire;
        
        $commentaires->setArticle($news);
        if ($this->getUser()){
            $commentaires->setUser($this->getUser());
        }

        $form=$this->createForm(new CommentaireType, $commentaires);
        
        $request=$this->getRequest();

        $form->bind($request);
        if ($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($commentaires);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('info', 'Commentaire bien enregistré !');

			return $this->redirect($this->generateUrl('zethzer_news_display', array('slug' => $news->getSlug())).'#comment'.$commentaires->getId());
        }

        $this->get('session')->getFlashBag()->add('error', 'Votre commentaire contient des erreurs.');
        
        return $this->forward('ZethzerNewsBundle:News:display', array(
        	'news' => $news,
        	'form' => $form->createView()
        ));
    }

	public function modifyCommentaireAction(Commentaire $commentaire)
	{
		$form=$this->createForm(new CommentaireType, $commentaire);
		$request=$this->getRequest();
		$dateEdition=new \DateTime();

		if($request->getMethod() == 'POST'){
			$form->bind($request);
			
			if($form->isValid()){
				$em=$this->getDoctrine()->getManager();
				$commentaire->setDateEdition($dateEdition);
				$em->persist($commentaire);
				$em->flush();

				$this->get('session')->getFlashBag()->add('info', 'Commentaire modifié !');

				return $this->redirect($this->generateUrl('zethzer_news_display', array('slug' => $commentaire->getArticle()->getSlug())));
			}
		}

		return $this->render('ZethzerNewsBundle:News:modify_comment.html.twig', array(
			'commentaire' => $commentaire,
			'form' => $form->createView()
		));
	}   	

    public function deleteCommentaireAction(Commentaire $commentaire)
	{
		$form=$this->createFormBuilder()->getForm();
		$request=$this->getRequest();

		if($request->getMethod() == 'POST'){
			$form->bind($request);
			
			if($form->isValid()){
				$em=$this->getDoctrine()->getManager();
				$em->remove($commentaire);
				$em->flush();

				$this->get('session')->getFlashBag()->add('info', 'Commentaire supprimé !');

				return $this->redirect($this->generateUrl('zethzer_news_display', array('slug' => $commentaire->getArticle()->getSlug())));
			}
		}

		return $this->render('ZethzerNewsBundle:News:delete_comment.html.twig', array(
			'commentaire' => $commentaire,
			'form' => $form->createView()
		));
	}

    public function contactAction()
    {
        $form=$this->createForm(new ContactType);
        $request=$this->getRequest();

        if($request->getMethod() == 'POST'){
            $form->bind($request);

            if($form->isValid()){
                $data=$form->getData();

                $message = \Swift_Message::newInstance()
                    ->setContentType('text/html')
                    ->setSubject($data->getSubject())
                    ->setFrom($data->getEmail())
                    ->setTo('setzermail@yahoo.fr')
                    ->setBody($data->getContent());
                
                $this->get('mailer')->send($message);
                
                $this->get('session')->getFlashBag()->add('info', 'Message envoyé ! Merci de nous avoir contacté.');

                return($this->redirect($this->generateUrl('zethzer_news_contact')));
            }
        }

        return $this->render('ZethzerNewsBundle:News:contact.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
