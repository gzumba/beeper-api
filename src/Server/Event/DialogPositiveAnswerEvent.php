<?php
namespace Zumba\Beeper\Server\Event;


class DialogPositiveAnswerEvent extends DialogAnswerEvent
{

	public function isPositive()
	{
		return true;
	}


}