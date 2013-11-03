<?php
/**
 * 
 */
class OAuth_Form_Credentials extends Zend_Form {

	public function init() {

		$consumer = new Zend_Form_Element_Hidden('consumer');

		$consumerKey = new Zend_Form_Element_Text('consumerKey');
		$consumerKey->addFilter('StringTrim')
			->setRequired(true)
			->setLabel('Your consumer key:')
			->setAttrib('class', 'x-form-text consumer-key');
		
		$consumerSecret = new Zend_Form_Element_Text('consumerSecret');
		$consumerSecret->addFilter('StringTrim')
			->setRequired(true)
			->setLabel('Your consumer secret:')
			->setAttrib('class', 'x-form-text consumer-secret');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class', 'x-form-btns');
		
		$this->addElements(
				array(
					$consumer,
					$consumerKey,
					$consumerSecret,
					$submit
				)
		);
		
		$this->addDisplayGroup(array('consumerKey', 'consumerSecret', 'submit'), 'credentials');
		
		$this->getDisplayGroup('credentials')
				->setLegend('Add your credentials here')
				->addAttribs(array('class' => 'x-fieldset'));
		
		return $this;
	}
}
