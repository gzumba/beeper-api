<?php
namespace Zumba\Beeper\Server\Event;


abstract class BeeperEvent
{
	const DIALOG_POSITIVE_ANSWER = 'dialog.positive_answer';
	const DIALOG_NEGATIVE_ANSWER = 'dialog.negative_answer';
	/**
	 * @var array
	 */
	protected $data;
	/** @var string */
	protected $id;

	public function __construct(array $data)
	{
		$this->data = $data;

		if( !array_key_exists('_id', $this->data) )
		{
			throw new \DomainException("Event has no _id");
		}

		$this->id = $this->data['_id'];

	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

}