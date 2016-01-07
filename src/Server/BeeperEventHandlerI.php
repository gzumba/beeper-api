<?php
namespace Zumba\Beeper\Server;


use Zumba\Beeper\Server\Event\DialogPositiveAnswerEvent;

interface BeeperEventHandlerI
{
	public function handleDialogPositiveAnswer(DialogPositiveAnswerEvent $event);
	public function handleDialogNegativeAnswer(DialogNegativeAnswerEvent $event);
}