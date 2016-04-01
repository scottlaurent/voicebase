<?php

namespace Scottlaurent\Voicebase\ObjectHandlers;

use Scottlaurent\Voicebase\Voicebase;

/**
 * Class BaseObjectManagerHandler
 * @package Scottlaurent\Voicebase\ObjectHandlers
 */
abstract class BaseObjectManagerHandler
{
	/**
	 * @var Voicebase
	 */
	protected $voicebase;

	/**
	 * BaseObjectManagerHandler constructor.
	 * @param Voicebase $voicebase
	 */
	public function __construct(Voicebase $voicebase)
    {
		$this->voicebase = $voicebase;
    }

	/**
	 * @param $response
	 * @return mixed
	 */
	protected function returnRawResponse($response)
	{
		return $response->getContents();
	}

	/**
	 * @param $response
	 * @return mixed
	 */
	protected function decodeResponse($response)
	{
		return json_decode($response->getContents(),true);
	}
}