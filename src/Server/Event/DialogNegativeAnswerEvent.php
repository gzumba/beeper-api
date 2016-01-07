<?php
namespace Zumba\Beeper\Server\Event;


class DialogNegativeAnswerEvent extends DialogAnswerEvent
{
	public function isPositive()
	{
		return false;
	}


}