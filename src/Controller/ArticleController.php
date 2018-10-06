<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController {
	/**
	 * @Route("/list", name="article_index", methods="GET")
	 */
	public function index(ArticleRepository $articleRepository): Response {
		$articles = $articleRepository->findAll ();
		return $this->render ( 'article/index.html.twig', [ 
				'articles' => $articleRepository->findAll () 
		] );
	}
	
	/**
	 * @Route("/listapi", name="article_index_api", methods="GET")
	 */
	public function indexapi(ArticleRepository $articleRepository): Response {
		$articles = $articleRepository->findAll ();
		// faire DTO
		$_article = [ ];
		$_articles = [ ];
		foreach ( $articles as $article ) {
			$_article ['name'] = $article->getName ();
			$_article ['description'] = $article->getDescription ();
			$_articles [] = $_article;
		}
		return new JsonResponse ( $_articles );
		// return $this->render('article/index.html.twig', ['articles' => $articleRepository->findAll()]);
	}
	
	/**
	 * @Route("/new", name="article_new", methods="GET|POST")
	 */
	public function new(Request $request): Response {
		$article = new Article ();
		$form = $this->createForm ( ArticleType::class, $article );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$em = $this->getDoctrine ()->getManager ();
			$em->persist ( $article );
			$em->flush ();
			
			return $this->redirectToRoute ( 'article_index' );
		}
		
		return $this->render ( 'article/new.html.twig', [ 
				'article' => $article,
				'form' => $form->createView () 
		] );
	}
	
	/**
	 * @Route("/newapi", name="article_new_api", methods="GET|POST")
	 */
	public function newapi(Request $request): Response {
		$article = new Article ();
		$body = $request->getContent ();
		$data = json_decode ( $body, true );
		
		$form = $this->createForm ( ArticleType::class, $article );
		$form->submit ( $data );
		$Validator = $this->get ( 'validator' );
		/*
		 * $errors = $Validator->validate(article);
		 *
		 * if(count($errors) > 0){
		 * $errorsString = (string) $errors;
		 * return new JsonResponse($errorsString);
		 * }
		 */
		$em = $this->getDoctrine ()->getManager ();
		$em->persist ( $article );
		$em->flush ();
		
		return $this->redirectToRoute ( 'article_index_api' );
	}
	
	/**
	 * @Route("/{id}", name="article_show", methods="GET")
	 */
	public function show(Article $article): Response {
		return $this->render ( 'article/show.html.twig', [ 
				'article' => $article 
		] );
	}
	
	/**
	 * @Route("/showidapi/{id}", name="article_show_api", methods="GET")
	 */
	public function showidapi(Article $article): Response {
		$_article ['name'] = $article->getName ();
		$_article ['description'] = $article->getDescription ();
		return new JsonResponse ( $_article );
	}
	
	/**
	 * @Route("/{id}/edit", name="article_edit", methods="GET|POST")
	 */
	public function edit(Request $request, Article $article): Response {
		$form = $this->createForm ( ArticleType::class, $article );
		$form->handleRequest ( $request );
		
		if ($form->isSubmitted () && $form->isValid ()) {
			$this->getDoctrine ()->getManager ()->flush ();
			
			return $this->redirectToRoute ( 'article_edit', [ 
					'id' => $article->getId () 
			] );
		}
		
		return $this->render ( 'article/edit.html.twig', [ 
				'article' => $article,
				'form' => $form->createView () 
		] );
	}
	
	/**
	 * @Route("/{id}/editapi", name="article_edit", methods="POST")
	 */
	public function editapi(Request $request, Article $article): Response {
		$body = $request->getContent ();
		$data = json_decode ( $body, true );
		
		$form = $this->createForm ( ArticleType::class, $article );
		$form->submit ( $data );
		//$Validator = $this->get ( 'validator' );
		/*
		 * $errors = $Validator->validate(article);
		 *
		 * if(count($errors) > 0){
		 * $errorsString = (string) $errors;
		 * return new JsonResponse($errorsString);
		 * }
		 */
		$em = $this->getDoctrine ()->getManager ();
		$em->flush ();
		
		return $this->redirectToRoute ( 'article_index_api' );
	}
	
	/**
	 * @Route("/{id}", name="article_delete", methods="DELETE")
	 */
	public function delete(Request $request, Article $article): Response {
		if ($this->isCsrfTokenValid ( 'delete' . $article->getId (), $request->request->get ( '_token' ) )) {
			$em = $this->getDoctrine ()->getManager ();
			$em->remove ( $article );
			$em->flush ();
		}
		
		return $this->redirectToRoute ( 'article_index' );
	}
	
	/**
	 * @Route("/{id}/delete", name="article_delete_api", methods="DELETE")
	 */
	public function deleteapi(Request $request, Article $article): Response {
			$em = $this->getDoctrine ()->getManager ();
			$em->remove ( $article );
			$em->flush ();
	
		return $this->redirectToRoute ( 'article_index' );
	}
	
	
}
