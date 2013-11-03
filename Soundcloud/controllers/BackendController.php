<?php
/**
 * Class Soundcloud_BackendController
 * @see https://github.com/soundcloud/api/wiki/10.2-Resources%3A-tracks
 *
 * @author Marko Krüger <kontakt@marko-krueger.de>
 */
class Soundcloud_BackendController extends Pimcore_Controller_Action_Admin {
	/**
	 *
	 * @var Zend_Config
	 */
	protected $soundcloudConfig = null;

	public function init() {
		parent::init();
		$this->soundcloudConfig = Soundcloud_Plugin::getConfig();
		$this->httpClient = OAuth_Plugin::getHandler(new Soundcloud_Consumer())->getAccessHttpClient();
	}

	public function indexAction() {
		
		$asset = Asset::getById($this->_getParam('id'));
		$fileInfo = array(
			'fullPath' => $asset->getFullPath(),
			'fileSize' => $asset->getFileSize('MB', 2),
			'fileName' => $asset->getFilename()
		);
		
		$this->httpClient->setUri(Soundcloud_Plugin::SOUNDCLOUD_API_ME);
		$this->httpClient->setMethod(Zend_Http_Client::GET);

		$response = $this->httpClient->request()->getBody();
		$userInfo = new SimpleXMLElement($response);
		$this->_helper->json(
			array(
				'success' => array(
					'fileInfo' => $fileInfo,
					'userInfo' => $userInfo
				)
			)
		);
	}
	/**
	 * Submits a track to soundcloud (should be private by default)
	 */
	public function submitTrackAction() {
		try {
			$asset = Asset::getById($this->_getParam('id'));
			$data = '';
			$boundary = '---------------------------' . md5(rand());
			$crlf = "\r\n";

			$fh = fopen($asset->getFileSystemPath(), 'r');
			$postFile = fread($fh, filesize($asset->getFileSystemPath()));
			fclose($fh);
			
			$http = $this->accessToken
					->getHttpClient($configuration, Soundcloud_Plugin::SOUNDCLOUD_API_TRACKS);
			$http->setMethod('POST');
//			$http->setUri(Soundcloud_Plugin::SOUNDCLOUD_API_TRACKS);
			$http->prepareOauth();

			$data .= "--{$boundary}{$crlf}";
			$data .= "Content-Disposition: form-data; name=\"track[asset_data]\"; filename=\"" . $asset->getFilename() . "\"{$crlf}";
			$data .= "Content-Type: audio/mpeg{$crlf}";
			$data .= $crlf;
			$data .= $postFile . $crlf;

			$data .= "Content-Disposition: form-data; name=\"track[title]\"{$crlf}";
			$data .= "Content-Type: text/plain{$crlf}";
            $data .= $crlf;
            $data .= $asset->getFilename() . $crlf;

			$data .= "--{$boundary}--{$crlf}";


			$oauthTokenSecret = $this->accessToken->getTokenSecret();
			$authHeader = $http->getHeader('authorization').',oauth_token_secret="'.$oauthTokenSecret.'"';
			$http->setHeaders('authorization', $authHeader);

			file_put_contents(PIMCORE_WEBSITE_PATH.'/var/tmp/data.log', print_r($http, true));
//			$this->_helper->json(print_r($http->getHeader('authorization'), true));
//			return;

			//$response = $http->request('POST');
			$response = $http->setRawData($data)
					->setEncType(Zend_Http_Client::ENC_FORMDATA)
					->request('POST');

			$xml = new SimpleXMLElement($response->getBody());
			$this->_helper->json(array("xml" => $xml, "last-req:" => $http->getLastRequest() ));

		} catch (Exception $e) {
			$this->_helper->json(array('error' => $e->getMessage()));
		}
	}

	public function updateTrackAction() {

	}

	public function updateUserAction() {
		throw new Exception('Not implemented yet');
	}

	public function deleteTrackAction() {
		throw new Exception('Not implemented yet');
	}

}

// End of class BackendController

