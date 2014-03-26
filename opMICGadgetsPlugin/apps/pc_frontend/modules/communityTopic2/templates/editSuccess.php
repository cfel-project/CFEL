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
$options = array();
$options['title'] = __('Edit the topic');
$options['url'] = url_for('d_topic/update').'/'.$communityTopic["id"];
$options['isMultipart'] = true;
op_include_form('formCommunityTopic', $form, $options);
include("__addPDFAttachment.php");
op_include_parts("buttonBox", "toDelete", array(
	"title" => __("Delete the topic and comments"),
	"button" => __("Delete"),
	"url" => url_for("communityTopic_delete_confirm", $communityTopic),
	"method" => "get",
));
?>
