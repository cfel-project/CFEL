<?php
/*******************************************************************************
 * Copyright (c) 2011, 2013 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/

class opMICGadgetsPluginConfigurationForm extends BaseForm{
	public function configure(){
//		parent::configure();

		$this->setWidget('dsl_menu', new sfWidgetFormInput());
		$this->setDefault('dsl_menu', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu', ''));
		$this->setValidator('dsl_menu', new sfValidatorString(array('trim' => true)));
		$this->widgetSchema->setLabel('dsl_menu', 'Menu Structure');
		$this->widgetSchema->setHelp('dsl_menu', 'Please input structure of home menu in json');

		$this->setWidget('ov_event_category', new sfWidgetFormInput());
		$this->setDefault('ov_event_category', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_ov_event_category', ''));
		$this->setValidator('ov_event_category', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('ov_event_category', 'Event Category');
		$this->widgetSchema->setHelp('ov_event_category', 'set event category options here');

		$this->widgetSchema->setNameFormat('op_micgadgets_plugin[%s]');
	}

	public function save(){
//		parent::save();
		$names = array("dsl_menu", "ov_event_category");
		foreach($names as $name){
			if(!is_null($this->getValue($name))){
				Doctrine::getTable("SnsConfig")->set("op_micgadgets_plugin_".$name, $this->getValue($name));
			}
		}
	}
}
