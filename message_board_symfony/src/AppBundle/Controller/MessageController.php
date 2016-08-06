<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Message;

class MessageController extends Controller
{
    /**
     * @Route("/messages", name="messages.index", methods={"GET"})
     * @Route("/messages/{messageId}", name="messages.edit", methods={"GET"})
     */
    public function indexAction($messageId = null)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Message::class);

        // user is editing message
        if (!is_null($messageId)) {
            $message = $repository->find($messageId);
        }

        // always get all messages to show on index page
        $messages = $repository->findAll();

        // render
        $viewPath = 'AppBundle:Message:index.html.twig';
        $renderedData = compact('messages', 'message');

        return $this->render($viewPath, $renderedData);
    }

    /**
     * @Route("/messages", name="messages.store", methods={"POST"})
     */
    public function storeAction(Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $message = new Message;

        $this->fillMessageWithRequest($message, $request);

        // create new Message
        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('messages.index');
    }

    /**
     * @Route("/messages/{messageId}", name="messages.update", methods={"PUT"})
     */
    public function updateAction($messageId, Request $request)
    {
        $em      = $this->getDoctrine()->getManager();
        $message = $em->getRepository(Message::class)->find($messageId);

        $this->fillMessageWithRequest($message, $request);

        // update existing Message
        $em->flush();

        return $this->redirectToRoute('messages.index');
    }

    /**
     * @Route("/messages/{messageId}/delete", name="messages.delete", methods={"GET"})
     */
    public function deleteAction($messageId)
    {
        $em      = $this->getDoctrine()->getManager();
        $message = $em->getRepository(Message::class)->find($messageId);

        // delete existing Message
        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute('messages.index');
    }

    /**
     * Helper method to unify Message data assignment.
     */
    protected function fillMessageWithRequest(
        Message $message,
        Request $request
    ) {
        // get data from Request
        $displayName = $request->get('display_name');
        $msgBody     = $request->get('body');

        // set Message data
        $message->setDisplayName($displayName);
        $message->setBody($msgBody);

        return $message;
    }
}
