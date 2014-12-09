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

class opMICGadgetsPluginConfigurationForm extends BaseForm{
	public function configure(){
//		parent::configure();

		$this->setWidget('dsl_menu', new sfWidgetFormInput());
		$this->setDefault('dsl_menu', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu', ''));
		$this->setValidator('dsl_menu', new sfValidatorString(array('trim' => true)));
		$this->widgetSchema->setLabel('dsl_menu', 'Menu Structure');
		$this->widgetSchema->setHelp('dsl_menu', 'Please input structure of home menu in json');

		$menu_types = array('default' => 'Default', 'shrink' => 'Shrink');
		$this->setWidget('dsl_menu_type', new sfWidgetFormSelectRadio(array('choices' => $menu_types)));
		$this->setDefault('dsl_menu_type', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu_type', 'default'));
		$this->setValidator('dsl_menu_type', new sfValidatorChoice(array('choices' => array_keys($menu_types))));
		$this->widgetSchema->setLabel('dsl_menu_type', 'Menu Type');
		$this->widgetSchema->setHelp('dsl_menu_type', 'Please choose menu type');

		$this->setWidget('dsl_menu_cols', new sfWidgetFormInput());
		$this->setDefault('dsl_menu_cols', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu_cols', 4));
		$this->setValidator('dsl_menu_cols', new sfValidatorNumber(array('min'=>1, 'max'=>10)));
		$this->widgetSchema->setLabel('dsl_menu_cols', 'Menu Columns');
		$this->widgetSchema->setHelp('dsl_menu_cols', 'Please input number of columns for menu gadget');

		$this->setWidget('ov_event_category', new sfWidgetFormInput());
		$this->setDefault('ov_event_category', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_ov_event_category', ''));
		$this->setValidator('ov_event_category', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('ov_event_category', 'Event Category');
		$this->widgetSchema->setHelp('ov_event_category', 'set event category options here');
		
		$choices = array('1' => 'On', '0' => 'Off');
		$this->setWidget('dsl_smttimeline_comment_delete', new sfWidgetFormSelectRadio(array('choices' => $choices)));
		$this->setDefault('dsl_smttimeline_comment_delete', Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_smttimeline_comment_delete', '1'));
		$this->setValidator('dsl_smttimeline_comment_delete', new sfValidatorChoice(array('choices' => array_keys($choices))));
		$this->widgetSchema->setLabel('dsl_smttimeline_comment_delete', 'Show delete timeline comment link for smartphone');
		$this->widgetSchema->setHelp('dsl_smttimeline_comment_delete', 'To hide delete timeline comment link for smartphone, please set Off. Default is On.');
		
		$this->widgetSchema->setNameFormat('op_micgadgets_plugin[%s]');
	}
	
	public function save(){
//		parent::save();
		$names = array("dsl_menu", "dsl_menu_type", "dsl_menu_cols", "ov_event_category", "dsl_smttimeline_comment_delete");
		foreach($names as $name){
			if(!is_null($this->getValue($name))){
				Doctrine::getTable("SnsConfig")->set("op_micgadgets_plugin_".$name, $this->getValue($name));
			}
		}
	}
}
