<?php

namespace AppBundle\Controller;

use AppBundle\Form\MerchantType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route(path="/user")
 */
class UserController extends BaseController
{
    /**
     * @Route("/", name="user-index")
     */
    public function indexAction()
    {

    }

    /**
     * @Route("/new", name="user-new")
     * @Method("GET")
     */
    public function newAction()
    {
        $form = $this->createForm(MerchantType::class, null);

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="user-post")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(MerchantType::class, null);
        $form->handleRequest($request);

        try {
            if ($form->isValid())
                $this->getMerchantHandler()->save($form->getData());

            return $this->redirectToRoute('login');
        } catch (\Exception $e) {
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user-edit")
     * @Method("GET")
     */
    public function editAction($id)
    {

    }

    /**
     * @Route("/{id}/edit", name="user-patch")
     * @Method("POST")
     */
    public function patchAction(Request $request, $id)
    {

    }
}