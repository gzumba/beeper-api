<?php
namespace Zumba\Beeper\Server;


use Zumba\Beeper\Server\Event\DialogNegativeAnswerEvent;
use Zumba\Beeper\Server\Event\DialogPositiveAnswerEvent;

class CallbackHandler
{
	/**
	 * @var EventFactory
	 */
	private $eventFactory;
	/**
	 * @var BeeperEventHandlerI
	 */
	private $eventHandler;

	public function __construct(EventFactory $eventFactory, BeeperEventHandlerI $eventHandler)
	{
		$this->eventFactory = $eventFactory;
		$this->eventHandler = $eventHandler;
	}

	public function handle(array $data)
	{
		$event = $this->eventFactory->buildEvent($data);

		if( $event instanceof DialogPositiveAnswerEvent )
		{
			return $this->eventHandler->handleDialogPositiveAnswer($event);
		}

		if( $event instanceof DialogNegativeAnswerEvent )
		{
			return $this->eventHandler->handleDialogNegativeAnswer($event);
		}

		return null;
	}
}