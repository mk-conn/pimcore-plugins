<?php
/**
 * OAuth Handler
 *
 * @author mk
 */
class OAuth_Handler {
	/**
	 *
	 * @var OAuth_Interface_Consumer
	 */
	protected $consumer;
	/**
	 *
	 * @var Zend_Config
	 */
	protected $config;
	/**
	 *
	 * @var Zend_Oauth_Token_Access
	 */
	protected $accessToken;

	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer
	 */
	public function __construct($consumer = null) {
		if($consumer instanceof OAuth_Interface_Consumer) {
			$this->consumer = $consumer;
			$this->setConfig();
		}
	}
	/**
	 * Connects to the service provider, submits also callback url where the
	 * accessToken will be handled
	 *
	 * @param string $callbackUrl
	 */
	public function connect() {
		$protocol = $_SERVER['HTTPS'] ? 'https://' : 'http://';

		$callbackUrl = sprintf(
			'%s%s/plugin/OAuth/admin/add-token/consumer/%s',
			$protocol,
			$_SERVER['HTTP_HOST'],
			get_class($this->consumer)
		);
		
		try {
			$session = new Zend_Session_Namespace('Pimcore_OAuth');
			$this->config->callbackUrl = $callbackUrl;

			$oauthConsumer = new Zend_Oauth_Consumer($this->config);
			$requestToken = $oauthConsumer->getRequestToken();
			$session->requestToken = serialize($requestToken);
			
			$oauthConsumer->redirect();
			
		} catch(Exception $e) {
 			throw $e;
		}
	}
	/**
	 * Store access token
	 * 
	 * @param array $params The url get parameters
	 */
	public function handleAccessToken($params) {
		
		$oAuthConsumer = new Zend_Oauth_Consumer($this->config);
		$session = new Zend_Session_Namespace('Pimcore_OAuth');
		if(!empty($params) && isset($session->requestToken)) {
			try {
				$accessToken = $oAuthConsumer->getAccessToken(
					$params,
					unserialize($session->requestToken)
				);
				$session->accessToken = serialize($accessToken);
				unset($session->requestToken);
				OAuth_Plugin::updateConsumerConfig(
					get_class($this->consumer),
					array('accessToken' => serialize($accessToken))
				);

			} catch(Exception $e) {
				throw $e;
			}		
		}
	}
	/**
	 *
	 * @return Zend_Oauth_Token_Access
	 */
	public function getAccessToken() {		
		return $this->accessToken;
	}
	/**
	 *
	 * @return Zend_Oauth_Client
	 */
	public function getAccessHttpClient() {
		try {
			$oauthOptions = $this->config;
			if($this->getAccessToken() instanceof Zend_Oauth_Token_Access) {
				return $this->getAccessToken()->getHttpClient(
					$oauthOptions->toArray(),
					null,
					array('timeout' => 5)
				);
			}
		} catch(Exception $e) {
			throw $e;
		}
	}
	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer 
	 */
	public function setConsumer(OAuth_Interface_Consumer $consumer) {
		$this->consumer = $consumer;

		return $this;
	}
	/**
	 *
	 * @return OAuth_Interface_Consumer
	 */
	public function getConsumer() {
		return $this->consumer;
	}
	/**
	 *
	 * Config setter
	 */
	public function setConfig() {
		$row = OAuth_Plugin::getConsumerConfig(get_class($this->consumer));
		$this->config = $this->consumer->getConfig();
		$this->config->consumerKey = $row['consumerKey'];
		$this->config->consumerSecret = $row['consumerSecret'];
		$this->accessToken = unserialize($row['accessToken']);
		
		return $this;
	}
	/**
	 *
	 * @return Zend_Config
	 */
	public function getConfig() {
		return $this->config;
	}
}
