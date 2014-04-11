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

class opDSLCommonConfig{
	public static function getSendTopicCommunityList(){
		return Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_list_sendtopic_comms', '');
	}
	public static function getElementLoggerConfig(){
		return Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_elem_logger_config','');
	}
	public static function getCommunityTopicListConfig(){
		return Doctrine::getTable('SnsConfig')->get('op_dslgadgets_plugin_comm_topiclist_config','');
	}
}
