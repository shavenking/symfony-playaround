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

        if ($request->headers->contains('content-type', 'application/json')) {
            return $this->json([
                'data' => $transfers->getIterator(),
                'meta' => [
                    'firstPage' => $firstPage,
                    'lastPage' => $lastPage,
                    'limit' => $limit,
                    'count' => $transfers->count()
                ]
            ]);
        }

        return $this->render($viewPath, $renderedData);
    }

    /**
     * @Route(
     *     "/deposits",
     *     name="transfers.deposits.store",
     *     methods={"POST"}
     * )
     */
    public function storeDepositAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (is_null($data)) {
            throw new HttpException(400);
        }

        $amount = $data['amount'];

        // validate amount
        // in deposit action, amount has to be positive
        if ($amount <= 0) {
            throw new HttpException(403, 'Amount has to be positve.');
        }

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $transfer = new Transfer($amount);

        $em->transactional(function ($em) use ($user, $amount, $transfer) {
            $repository = $em->getRepository('AppBundle:Transfer');
            $latestBalance = $repository->getLatestBalance($user, $lock = true);

            $transfer->setUser($user);
            $transfer->setBalance($latestBalance + $amount);

            $em->persist($transfer);
        });

        $data = [
            'transfer' => [
                'id' => $transfer->getId(),
                'amount' => $transfer->getAmount(),
                'transfered_at' => $transfer->getTransferedAt()
            ]
        ];

        return $this->json([
            'data' => $data
        ]);
    }

    /**
     * @Route(
     *     "/withdrawals",
     *     name="transfers.withdrawals.store",
     *     methods={"POST"}
     * )
     */
    public function storeWithdrawalAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (is_null($data)) {
            throw new HttpException(400);
        }

        $amount = $data['amount'];

        // validate amount
        // in withdrawal action, amount has to be negative
        if ($amount <= 0) {
            throw new HttpException(403, 'Amount has to be positive.');
        }

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $amount *= -1;
        $transfer = new Transfer($amount);

        $em->transactional(function ($em) use ($user, $amount, $transfer) {
            $repository = $em->getRepository('AppBundle:Transfer');
            $latestBalance = $repository->getLatestBalance($user, $lock = true);

            $transfer->setUser($user);
            $transfer->setBalance($latestBalance + $amount);

            $em->persist($transfer);
        });

        $data = [
            'transfer' => [
                'id' => $transfer->getId(),
                'amount' => $transfer->getAmount(),
                'transfered_at' => $transfer->getTransferedAt()
            ]
        ];

        return $this->json([
            'data' => $data
        ]);
    }
}
