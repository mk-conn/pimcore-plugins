<?php

/**
 * Soundcloud Plugin to fetch tracks for a given user
 *
 * @author mk
 */
class Soundcloud_Plugin	extends Pimcore_API_Plugin_Abstract
implements Pimcore_API_Plugin_Interface {
	/**
	 * Soundcloud tracks url sprintf-ready format
	 */
	const SOUNDCLOUD_API_USER_TRACKS = 'http://api.soundcloud.com/v1/users/%s/tracks';
	/**
	 * Soundcloud *me* (users/user) request url
	 */
	const SOUNDCLOUD_API_ME = 'http://api.soundcloud.com/me';

	const SOUNDCLOUD_API_TRACKS = 'http://api.soundcloud.com/tracks';
	/**
	 *
	 * @var Zend_Config_Xml
	 */
	private static $config = null;
	/**
	 * Initial config file
	 */
	const CONFIG_FILE = 'soundcloud.xml';
	/**
	 * Creates config and such
	 *
	 * @return string
	 */
	public static function install() {

		$statusMessage = '';

		if(!self::readyForInstall()) {
			return 'Soundcloud plugin not ready for install!';
		}
		if(!self::isInstalled()) {
			try {
				OAuth_Plugin::registerConsumer(new Soundcloud_Consumer());

				$config = self::getConfig(true);
				$config->installed = 'true';

				$writer = new Zend_Config_Writer_Xml(
					array(
						'config'	=> $config,
						'filename'  => self::getConfigFileName()
					)
				);
				$writer->write();
				/* @var $websiteConfig Zend_Config */
				$websiteConfig = Pimcore_Tool_Frontend::getWebsiteConfig();
				$playerConfig = array(
					'soundcloud_player_width' => array(
						'data' => 700,
						'type' => 'text'
					),
					'soundcloud_player_height' => array(
						'data' => 81,
						'type' => 'text'
					),
					'soundcloud_player_color' => array(
						'data' => '2B5668',
						'type' => 'text'
					)
				);
				$mergeConfig = new Zend_Config($playerConfig, true);
				//merge websiteconfig with possibly existant config
				$websiteConfig->merge($mergeConfig);

				$websiteWriter = new Zend_Config_Writer_Xml(
					array(
						'config'	=> $websiteConfig,
						'filename'	=> PIMCORE_CONFIGURATION_DIRECTORY . "/website.xml"
					)
				);
				$websiteWriter->write();
				// register for oauth

				$statusMessage = 'Plugin installation successfull.';
			} catch (Exception $e) {
				$statusMessage = $e->getMessage();
			}
		}
		return $statusMessage;
	}

	/**
	 * Checks if config file is present and directory is writeable
	 *
	 * @return boolean
	 */
	public static function readyForInstall() {
		if(!is_writeable(PIMCORE_PLUGINS_PATH.'/Soundcloud')) {
			Logger::debug('Soundcloud path not writeable');
			return false;
		} else if(!is_writeable(self::getConfigFileName())) {
			Logger::debug('Soundcloud config file not writeable');
			return false;
		}
		return true;
	}
	/**
	 * Checks if plugin is installed
	 *
	 * @return boolean
	 */
	public static function isInstalled() {
		$config = self::getConfig(true);

		if($config->installed === 'true') {
			return true;
		}
		return false;
	}
	/**
	 * Gets the config
	 *
	 * @param boolean $reload
	 * @return Zend_Config writable
	 */
	public static function getConfig($reload = false) {
		try {
			if(!self::$config || $reload === true) {
				self::$config = new Zend_Config_Xml(
					self::getConfigFileName(),
					null,
					true
				);
			}
			return self::$config;
		} catch (Zend_Config_Exception $e) {
			Logger::alert($e->getMessage());
		}
		return null;
	}
	/**
	 *
	 * @param mixed $name array (key => value) or string
	 * @param mixed $value
	 */
	public static function updateConfig($name, $value = null) {
		$config = self::getConfig();

		if(is_array($name)) {
			foreach($name as $key => $value) {
				$config->{$key} = $value;
			}
		} else {
			$config->{$name} = $value;
		}

		$writer = new Zend_Config_Writer_Xml(
			array(
				'config'	=> $config,
				'filename'	=> self::getConfigFileName()
			)
		);
		$writer->write();
	}
	/**
	 * Returns just the config filename
	 * @return string
	 */
	protected static function getConfigFileName() {
		return PIMCORE_PLUGINS_PATH.'/Soundcloud/soundcloud.xml';
	}
	/**
	 * Undo all changes made during installation/configuration
	 */
	public static function uninstall() {
		$config = self::getConfig();
		// set installed false
		$config->installed = 'false';
		// write back config with default settings
		$writer = new Zend_Config_Writer_Xml(
			array(
				'config'	=> $config,
				'filename'	=> self::getConfigFileName()
			)
		);
		$writer->write();
		// remove entries from pimcore website config and write back
		$websiteConfig = Pimcore_Tool_Frontend::getWebsiteConfig();
		unset(
			$websiteConfig->soundcloud_player_width,
			$websiteConfig->soundcloud_player_height
		);
		$writer = new Zend_Config_Writer_Xml(
			array(
				'config'	=> $websiteConfig,
				'filename'	=> PIMCORE_CONFIGURATION_DIRECTORY . "/website.xml"
			)
		);
		$writer->write();
		OAuth_Plugin::unregisterConsumer(new Soundcloud_Consumer());
		// done.
	}
}
