<?php
namespace Zumba\Beeper\Server\Event;


abstract class DialogAnswerEvent extends BeeperEvent
{
	/**
	 * @return bool
	 */
	abstract public function isPositive();

	public function isNegative()
	{
		return !$this->isPositive();
	}

}