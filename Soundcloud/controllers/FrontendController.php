<?php
/**
 * Soundcloud frontend controller
 *
 * @author mk
 */
class Soundcloud_FrontendController extends Pimcore_Controller_Action_Frontend {
	/**
	 *
	 * @var Soundcloud_Consumer
	 */
	protected $consumer;
	/**
	 * Do some init stuff here
	 * @todo for dropbox action this init is to much/not needed
	 */
	public function init() {
		parent::init();
		if(!Soundcloud_Plugin::isInstalled()) {
			$this->_forward('not-installed');
			return;
		}
		try {
			$this->consumer = new Soundcloud_Consumer();
			$oAuthHandler = new OAuth_Handler($this->consumer);
			$this->consumer->setHttpClient($oAuthHandler->getAccessHttpClient());
		} catch(Exception $e) {
			$this->view->message = $e->getMessage();
		}
	}
	/**
	 * Fetch tracks from soundcloud
	 */
	public function tracksAction() {
		try {
			$username = $this->_getParam('username');

			if($username) {
				$options = array('color' => $this->config->soundcloud_player_color);
				$this->view->tracks = $this->consumer->fetchTracks($username, true, $options);
				$this->view->playerWidth	= $this->config->soundcloud_player_width;
				$this->view->playerHeight	= $this->config->soundcloud_player_height;
			}
		} catch (Exception $e) {
			Logger::err($e->getMessage());
			$this->view->message = 'Temporarly Soundcloud connection problems!';
		}
	}
	/**
	 * Just shows the dropbox
	 */
	public function dropboxAction() {
		$this->view->username = $this->_getParam('username');
		$this->view->title = $this->_getParam('title');
	}

	public function notInstalledAction() {}
}
