<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use AppBundle\Event\MessageRepliedEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends Controller
{
    /**
     * @Route("/messages", name="messages.index", methods={"GET"})
     * @Route("/messages/{messageId}/edit", name="messages.edit", methods={"GET"})
     * @Route("/messages/{messageId}/reply", name="messages.reply", methods={"GET"})
     */
    public function indexAction($messageId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Message');

        // user is editing or replying message
        if (!is_null($messageId)) {
            $message = $repository->find($messageId);
        }

        // select top level Messages
        $messages = $repository->findAllTopLevel();

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
        $em = $this->getDoctrine()->getManager();
        $message = new Message;

        $this->fillMessageWithRequest($message, $request);

        // create new Message
        $em->persist($message);
        $em->flush();

        // user is replying another message
        if (!is_null($message->getParent())) {
            $parent = $message->getParent();
            $child = $message;
            $event = new MessageRepliedEvent($parent, $child);
            $dispatcher = $this->get('event_dispatcher');

            $dispatcher->dispatch('app.message_replied', $event);
        }

        return $this->redirectToRoute('messages.index');
    }

    /**
     * @Route("/messages/{messageId}", name="messages.update", methods={"PUT"})
     */
    public function updateAction($messageId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('AppBundle:Message')->find($messageId);

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
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository('AppBundle:Message')->find($messageId);

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
        $msgBody = $request->get('body');
        $parentId = $request->get('parent_id');

        // set Message data
        $message->setDisplayName($displayName);
        $message->setBody($msgBody);

        // set parent if provided
        if (!is_null($parentId)) {
            $em = $this->getDoctrine()->getManager();
            $parent = $em->getRepository('AppBundle:Message')->find($parentId);

            $message->setParent($parent);
        }

        return $message;
    }
}
