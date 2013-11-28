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
use_helper('opCommunityTopicDsl');
$data = array();
if (count($topics)){
	foreach ($topics as $topic){
		$data[] = op_api_community_topic_dsl($topic);
	}
}

return array(
	'status' => 'success',
	'total' => $topics_total,
	'offs' => $offs,
	'data' => $data,
);