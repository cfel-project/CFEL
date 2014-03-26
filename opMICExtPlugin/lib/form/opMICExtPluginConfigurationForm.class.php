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

class opMICExtPluginConfigurationForm extends BaseForm{
	public function configure(){
//		parent::configure();

		$this->setWidget('d3_url', new sfWidgetFormInput());
		$this->setDefault('d3_url', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_d3_url', '//cdnjs.cloudflare.com/ajax/libs/d3/3.4.3/d3.min.js'));
		$this->setValidator('d3_url', new sfValidatorString(array('trim' => true)));
		$this->widgetSchema->setLabel('d3_url', 'Location of d3.js');
		$this->widgetSchema->setHelp('d3_url', 'Please set URL of d3.js');

		$this->widgetSchema->setNameFormat('op_micext_plugin[%s]');
	}
	
	public function save(){
//		parent::save();
		$names = array("d3_url");
		foreach($names as $name){
			if(!is_null($this->getValue($name))){
				Doctrine::getTable("SnsConfig")->set("op_micext_plugin_".$name, $this->getValue($name));
			}
		}
	}
}
