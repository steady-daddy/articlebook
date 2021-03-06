<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 */
class UserController extends Controller
{

	/**
	 * @Route("/{username}", name="user_profile")
	 * @Template()
	 */

	public function profileAction($username){

		/** @var \UserBundle\Entity\User $user */
		$user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(array('username' => strip_tags($username)));
		$articles = [];
		$ctr = 0;

		foreach ($user->getArticles() as $article) {
			/** @var \ArticlesBundle\Entity\Article $article */
			$articles[$ctr]['link'] = $article->getLink();
			$articles[$ctr]['title'] = $article->getTitle();
			$articles[$ctr]['description'] = $article->getDescription();
			$articles[$ctr]['imageUrl'] = $article->getImageUrl();
			$ctr++;
		}

		return array(
			'user' => $user,
			'articles' => $articles
		);
	}

	/**
     * Lists all user entities.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('UserBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('UserBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('user/new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

}
