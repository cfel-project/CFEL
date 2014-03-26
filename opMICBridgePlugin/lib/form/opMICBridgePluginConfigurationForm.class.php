<?php
/*******************************************************************************
 * Copyright (c) 2011, 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/

class opMICBridgePluginConfigurationForm extends BaseForm{
	public function configure(){
//		parent::configure();

		$this->setWidget('db_host', new sfWidgetFormInput());
		$this->setDefault('db_host', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_db_host', '127.0.0.1'));
		$this->setValidator('db_host', new sfValidatorString(array('trim' => true)));
		$this->widgetSchema->setLabel('db_host', 'Database Host');
		$this->widgetSchema->setHelp('db_host', 'Please input host of the database');

		$this->setWidget('db_port', new sfWidgetFormInput());
		$this->setDefault('db_port', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_db_port', '3306'));
		$this->setValidator('db_port', new sfValidatorString(array('trim' => true)));
		$this->widgetSchema->setLabel('db_port', 'Database Port');
		$this->widgetSchema->setHelp('db_port', 'Please input port of the database');

		$this->setWidget('db_name', new sfWidgetFormInput());
		$this->setDefault('db_name', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_db_name', ''));
		$this->setValidator('db_name', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('db_name', 'Database Name');
		$this->widgetSchema->setHelp('db_name', 'set database name here');

		$this->setWidget('db_user', new sfWidgetFormInput());
		$this->setDefault('db_user', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_db_user', ''));
		$this->setValidator('db_user', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('db_user', 'Database User');
		$this->widgetSchema->setHelp('db_user', 'set database user name here');

		$this->setWidget('db_pass', new sfWidgetFormInput());
		$this->setDefault('db_pass', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_db_pass', ''));
		$this->setValidator('db_pass', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('db_pass', 'Database Password');
		$this->widgetSchema->setHelp('db_pass', 'set database password here');

		$this->setWidget('el_base', new sfWidgetFormInput());
		$this->setDefault('el_base', Doctrine::getTable('SnsConfig')->get('op_micbridge_plugin_el_base', '/el'));
		$this->setValidator('el_base', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('el_base', 'Eld-Land Application Base');
		$this->widgetSchema->setHelp('el_base', 'set Eld-Land Application Base URL here.');
		
		$this->widgetSchema->setNameFormat('op_micbridge_plugin[%s]');
	}
	
	public function save(){
//		parent::save();
		$names = array("db_host", "db_name", "db_user", "db_pass", "el_base");
		foreach($names as $name){
			if(!is_null($this->getValue($name))){
				Doctrine::getTable("SnsConfig")->set("op_micbridge_plugin_".$name, $this->getValue($name));
			}
		}
	}
}
