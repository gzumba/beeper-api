<?php
/**
 * Created by PhpStorm.
 * User: zumba
 * Date: 4.1.2016
 * Time: 16:19
 */

namespace Zumba\Beeper\Server\Event;


abstract class DialogAnswerEvent extends BeeperEvent
{

	abstract public function isPositive();

}