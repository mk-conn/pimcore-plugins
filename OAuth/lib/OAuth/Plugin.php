<?php
/**
 * OAuth_Plugin
 *
 * Basic plugin file that handles installation, deinstallation, etc.
 *
 * @author mk
 */
class OAuth_Plugin extends Pimcore_API_Plugin_Abstract
implements Pimcore_API_Plugin_Interface {
	/**
	 * Table name where configs are stored
	 */
	const TABLE_NAME = 'plugin_oauth';
	/**
	 *
	 * @return string
	 */
	public static function install() {
		$sql = file_get_contents(self::getPluginPath().'/data/create.sql');
		$db = Pimcore_API_Plugin_Abstract::getDb();
		$db->exec($sql);
		if(self::isInstalled()) {
			return 'OAuth installed and ready to be used.';
		}
		return 'Oooh bad... installation failed!';
	}
	/**
	 *
	 * @return bool
	 */
	public static function isInstalled() {
		$db = Pimcore_API_Plugin_Abstract::getDb();
		try {
			$db->describeTable(self::TABLE_NAME);
			return true;
		} catch(Zend_Db_Statement_Exception $e) {
			return false;
		}
		return false;
	}
	/**
	 *
	 * @return string
	 */
	public static function getPluginPath() {
		return PIMCORE_PLUGINS_PATH.'/OAuth';
	}
	/**
	 *
	 * @return bool
	 */
	public static function readyForInstall() {
		return is_writeable(self::getPluginPath());
	}
	/**
	 *
	 * @return string 
	 */
	public static function uninstall() {
		$message = 'Uninstall failed';		
		if(!self::getConsumers()) {
			$db = Pimcore_API_Plugin_Abstract::getDb();
			$db->exec('DROP TABLE '.self::TABLE_NAME);
			if(!self::isInstalled()) {
				$message =  'OAuth is now uninstalled!';
			}
		} else {
			$message = 'Please uninstall all consumers first.';
		}
		return $message;
	}
	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer
	 */
	public static function registerConsumer(OAuth_Interface_Consumer $consumer) {
		if(self::isInstalled()) {
			$className = get_class($consumer);
			$db = Pimcore_API_Plugin_Abstract::getDb();
			$db->insert(self::TABLE_NAME, array('className' => $className));
		} else {
			throw new Exception('OAuth Plugin not installed - install first.');
		}
	}
	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer
	 */
	public static function unregisterConsumer(OAuth_Interface_Consumer $consumer) {
		$className = get_class($consumer);
		$db = Pimcore_API_Plugin_Abstract::getDb();
		$db->delete(self::TABLE_NAME, "className = '{$className}'");
	}
	/**
	 *
	 * @return Zend_Db_Table_Rowset
	 */
	public static function getConsumers() {
		if(self::isInstalled()) {
			$db = Pimcore_API_Plugin_Abstract::getDb();
			return $db->fetchAll('SELECT * FROM '.self::TABLE_NAME.' ORDER BY id DESC');
		}		
		return null;
	}
	/**
	 * @return string
	 */
	public static function getConsumer($consumer) {
		return new $consumer();
	}
	/**
	 *
	 * @param string $consumer
	 * @param array $data
	 */
	public static function updateConsumerConfig($consumer, $data) {
		$db = Pimcore_API_Plugin_Abstract::getDb();
		return $db->update(self::TABLE_NAME, $data, "className = '$consumer'");
	}
	/**
	 *
	 * @param string $consumer
	 * @return Zend_Db_Table_Row
	 */
	public static function getConsumerConfig($consumer) {
		$db = Pimcore_API_Plugin_Abstract::getDb();
		return $db->fetchRow("SELECT * FROM ".self::TABLE_NAME." WHERE className = '$consumer'");
	}
	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer
	 * @return OAuth_Handler
	 */
	public static function getHandler($consumer) {
		$oauthHandler = new OAuth_Handler($consumer);
		return $oauthHandler;
	}
	/**
	 * Counts, how many consumers are registered
	 *
	 * @return string
	 */
	public static function getPluginState() {
		parent::getPluginState();
		$consumerCount = count(self::getConsumers());
		return sprintf('%d consumer(s) registered.', $consumerCount);
	}
}