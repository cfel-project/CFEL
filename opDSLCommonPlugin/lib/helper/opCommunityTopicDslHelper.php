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
use_helper('opCommunityTopic');

function op_api_community_topic_dsl($topic){
	$member = sfContext::getInstance()->getUser()->getMember();
	$_topic = op_api_community_topic($topic);
	$_topic['editable'] = $topic->isEditable($member->getId());
	$_topic['last_visit'] = opDSLGadgetsPluginUtil::getLastTopicVisitTime($member, $topic->getId());
	$_topic['topic_updated'] = $topic->getTopicUpdatedAt();
	$images = $topic->getImages();
	if(count($images)){
		foreach($images as $image){
			$_topic['images'][] = op_api_topic_image($image);
		}
	}
	return $_topic;
}

