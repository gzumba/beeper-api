<?php
namespace Zumba\Beeper\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LoggerAwareInterface;
use Zumba\Beeper\Client\Response\BeeperResponse;
use Zumba\Log\LoggingI;
use Zumba\Log\LoggingTrait;

class BeeperClient implements LoggerAwareInterface
{
	use LoggingTrait;
	/**
	 * @var Client
	 */
	private $guzzle;
	private $app_id;
	private $api_key;
	private $default_sender_id;
	private $white_list;

	public function __construct(Client $guzzle, $app_id, $api_key)
	{
		$this->guzzle = $guzzle;
		$this->app_id = $app_id;
		$this->api_key = $api_key;
	}

	public function senderCreate($sender)
	{
		$body = json_encode($sender);
		$request = new Request('POST', 'senders', $this->buildHeaders(), $body);

		return $this->guzzle->send($request);
	}

	/**
	 * @param string $recipient
	 * @param string $text
	 * @param string $sender_id use default when null
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function textSend($recipient, $text, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
		];

		$body = json_encode($data);
		$request = new Request('POST', 'messages', $this->buildHeaders(), $body);

		$this->logInfo("Sending text message to {phone}", $data);

		return $this->guzzle->send($request);
	}

	/**
	 * @param $recipient
	 * @param $text
	 * @param $positive_button_text
	 * @param $negative_button_text
	 * @param string $sender_id
	 * @return BeeperResponse
	 */
	public function dialogSend($recipient, $text, $positive_button_text, $negative_button_text, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
			'positive_button_text' => $positive_button_text,
			'negative_button_text' => $negative_button_text,
		];

		$body = json_encode($data);
		$request = new Request('POST', 'dialogs', $this->buildHeaders(), $body);

		$this->logInfo("Sending dialog message to {phone}", $data);

		$response = $this->guzzle->send($request);

		return new BeeperResponse($response->getStatusCode(), json_decode($response->getBody(), true));
	}

	/**
	 * @param string $recipient
	 * @param string $text
	 * @param \DateTime $start
	 * @param \DateTime $end
	 * @param string|null $sender_id
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function eventSend($recipient, $text, \DateTime $start, \DateTime $end, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
			'start_time' => $start->format('c'),
			'end_time' => $end->format('c'),
		];

		$body = json_encode($data);
		$request = new Request('POST', 'events', $this->buildHeaders(), $body);

		$this->logInfo("Sending event message to {phone}", $data);

		return $this->guzzle->send($request);
	}

	/**
	 * @param $recipient
	 * @param $text
	 * @param null $sender_id
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	public function taskSend($recipient, $text, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
		];

		$body = json_encode($data);
		$request = new Request('POST', 'tasks', $this->buildHeaders(), $body);

		$this->logInfo("Sending task message to {phone}", $data);

		return $this->guzzle->send($request);
	}

	/**
	 * @param string $recipient
	 * @param string $text
	 * @param $latitude
	 * @param $longitude
	 * @param null $sender_id
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	public function locationSend($recipient, $text, $latitude, $longitude, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
			'latitude' => $latitude,
			'longitude' => $longitude,
		];

		$body = json_encode($data);
		$request = new Request('POST', 'locations', $this->buildHeaders(), $body);

		$this->logInfo("Sending location message to {phone}", $data);

		return $this->guzzle->send($request);
	}

	/**
	 * @param $recipient
	 * @param $text
	 * @param string $image url or base64 encoded image
	 * @param null $sender_id
	 * @return mixed|\Psr\Http\Message\ResponseInterface
	 */
	public function imageSend($recipient, $text, $image, $sender_id = null)
	{
		$data = [
			'sender_id' => $this->resolveSenderId($sender_id),
			'phone' => $recipient,
			'text' => $text,
			'image' => $image,
		];

		$body = json_encode($data);
		$request = new Request('POST', 'images', $this->buildHeaders(), $body);

		return $this->guzzle->send($request);
	}

	/**
	 * @return array
	 */
	protected function buildHeaders()
	{
		return [
			'Content-Type' => 'application/json',
			'X-Beeper-Application-Id' => $this->app_id,
			'X-Beeper-REST-API-Key' => $this->api_key,
		];
	}

	public function getDefaultSenderId()
	{
		return $this->default_sender_id;
	}

	/**
	 * @param string $default_sender_id
	 * @return BeeperClient
	 */
	public function setDefaultSenderId($default_sender_id)
	{
		$this->default_sender_id = $default_sender_id;

		return $this;
	}

	private function resolveSenderId($sender_id)
	{
		if( $sender_id )
		{
			return $sender_id;
		}

		if( !$this->default_sender_id )
		{
			throw new \LogicException("No default sender_id set");
		}

		return $this->default_sender_id;
	}

	/**
	 * @return array
	 */
	public function senderList()
	{
		$request = new Request('GET', 'senders', $this->buildHeaders());

		$response = $this->guzzle->send($request);

		return json_decode($response->getBody(), true);
	}

	public function webhookCreate($url, array $events)
	{
		$data = [
			'payload_url' => $url,
			'events' => $events,
		];

		$body = json_encode($data);

		dump($body);
		$request = new Request('POST', 'webhooks', $this->buildHeaders(), $body);

		return $this->guzzle->send($request);
	}

	/**
	 * @return array
	 */
	public function webhookList()
	{
		$request = new Request('GET', 'webhooks', $this->buildHeaders());

		$response = $this->guzzle->send($request);

		return json_decode($response->getBody(), true);
	}

	public function webhookDelete($webhook_id)
	{
		$request = new Request('DELETE', "webhooks/{$webhook_id}", $this->buildHeaders());

		$response = $this->guzzle->send($request);

		return json_decode($response->getBody(), true);
	}

}