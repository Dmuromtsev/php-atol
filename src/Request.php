<?php

namespace dmuromtsev\phpAtol;
/**
 * Class Request
 * @package dmuromtsev\phpAtol
 *
 */
class Request
{
	/**
	 * @var array
	 */
	protected $params = [ 'header' => 0, 'timeout' => 10];

	/**
	 * @param $url
	 * @param string $type
	 * @param array $postData
	 * @return mixed
	 */
	public function makeRequest($url, $type = 'GET', $postData = [] )
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,  $url);

		if ($type == 'POST'){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE)); //JSON_UNESCAPED_UNICODE
		}
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->params['header']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, $this->params['header']);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);

		return $result;
	}

	/**
	 * @param array $params
	 */
	protected function initParams($params = [] )
	{
		foreach ($params as $k => $v)
		{
			if ( isset($this->params[$k]) ){

				$this->params[$k] = $v;

			}
		}
	}
}