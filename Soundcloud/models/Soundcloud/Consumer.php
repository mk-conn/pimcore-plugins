<?php

/**
 * Soundcloud Consumer
 *
 * @author mk
 */
class Soundcloud_Consumer implements OAuth_Interface_Consumer {
	/**
	 *
	 * @var Zend_Http_Client
	 */
	protected $httpClient;
	/**
	 *
	 * @return Zend_Config
	 */
	public function getConfig() {
		return Soundcloud_Plugin::getConfig();
	}
	/**
	 * @param null $username
	 * @param bool $onlyPublic
	 * @param array $options
	 * @return array
	 */
	public function fetchTracks($username = null, $onlyPublic = true, $options = array()) {
		$tracks = array();
		$uri = sprintf(Soundcloud_Plugin::SOUNDCLOUD_API_USER_TRACKS, $username);
		$this->httpClient->setUri($uri);
		$this->httpClient->setMethod(Zend_Http_Client::GET);
		if($onlyPublic) {
			$this->httpClient->setParameterGet('filter', 'public');
		}

		$response = $this->httpClient->request();
		if($response->isSuccessful()) {
			$xml = simplexml_load_string($response->getBody(), 'SimpleXMLElement', LIBXML_NOCDATA);
			foreach($xml->track as $track) {
				$soundcloudTrack = new Soundcloud_Track($track);
				$soundcloudTrack->setOptions($options);
				array_push($tracks, $soundcloudTrack);
			}
		}
		return $tracks;
	}
	/**
	 *
	 */
	public function postTrack() {
		throw new Exception('Not implemented yet');
	}
	/**
	 *
	 */
	public function updateUserData() {
		throw new Exception('Not implemented yet');
	}
	/**
	 *
	 */
	public function updateTrack() {
		throw new Exception('Not implemented yet');
	}
	/**
	 *
	 * @return Zend_Http_Client
	 */
	public function getHttpClient() {
		return $this->httpClient;
	}
	/**
	 *
	 * @param Zend_Http_Client $httpClient
	 * @return Soundcloud_Consumer
	 */
	public function setHttpClient($httpClient) {
		$this->httpClient = $httpClient;
		return $this;
	}

}

?>
