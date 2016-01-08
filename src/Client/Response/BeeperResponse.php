<?php
namespace Zumba\Beeper\Client\Response;


class BeeperResponse
{
	private $data;
	private $status_code;

	/**
	 * BeeperResponse constructor.
	 * @param int $status_code
	 * @param array $data
	 */
	public function __construct($status_code, array $data)
	{
		$this->data = $data;
		$this->status_code = $status_code;
	}

	public function isSuccess()
	{
		return $this->status_code === 200;
	}

	public function getId()
	{
		return $this->getFieldOrNull('_id');
	}

	/**
	 * @param string $field
	 * @return mixed
	 */
	private function getFieldOrNull($field)
	{
		if( !array_key_exists($field, $this->data) )
		{
			return null;
		}

		return $this->data[$field];
	}
}