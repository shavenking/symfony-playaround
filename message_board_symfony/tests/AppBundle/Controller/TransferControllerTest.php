<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Transfer;
use AppBundle\Test\TestCase;

class TransferControllerTest extends TestCase
{
    public function testIndexReachable()
    {
        $credentials = $this->getCredentials();

        $this->client->request('GET', '/transfers', [], [], $credentials);
        $this->assertStatusOk();
    }

    public function testTransfersPaginated()
    {
        $credentials = $this->getCredentials();
        $repository = $this->em->getRepository('AppBundle:Transfer');

        $transfers = $repository->getPaginator($credentials['PHP_AUTH_USER']);
        $crawler = $this->hitIndex([], $credentials);
        $this->assertLessThanOrEqual(
            $transfers->count(),
            $crawler->filter('tr')->count() - 1
        );

        $crawler = $this->hitIndex(['limit' => 2], $credentials);
        $this->assertLessThanOrEqual(
            2,
            $crawler->filter('tr')->count() - 1
        );
    }

    public function testStoreDeposit()
    {
        $data = $this->getRandomData();
        $credentials = $this->getCredentials();

        $this->hitDeposit($data, $credentials);
        $this->assertStatusOk();
    }

    public function testStoreWithdrawal()
    {
        $data = $this->getRandomData();
        $credentials = $this->getCredentials();

        $this->hitWithdrawal($data, $credentials);
        $this->assertStatusOk();
    }

    public function testFailureAmount()
    {
        $data = $this->getFailureData();
        $credentials = $this->getCredentials();

        $this->hitDeposit($data, $credentials);
        $this->assertStatusCodeEquals(403);

        $this->hitWithdrawal($data, $credentials);
        $this->assertStatusCodeEquals(403);
    }

    protected function hitIndex($data = [], $credentials)
    {
        return $this->client->request('GET', '/transfers', $data, [], $credentials);
    }

    protected function hitDeposit($data, $credentials)
    {
        return $this->client->request('POST', '/deposits', $data, [], $credentials);
    }

    protected function hitWithdrawal($data, $credentials)
    {
        return $this->client->request('POST', '/withdrawals', $data, [], $credentials);
    }

    protected function getCredentials()
    {
        return [
            'PHP_AUTH_USER' => 'hugh',
            'PHP_AUTH_PW' => 'hugh'
        ];
    }

    protected function getRandomData()
    {
        return ['amount' => rand(1, 9999)];
    }

    // amount can NOT be negative integer
    protected function getFailureData()
    {
        return ['amount' => -1];
    }
}
