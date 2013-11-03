<?php

/**
 * Description of Consumer
 *
 * @author mk
 */
class OAuth_Consumer {
	/**
	 *
	 * @var OAuth_Interface_Consumer
	 */
	protected $consumer;
	/**
	 *
	 * @var Zend_Form
	 */
	protected $form;
	/**
	 *
	 * @var bool
	 */
	protected $hasAccess;
	/**
	 *
	 * @var string
	 */
	protected $name;

	public function __construct() {}

	/**
	 *
	 * @return OAuth_Interface_Consumer
	 */
	public function getConsumer() {
		return $this->consumer;
	}
	/**
	 *
	 * @param OAuth_Interface_Consumer $consumer
	 */
	public function setConsumer($consumer) {
		$this->consumer = $consumer;
		return $this;
	}
	/**
	 *
	 * @return Zend_Form
	 */
	public function getForm() {
		return $this->form;
	}
	/**
	 *
	 * @param Zend_Form $form
	 */
	public function setForm($form) {
		$this->form = $form;
		return $this;
	}
	/**
	 *
	 * @return bool
	 */
	public function getHasAccess() {
		return $this->hasAccess;
	}
	/**
	 *
	 * @param bool $hasAccess
	 * @return OAuth_Consumer
	 */
	public function setHasAccess($hasAccess) {
		$this->hasAccess = $hasAccess;
		return $this;
	}
	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	/**
	 *
	 * @param string $name
	 * @return OAuth_Consumer
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}
}