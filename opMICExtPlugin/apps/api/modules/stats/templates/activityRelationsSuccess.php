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
use_helper("Javascript", "opUtil", "opAsset", "opConfirmationLog");
$nodes = array();
if(count($result["members"])){
	foreach($result["members"] as $member){
		$nodes[] = array(
			"id" => $member["id"],
			"count" => 1 * $member["count"],
			"name" => $member["name"],
			"image" => (empty($member["image"]) ? op_image_path("no_image.gif", true) : sf_image_path($member["image"], array("size" => "48x48"))),
			"prof_url" => app_url_for('pc_frontend', array('sf_route' => 'obj_member_profile', 'id' => $member["id"]), true),
		);
	}
}
$links = array();
if(count($result["relations"])){
	foreach($result["relations"] as $relation){
		$links[] = array(
			"src" => $relation["src"],
			"trg" => $relation["trg"],
			"count" => 1 * $relation["count"],
		);
	}
}
return array(
	"nodes" => $nodes,
	"links" => $links,
);
