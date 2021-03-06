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

class opMICGadgetsConfig{
	public static function getHomeMenuConfig(){
		return Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu', '');
	}
	public static function getHomeMenuType(){
		return Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu_type', 'default');
	}
	public static function getHomeMenuColumns(){
		return Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_menu_cols', 4);
	}
	public static function getEventCategoryConfig(){
		return Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_ov_event_category', '');
	}
	public static function getSmtTimelineCommentDeleteConfig(){
		return Doctrine::getTable('SnsConfig')->get('op_micgadgets_plugin_dsl_smttimeline_comment_delete', '1');
	}
}