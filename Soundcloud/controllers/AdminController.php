<?php

/**
 * Description of AdminController
 *
 * @author mk
 */
class Soundclouds_AdminController extends Pimcore_Controller_Action_Admin {
	/**
	 *
	 * @return null
	 */
	public function init() {
		parent::init();
		if(!Soundcloud_Plugin::isInstalled()) {
			// show please install message...
			$this->_forward('not-installed');
			return;
		}
	}

	public function notInstalledAction() {

	}
}

?>
