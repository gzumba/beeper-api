<?php
namespace Zumba\Beeper\Server;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Zumba\Beeper\Server\Event\BeeperEvent;
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

		switch($data['event'])
		{
			case BeeperEvent::DIALOG_POSITIVE_ANSWER:
				return new DialogPositiveAnswerEvent($data['body']);
			case BeeperEvent::DIALOG_NEGATIVE_ANSWER:
				return new DialogPositiveAnswerEvent($data['body']);
			default:
				$this->logWarn("Unsupported event {event}", ['event' => $data['event']]);
				throw new \InvalidArgumentException("Unsupported event {$data['event']}");
		}
	}

}
