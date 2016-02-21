<?php
namespace Zumba\Beeper\Tests\Unit\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Zumba\Beeper\Client\BeeperClient;
use Zumba\Beeper\Client\Response\BeeperResponse;

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
		$this->client->setDefaultSenderId('sender id');
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

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->senderCreate($sender);
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testTextSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('messages', $request->getUri());
			$data = json_decode($request->getBody(), true);
			self::assertEquals('test recipient', $data['phone']);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->textSend('test recipient', 'text to send');
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testDialogSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('dialogs', $request->getUri());
			$data = json_decode($request->getBody(), true);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->dialogSend('test recipient', 'dialog text', 'positive', 'negative');
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testEventSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('events', $request->getUri());
			$data = json_decode($request->getBody(), true);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->eventSend('test recipient', 'event text', new \DateTime(), new \DateTime());
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testTaskSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('tasks', $request->getUri());
			$data = json_decode($request->getBody(), true);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->taskSend('test recipient', 'event text');
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testLocationSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('locations', $request->getUri());
			$data = json_decode($request->getBody(), true);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->locationSend('test recipient', 'event text', 'latitude', 'longitude');
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testImageSend()
	{
		$request_argument = Argument::that( function (RequestInterface $request) {
			self::assertEquals('images', $request->getUri());
			$data = json_decode($request->getBody(), true);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$res = $this->client->imageSend('test recipient', 'image text', 'image');
		self::assertInstanceOf(BeeperResponse::class, $res);
		self::assertTrue($res->isSuccess());
	}

	public function testSendingWithoutDefaultSenderThrows()
	{
		$this->client->setDefaultSenderId(null);

		$this->guzzle->send(Argument::any())->shouldNotBecalled();

		self::expectException(\LogicException::class);
		self::expectExceptionMessage('sender_id');
		$this->client->textSend('recipient', 'text');
	}

	public function testSendingWithoutDefaultUsesArgument()
	{
		$this->client->setDefaultSenderId(null);

		$request_argument = Argument::that( function (RequestInterface $request) {
			$data = json_decode($request->getBody(), true);
			self::assertEquals('my sender id', $data['sender_id']);
			return true;
		});

		$this->guzzle->send($request_argument)->shouldBeCalledTimes(1)->willReturn(new Response());
		$this->client->textSend('recipient', 'text', 'my sender id');
	}

}