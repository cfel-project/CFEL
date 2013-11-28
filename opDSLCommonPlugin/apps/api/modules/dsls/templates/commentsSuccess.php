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
use_helper('opCommunityTopic');

$data = array();

if(0 < count($comments)){
	foreach($comments as $com){
		$entry = op_api_community_topic_comment($com);
		$entry["deletable"] = $com->isDeletable($memberId);
		$entry["number"] = $com->getNumber();
		$images = $com->getImages();
		if(0 < count($images)){
			foreach($images as $image){
				$entry_['images'][] = op_api_topic_image($image);
			}
		}
		$data[] = $entry;
	}
	$data = array_reverse($data);
}

return array(
	'status' => 'success',
	'data' => $data
);
