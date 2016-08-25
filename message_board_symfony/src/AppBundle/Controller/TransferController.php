<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Transfer;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TransferController extends Controller
{
    /**
     * @Route("/transfers", name="transfers.index", methods={"GET"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Transfer');

        $username = $this->getUser()->getUsername();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $transfers = $repository->getPaginator($username, $page, $limit);

        $viewPath = 'AppBundle:Transfer:index.html.twig';
        $firstPage = 1;
        $lastPage = ceil($transfers->count() / $limit);
        $renderedData = compact('transfers', 'firstPage', 'lastPage', 'limit');

        return $this->render($viewPath, $renderedData);
    }
}
