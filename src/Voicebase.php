<?php

namespace Scottlaurent\Voicebase;

use ReflectionClass;
use GuzzleHttp\Client as Client;

/**
 * Class Voicebase
 * @package Scottlaurent\Voicebase
 */
class Voicebase
{

	/**
	 * @var array
	 */
	protected $parameters = [
		'token', // required
		'base_url', // optional
		'accuracy_engine' // optional
	];

	/**
	 * @var Client
	 */
	protected $client;

	/**
	 * @var string
	 */
	protected $token;

	/**
	 * Supported values are:
	 * standard - the standard engine emphasizing speed over accuracy
	 * premium - the premium engine emphasing high accuracy
	 *
	 * @var string
	 */
	protected $accuracy_engine = 'standard';

	/**
	 * @var string
	 */
	protected $base_url = 'https://apis.voicebase.com/v2-beta';

	/**
	 * @var $string
	 */
	protected $url;

	/**
	 * Voicebase constructor.
	 * @param null $parameters
	 */
	function __construct($parameters = null)
    {
		$this->init($parameters);
    }

	/**
	 * Make a raw request against the Voicebase API
	 *
	 * @param $method
	 * @param $path
	 * @param array $parameters
	 * @param array $extra_headers
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \Exception
	 */
	function makeRequest($method, $path, $parameters=[], $extra_headers=[])
    {
		try {
			$client = $this->getClient($extra_headers);
			$reponse = $client->request($method, $path, $parameters);
			return $reponse->getBody();
		}
		catch (\GuzzleHttp\Exception\ClientException $e) {
			$responseBodyAsString = $e->getResponse()->getBody()->getContents();
			throw new \Exception('There was a problem with the request. ' . $responseBodyAsString);
		}
    }

	/**
	 * Make a POST request against the Voicebase API
	 *
	 * @param $path
	 * @param array $parameters
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \Exception
	 */
	public function post($path, $parameters=[])
    {
        return $this->makeRequest('POST',$path,$parameters);
    }

	/**
	 * Make a GET request against the Voicebase API
	 *
	 * @param $path
	 * @param array $parameters
	 * @param array $extra_headers
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \Exception
	 */
	public function get($path, $parameters=[],$extra_headers=[])
    {
        return $this->makeRequest('GET',$path,$parameters,$extra_headers);
    }


	/**
	 * Make a PUT request against the Voicebase API
	 *
	 * @param $path
	 * @param array $parameters
	 * @return \Psr\Http\Message\StreamInterface
	 * @throws \Exception
	 */
	public function put($path, $parameters=[])
	{
		return $this->makeRequest('PUT',$path,$parameters);
	}


	/**
	 * Generate a Guzzle client object
	 *
	 * @param array $extra_headers
	 * @return Client
	 */
	private function getClient($extra_headers=[])
    {
        if (!$this->client)
        {
	        $this->client  = new Client([
	            'base_uri' => $this->base_url . '/',
				'headers' => $extra_headers + [
						'Authorization' => 'Bearer ' . $this->token]
				]);
		}

        return $this->client;
    }

	/**
	 * Init our object using a token
	 * Optional: send array (be sure to include token in array)
	 *
	 * @param $parameters
	 * @throws \Exception
	 */
	private function init($parameters)
	{
        if (is_array($parameters))
        {
            foreach (['token','base_url','accuracy_engine'] as $parameter)
            {
                if (isset($parameters[$parameter]))
		        {
		             $this->$parameter = $parameters[$parameter];
		        }
            }
        } else {
            $this->token = $parameters;
        }

		foreach (['token','base_url','accuracy_engine'] as $parameter)
		{
			if (!$this->$parameter)
			{
				throw new \Exception('missing parameter ' . $parameter);
			}
		}
	}

	/**
	 * Magic method to handle service calls to Voicebase methods/objects
	 *
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \Exception
	 * @internal param $field
	 */
	function __call($method,$arguments)
	{
		if (in_array($method,['media','definitions'])) {

			try {

				// determine the name of the class we will use to handle our method
				$classpath = __NAMESPACE__ . '\\ObjectHandlers\\' . ucwords($method);

				// create that class
				$class = new ReflectionClass($classpath);

				// add this (self) to the parameters since we will be using this class as our http client
				array_unshift($arguments, $this);

				// return an instance of the method
				return $class->newInstanceArgs($arguments);

			} catch (\Exception $e) {
				throw ($e);
			}
		}

		throw new \Exception("Invalid Method Handler. $method is not defined");
	}
}
