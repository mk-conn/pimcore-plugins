<?php

/**
 * Pimcore Admin Backend Controller
 *
 * @author mk
 */
class OAuth_AdminController extends Pimcore_Controller_Action_Admin {
	/**
	 *
	 * @return null
	 */
	public function init() {
		parent::init();

		//$this->handler = new OAuth_Handler();
		if(!OAuth_Plugin::isInstalled()) {
			// show please install message...
			$this->_forward('not-installed');
			return;
		}
	}

	/**
	 * Get Accesstoken from service provider
	 */
	public function getAccessAction() {

		$consumer = $this->_getParam('consumer');
	
		try {
			/* @var $consumerModel OAuth_Interface_Consumer */
			$consumerModel = new $consumer();
			$oAuthHandler = new OAuth_Handler($consumerModel);
			$oAuthHandler->connect();
		} catch(Exception $e) {
			$this->view->error = $e->getMessage();
		}
	}
	/**
	 * When service provider redirects back with the access token
	 * it will be stored here
	 */
	public function addTokenAction() {
		$consumer = $this->_getParam('consumer');
		/* @var $consumerModel OAuth_Interface_Consumer */
		$consumerModel = new $consumer();		
		try {
			$oAuthHandler = new OAuth_Handler($consumerModel);
			$oAuthHandler->handleAccessToken($_GET);

			$this->_forward('config');
		} catch (Exception $e) {

		}		
	}
	/**
	 * Stores consumer key and secret 
	 */
	public function addKeysAction() {

		$form = new OAuth_Form_Credentials();
		$consumer = $this->_getParam('consumer');
		if($this->_request->isPost()) {
			if($form->isValid($_POST)) {
				$values = $form->getValues();
				$updateValues = array(
					'consumerKey' => $values['consumerKey'],
					'consumerSecret' => $values['consumerSecret']
				);				
				$rows = OAuth_Plugin::updateConsumerConfig(
					$consumer,
					$updateValues
				);
				$return = array(
					'success' => 'Credentials updated successfully '.$consumer
				);
				$this->_helper->json($return);
				return;
			} else {
				$errors = array();
				foreach($form->getErrors() as $error => $reason) {
					if($reason) {
						$errors[] = $error;
					}
				}
				$this->_helper->json(array('errors' => $errors));
				return;
			}
		}
		$this->view->form = $form;
	}
	/**
	 * Removes access token which means consumer is disconnected
	 * from the service provider
	 */
	public function disconnectAction() {
		$consumer = $this->_getParam('consumer');

		try {
			OAuth_Plugin::updateConsumerConfig($consumer, array('accessToken' => null));
			$this->_helper->json(array('success' => 'disconnected'));
		} catch (Exception $e) {
			$this->_helper->json(array('error' => $e->getMessage()));
		}
	}
	/**
	 * The config page in the pimcore backend
	 */
	public function configAction() {
		
		$consumers = OAuth_Plugin::getConsumers();
		$viewConsumers = new ArrayObject();
		
		foreach($consumers as $consumer) {
			$hasAccess = false;
			$consumerView = array();

			$class = $consumer['className'];
			$config = array(
				'consumerKey'		=> $consumer['consumerKey'],
				'consumerSecret'	=> $consumer['consumerSecret'],
				'accessToken'		=> unserialize($consumer['accessToken']),
				'consumer'			=> $class
			);

			if($config['accessToken'] instanceof Zend_Oauth_Token_Access) {
				$hasAccess = true;
			}

			$form = new OAuth_Form_Credentials();
			$form->setDefaults($config);
			$form->setName($class);
			$form->setAttrib('class', 'consumer-form');
			$form->setIsArray(true);

			$consumerClass = new $class();
			$consumerModel = new OAuth_Consumer();
			$consumerModel->setConsumer($consumerClass)
					->setForm($form)
					->setName(str_replace('_', ' ', $class))
					->setHasAccess($hasAccess);
			
			$viewConsumers->append($consumerModel);
		}
		$this->view->consumers = $viewConsumers;
	}
	/**
	 * Are consumerKey and secret already entered and stored?
	 */
	public function hasKeysAction() {
		$return = array('hasKeys' => false);
		$consumer = $this->_getParam('consumer');
		
		$row = OAuth_Plugin::getConsumerConfig($consumer);
		if($row['consumerKey'] && $row['consumerSecret']) {
			$return['hasKeys'] = true;
		}		
		$this->_helper->json($return);		
	}
	/**
	 * Plugin is not installed - render the not-installed view
	 */
	public function notInstalledAction() {}

}
