<?php
namespace Zumba\Beeper\Tests\Unit;

use GuzzleHttp\Client;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Zumba\Beeper\Client\BeeperClient;

class BeeperClientTest extends \PHPUnit_Framework_TestCase
{
	/** @var  BeeperClient */
	private $client;
	/** @var Client|ObjectProphecy */
	private $guzzle;

	public function setUp()
	{
		$this->guzzle = $this->prophesize(Client::class);
		$this->client = new BeeperClient($this->guzzle->reveal(), 'app id', 'api key');
	}

	public function testSenderCreate()
	{
		$sender = ['name' => 'test name'];
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('senders', $request->getUri());
			$data = json_decode($request->getBody(), true);
			self::assertEquals('test name', $data['name']);
			self::assertContains('application/json', $request->getHeader('Content-Type'));
			self::assertContains('app id', $request->getHeader('X-Beeper-Application-Id'));
			self::assertContains('api key', $request->getHeader('X-Beeper-REST-API-Key'));
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1);
		$res = $this->client->senderCreate($sender);
	}
}