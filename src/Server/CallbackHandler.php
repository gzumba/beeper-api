<?php
namespace Zumba\Beeper\Server;


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
	}
}