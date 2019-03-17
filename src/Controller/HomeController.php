<?php

namespace App\Controller;

use App\Entity\Article;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeController extends Controller
 	{
 	
/**
* @Route("/")
* @Method({"GET"})
*/

// /**
// * @var Twig\Environment
// */

private $twig;

public function __construct(Environment $twig) 
{

	$this->twig = $twig;
}


/**

* @Route("/", name="article_list")

*/

public function index()
{
$articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
   {

	return new response($this->twig->render('pages/index.html.twig',  array('articles' =>$articles)));
   }
}





/**
* @Route("/pages/new", name="new_article")
* Methods({"GET", "POST"})
*/

 public function new(Request $request) { 

$request = Request::createFromGlobals();

 $article = new Article();

 $form = $this->createFormBuilder($article)

 ->add('title', TextType::class, array('attr' =>
 	array('class'=>'form-control')))

->add('body', TextareaType::class, array(
	'required' => false,
    'attr' =>array('class' => 'form-control')
))

->add('save',SubmitType::class, array(
'label' => 'Create',
'attr' =>array('class' =>'btn btn-primary mt-3')
))
->getForm();

$form->handleRequest($request);

if($form->isSubmitted() && $form->isValid()) {

$article =$form->getData();

$entityManager = $this->getDoctrine()->getManager();
$entityManager->persist($article);
$entityManager->flush();


return $this->redirectToRoute('article_list');

}

return $this->render('pages/new.html.twig', array(
'form' => $form -> createView()
));

 }



/**
* @Route("/article/edit/{id}", name="edit_article")
* Methods({"GET", "POST"})
*/

 public function edit(Request $request, $id) { 

$request = Request::createFromGlobals();

 $article = new Article();
 $article =$this->getDoctrine()->getRepository(Article::class)->find($id);

 $form = $this->createFormBuilder($article)

 ->add('title', TextType::class, array('attr' =>
 	array('class'=>'form-control')))

->add('body', TextareaType::class, array(
	'required' => false,
    'attr' =>array('class' => 'form-control')
))

->add('save',SubmitType::class, array(
'label' => 'Update',
'attr' =>array('class' =>'btn btn-primary mt-3')
))
->getForm();

$form->handleRequest($request);

if($form->isSubmitted() && $form->isValid()) {



$entityManager = $this->getDoctrine()->getManager();
$entityManager->flush();


return $this->redirectToRoute('article_list');

}

return $this->render('pages/edit.html.twig', array(
'form' => $form -> createView()
));

 }


/**

* @Route("/article/{id}", name = "afficher_article")

*/

public function afficher($id) {

	$article =$this->getDoctrine()->getRepository(Article::class)->find($id);

	return $this->render('pages/show.html.twig', array('article' => $article));
}


/**
* @Route("/article/delete/{id}")
* @Method({"DELETE"})
*/

public function delete(Request $request, $id) {

$article =$this->getDoctrine()->getRepository
(Article::class)->find($id);


$entityManager = $this->getDoctrine()->getManager();
$entityManager->remove($article);
$entityManager->flush();

$response = new Response();
$response->send();

}







// /**
// * @Route("article/save")
// */


 // public function save() {

 // $entityManager = $this->getDoctrine()->getManager();

 // $article = new Article();
 // $article->setTitle('Article deux');
 // $article->setBody('Ceci est le corps de l article deux');
 // $entityManager->persist($article);

 // $entityManager->flush();



 // return new response('sauve un article avec l id de'  .$article->getId());
 //  }










 /**

 * @Route("/pages/{slug}")

 */


 public function home($slug)



 {

	return new response($this->twig-> render('/pages/home.html.twig',[

		'title' => ucwords(str_replace('-',' ', $slug)),
	]));
	
   }

 }