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
     * @Route(
     *     "/transfers.{_format}",
     *     defaults={"_format": "html"},
     *     requirements={"_format": "html|json"},
     *     name="transfers.index",
     *     methods={"GET"}
     * )
     */
    public function indexAction(Request $request, $_format)
    {
        $username = $this->getUser()->getUsername();
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $transfers = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:Transfer')
            ->getPaginator($username, $page, $limit);

        // pagination metadata
        $firstPage = 1;
        $lastPage = ceil($transfers->count() / $limit);

        // response json data if requested
        if ('json' === $_format) {
            return $this->json([
                'data' => $transfers->getIterator(),
                'meta' => [
                    'firstPage' => $firstPage,
                    'lastPage' => $lastPage,
                    'page' => $page,
                    'limit' => $limit,
                    'count' => $transfers->count()
                ]
            ]);
        }

        $viewPath = 'AppBundle:Transfer:index.html.twig';
        $viewData = compact('transfers', 'firstPage', 'lastPage', 'limit');

        return $this->render($viewPath, $viewData);
    }

    /**
     * @Route(
     *     "/deposits.{_format}",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     name="transfers.deposits.store",
     *     methods={"POST"}
     * )
     */
    public function storeDepositAction(Request $request)
    {
        $amount = $this->getAmountFromRequest($request);
        $user = $this->getUser();
        $transfer = $this->persistTransfer($user, $amount);

        return $this->json([
            'data' => $transfer
        ]);
    }

    /**
     * @Route(
     *     "/withdrawals.{_format}",
     *     defaults={"_format": "json"},
     *     requirements={"_format": "json"},
     *     name="transfers.withdrawals.store",
     *     methods={"POST"}
     * )
     */
    public function storeWithdrawalAction(Request $request)
    {
        $amount = $this->getAmountFromRequest($request) * -1;
        $user = $this->getUser();
        $transfer = $this->persistTransfer($user, $amount);

        return $this->json([
            'data' => $transfer
        ]);
    }

    protected function getAmountFromRequest(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if (is_null($data)) {
            throw new HttpException(400, 'Request content should be JSON.');
        }

        $amount = $data['amount'];

        if ($amount <= 0) {
            throw new HttpException(403, 'Amount has to be positive.');
        }

        return $amount;
    }

    protected function persistTransfer($user, $amount)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Transfer');

        $transfer = new Transfer;

        // start transactional and
        // lock the latest balance
        $em->transactional(function ($em) use (
            $user,
            $amount,
            $transfer,
            $repository
        ) {
            $latestBalance = $repository->getLatestBalance($user, $lock = true);

            $transfer->setAmount($amount);
            $transfer->setUser($user);
            $transfer->setBalance($latestBalance + $amount);

            $em->persist($transfer);
        });

        return $transfer;
    }
}
