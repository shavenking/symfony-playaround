<?php

namespace Tests\AppBundle\Controller;

use AppBundle\DataFixtures\ORM\LoadTransferData;
use AppBundle\DataFixtures\ORM\LoadUserData;
use AppBundle\Entity\Transfer;
use AppBundle\Test\TestCase;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class TransferControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->loadFixtures([
            LoadUserData::class
        ]);
    }

    public function testIndexReachable()
    {
        $client = static::makeClient($this->getCredentials());

        $client->request('GET', '/transfers.json');
        $this->assertStatusCode(200, $client);

        $client->request('GET', '/transfers');
        $this->assertStatusCode(200, $client);
    }

    public function testTransfersPaginatable()
    {
        $this->loadFixtures([LoadTransferData::class]);

        $credentials = $this->getCredentials();

        // limit 2
        $content = json_decode(
            $this->fetchContent('/transfers.json?limit=2', 'GET', $credentials),
            true
        );
        $this->assertSame(2, count($content['data']));

        // limit 4
        $content = json_decode(
            $this->fetchContent('/transfers.json?limit=4', 'GET', $credentials),
            true
        );
        $this->assertSame(4, count($content['data']));
    }

    public function testStoreTransfer()
    {
        $amount = rand(1, 999);
        $data = json_encode(compact('amount'));
        $client = static::makeClient($this->getCredentials());

        // deposit
        $this->hitDeposit($client, $data);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertStatusCode(200, $client);
        $this->assertSame($amount, $content['data']['amount']);

        // withdrawal
        $this->hitWithdrawal($client, $data);
        $content = json_decode($client->getResponse()->getContent(), true);

        $this->assertStatusCode(200, $client);
        $this->assertSame($amount * -1, $content['data']['amount']);
    }

    public function testTransferFailureAmount()
    {
        $data = json_encode(['amount' => -1]);
        $client = static::makeClient($this->getCredentials());

        // deposit
        $this->hitDeposit($client, $data);
        $this->assertStatusCode(403, $client);

        // withdrawal
        $this->hitWithdrawal($client, $data);
        $this->assertStatusCode(403, $client);
    }

    public function testNotJsonRequest()
    {
        $data = 'amount=1';
        $client = static::makeClient($this->getCredentials());

        // deposit
        $this->hitDeposit($client, $data);
        $this->assertStatusCode(400, $client);

        // withdrawal
        $this->hitWithdrawal($client, $data);
        $this->assertStatusCode(400, $client);
    }

    protected function getCredentials()
    {
        return [
            'username' => 'hugh',
            'password' => 'hugh'
        ];
    }

    protected function hitDeposit($client, $data = [])
    {
        $client->request(
            'POST',
            '/deposits',
            $params = [],
            $files = [],
            $server = [],
            $data
        );
    }

    protected function hitWithdrawal($client, $data = [])
    {
        $client->request(
            'POST',
            '/withdrawals',
            $params = [],
            $files = [],
            $server = [],
            $data
        );
    }
}
