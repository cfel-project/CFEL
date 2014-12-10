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

		$this->setWidget('brgr_cls_ext', new sfWidgetFormInput());
		$this->setDefault('brgr_cls_ext', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_brgr_cls_ext', ''));
		$this->setValidator('brgr_cls_ext', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('brgr_cls_ext', 'Bar graph cluster func extension');
		$this->widgetSchema->setHelp('brgr_cls_ext', 'Set bar graph cluster objects in JSON format (without outer [] clause');

		$target_types = array('all' => 'All', 'community' => 'Community', 'member' => 'Member');
		$this->setWidget('rel_query_target', new sfWidgetFormSelectRadio(array('choices' => $target_types)));
		$this->setDefault('rel_query_target', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_rel_query_target', 'all'));
		$this->setValidator('rel_query_target', new sfValidatorChoice(array('choices' => array_keys($target_types))));
		$this->widgetSchema->setLabel('rel_query_target', 'Visualization Query Target Type');
		$this->widgetSchema->setHelp('rel_query_target', 'Please choose query target');

		$this->setWidget('rel_query_target_id', new sfWidgetFormInput());
		$this->setDefault('rel_query_target_id', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_rel_query_target_id', ''));
		$this->setValidator('rel_query_target_id', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('rel_query_target_id', 'Visualization Query Target ID');
		$this->widgetSchema->setHelp('rel_query_target_id', 'Set id for the target type above.');

		$this->setWidget('rel_query_exclude_id', new sfWidgetFormInput());
		$this->setDefault('rel_query_exclude_id', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_rel_query_exclude_id', ''));
		$this->setValidator('rel_query_exclude_id', new sfValidatorString(array('required' => false, 'trim' => true)));
		$this->widgetSchema->setLabel('rel_query_exclude_id', 'Visualization Query exclude ID');
		$this->widgetSchema->setHelp('rel_query_exclude_id', 'Set id for the exclude member.');

		$query_durations = array('90d' => '90日分', '183d' => '半年分', '1y' => '一年分', 'all' => '全て');
		$this->setWidget('rel_query_duration', new sfWidgetFormSelectRadio(array('choices' => $query_durations)));
		$this->setDefault('rel_query_duration', Doctrine::getTable('SnsConfig')->get('op_micext_plugin_rel_query_duration', '90d'));
		$this->setValidator('rel_query_duration', new sfValidatorChoice(array('choices' => array_keys($query_durations))));
		$this->widgetSchema->setLabel('rel_query_duration', 'Visualization Query Duration(Default)');
		$this->widgetSchema->setHelp('rel_query_duration', 'Please choose default query duration for visualization');

		$this->widgetSchema->setNameFormat('op_micext_plugin[%s]');
	}
	
	public function save(){
//		parent::save();
		$names = array("d3_url", "brgr_cls_ext", "rel_query_target", "rel_query_target_id", "rel_query_exclude_id", "rel_query_duration");
		foreach($names as $name){
			if(!is_null($this->getValue($name))){
				Doctrine::getTable("SnsConfig")->set("op_micext_plugin_".$name, $this->getValue($name));
			}
		}
	}
}
