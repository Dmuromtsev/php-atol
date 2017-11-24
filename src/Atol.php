<?php

namespace dmuromtsev\phpAtol;

/**
 * Class Atol
 * @package dmuromtsev\phpAtol
 */
class Atol
{
	/**
	 * @var
	 */
	protected $login;

	/**
	 * @var
	 */
	protected $password;

	/**
	 * @var
	 */
	protected $token;

	/**
	 * @var
	 */
	protected $group_code;

	/**
	 * @var
	 */
	protected $apiversion = 'v3';


	/**
	 * @var
	 */
	protected $errors = [];

	/**
	 * @var
	 */
	private   $REQUEST;


	public function __construct( $login = '', $password = '', $group_code = '', $inn = '', $kpp = '', $company = '', $hostname = '' )
	{
		$this->login      = $login;
		$this->password   = $password;
		$this->group_code = $group_code;

		$this->initRequest();
	}


	/**
	 * @return bool
	 * @throws \Exception
	 */
	public function getToken()
	{
		$this->errors = [];

		$return = true;

		$result = $this->REQUEST->makeRequest('https://online.atol.ru/possystem/'.$this->apiversion.'/getToken?login='.$this->login .'&pass='.$this->password);

		if ( !$result ){
			try {
				throw new \Exception("Не возможно установить соединение с АТОЛ, сервер не отвечает");
			}catch (\Exception $e) {
				$this->errors[] = $e->getMessage();
			}
			$return = false;
		}
		else{
			$data = json_decode($result);
			if ( isset($data->code) && (int) $data->code > 1 ){
				try {
					throw new \Exception($data->text);
				}catch (\Exception $e) {
					$this->errors[] = $e->getMessage();
				}
				$return = false;
			}
			else
			{
				try {
					if ($data->token == ''){
						throw new \Exception('Не удалось получить ТОКЕН, сервер АТОЛ не вернул данные');
					}
				}catch (\Exception $e) {
					$this->errors[] = $e->getMessage();
					$return = false;
				}

				$this->token = $data->token;
			}
		}

		return $return;
	}

	/**
	 * @param string $type
	 * @param array $params
	 * @return mixed
	 */
	public function send($type, $params )
	{
		$this->getToken();

		if (!$this->errors) {

			$result = $this->REQUEST->makeRequest('https://online.atol.ru/possystem/' . $this->apiversion . '/' . $this->group_code . '/' . $type . '?tokenid=' . $this->token, 'POST', $params);

			if ($result){

				$jsd = json_decode($result);

				if ($jsd->status == 'fail'){

					$this->errors[] = $jsd->error->text;

				} else {

					return $jsd;

				}

			} else {

				$this->errors[] = 'Сервер АТОЛ не вернул данные';

			}
		}

		return false;
	}

	/**
	 * @param $uid
	 * @return mixed
	 */
	public function check( $uid )
	{
		$this->getToken();

		if (!$this->errors){

			$result = $this->REQUEST->makeRequest('https://online.atol.ru/possystem/' . $this->apiversion . '/' . $this->group_code . '/report/' . $uid . '?tokenid=' . $this->token);

			if ($result){

				$jsd = json_decode($result);

				if ( $jsd->error ){

					$this->errors[] = $jsd->error->text;

				} else {

					return $jsd;

				}
			}
			else
			{

				$this->errors[] = 'Сервер АТОЛ не вернул данные';

			}

		}

		return false;
	}

	/**
	 *
	 */
	protected function initRequest()
	{
		$this->REQUEST = new Request();
	}


	/**
	 * @return array
	 *
	 */
	public function getErrors()
	{
		return $this->errors;
	}

}