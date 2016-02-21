<?php
namespace Zumba\Beeper\Tests\Unit\Server;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Zumba\Beeper\Client\BeeperClient;
use Zumba\Beeper\Client\Response\BeeperResponse;
use Zumba\Beeper\Server\Event\DialogNegativeAnswerEvent;
use Zumba\Beeper\Server\Event\DialogPositiveAnswerEvent;
use Zumba\Beeper\Server\EventFactory;

class EventFactoryTest extends \PHPUnit_Framework_TestCase
{
	/** @var EventFactory */
	private $factory;

	public function setUp()
	{
		$this->factory = new EventFactory();
	}

	public function testMissingEventThrows()
	{
		$data = [];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('event');
		$this->factory->buildEvent($data);
	}

	public function testMissingBodyThrows()
	{
		$data = ['event' => 'my event'];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('body');
		$this->factory->buildEvent($data);
	}

	public function testInvalidBodyThrows()
	{
		$data = ['event' => 'my event', 'body' => ''];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('array');
		$this->factory->buildEvent($data);
	}

	public function testMissingMessageThrows()
	{
		$data = ['event' => 'my event', 'body' => []];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('message');
		$this->factory->buildEvent($data);
	}

	public function testInvalidMessageThrows()
	{
		$data = ['event' => 'my event', 'body' => ['message' => 'my message']];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('Message');
		$this->factory->buildEvent($data);
	}

	public function testUnrecognizedEventThrows()
	{
		$data = ['event' => 'my event', 'body' => ['message' => []]];

		self::expectException(\InvalidArgumentException::class);
		self::expectExceptionMessage('event');
		$this->factory->buildEvent($data);
	}

	public function testMessageWithoutIdThrows()
	{
		$data = ['event' => 'dialog.positive_answer', 'body' => ['message' => []]];

		self::expectException(\DomainException::class);
		self::expectExceptionMessage('_id');
		$this->factory->buildEvent($data);
	}

	public function testPositiveDialogAnswer()
	{
		$data = ['event' => 'dialog.positive_answer', 'body' => ['message' => ['_id' => 'my id']]];

		$event = $this->factory->buildEvent($data);

		self::assertInstanceOf(DialogPositiveAnswerEvent::class, $event);
		self::assertTrue($event->isPositive());
		self::assertFalse($event->isNegative());
	}

	public function testNegativeDialogAnswer()
	{
		$data = ['event' => 'dialog.negative_answer', 'body' => ['message' => ['_id' => 'my id']]];

		$event = $this->factory->buildEvent($data);

		self::assertInstanceOf(DialogNegativeAnswerEvent::class, $event);
		self::assertFalse($event->isPositive());
		self::assertTrue($event->isNegative());
	}

}
