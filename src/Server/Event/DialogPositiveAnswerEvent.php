<?php
/**
 * Created by PhpStorm.
 * User: zumba
 * Date: 4.1.2016
 * Time: 16:19
 */

namespace Zumba\Beeper\Server\Event;


class DialogPositiveAnswerEvent extends DialogAnswerEvent
{
	/**
	 * @var array
	 */
	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function isPositive()
	{
		return true;
	}


}