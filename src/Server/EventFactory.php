<?php
namespace Zumba\Beeper\Server;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Zumba\Beeper\Server\Event\BeeperEvent;
use Zumba\Beeper\Server\Event\DialogNegativeAnswerEvent;
use Zumba\Beeper\Server\Event\DialogPositiveAnswerEvent;
use Zumba\Log\LoggingI;
use Zumba\Log\LoggingTrait;

class EventFactory implements LoggerAwareInterface
{
	use LoggingTrait;

	public function buildEvent(array $data)
	{
		if( !array_key_exists('event', $data) )
		{
			throw new \InvalidArgumentException("No event in data");
		}

		if( !array_key_exists('body', $data) )
		{
			throw new \InvalidArgumentException("No body in data");
		}

		if( !array_key_exists('message', $data['body']) )
		{
			throw new \InvalidArgumentException("No message in data body");
		}

		$message = $data['body']['message'];

		switch($data['event'])
		{
			case BeeperEvent::DIALOG_POSITIVE_ANSWER:
				return new DialogPositiveAnswerEvent($message);
			case BeeperEvent::DIALOG_NEGATIVE_ANSWER:
				return new DialogNegativeAnswerEvent($message);
			default:
				$this->logWarn("Unsupported event {event}", ['event' => $data['event']]);
				throw new \InvalidArgumentException("Unsupported event {$data['event']}");
		}
	}

}
