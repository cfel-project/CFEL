<?php

class opDSLCommonPluginConfigurationForm extends BaseForm{
	public function configure(){
		$this->setWidget('elem_logger_config', new sfWidgetFormInput());
		$this->setDefault('elem_logger_config', Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_elem_logger_config', ''));
		$this->setValidator('elem_logger_config', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('elem_logger_config', 'Element Logger Configuration');
		$this->widgetSchema->setHelp('elem_logger_config', 'Write Logger configuration JSON array');
		
		$this->setWidget('list_sendtopic_comms', new sfWidgetFormInput());
		$this->setDefault('list_sendtopic_comms', Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_list_sendtopic_comms', ''));
		$this->setValidator('list_sendtopic_comms', new sfValidatorString(array('required' => true, 'trim' => true)));
		$this->widgetSchema->setLabel('list_sendtopic_comms', 'Send Topic community and message');
		$this->widgetSchema->setHelp('list_sendtopic_comms', 'Please configure message/button/community in which user is allowed to send topic contents');

		$this->setWidget('comm_topiclist_config', new sfWidgetFormInput());
		$this->setDefault('comm_topiclist_config', Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_comm_topiclist_config', ''));
		$this->setValidator('comm_topiclist_config', new sfValidatorString(array('required' => true, 'trim' => true)));
		$this->widgetSchema->setLabel('comm_topiclist_config', 'Topic List Configuration');
		$this->widgetSchema->setHelp('comm_topiclist_config', 'Specify Topic List conguration in JSON');

		$this->widgetSchema->setNameFormat('op_dslgadgets_plugin[%s]');
	}

	public function save(){
		$names = array('elem_logger_config', 'list_sendtopic_comms', 'comm_topiclist_config');

		foreach ($names as $name){
			if (!is_null($this->getValue($name))){
				Doctrine::getTable('SnsConfig')->set('op_dslgadgets_plugin_'.$name, $this->getValue($name));
			}
		}
	}
}
