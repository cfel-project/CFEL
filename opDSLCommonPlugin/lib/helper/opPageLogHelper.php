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
function op_api_page_log($log){
	return array(
		"time_s" => 1000 * strtotime($log->getUpdatedAt()),
		"time_c" => $log->getClientEpoc(),
		"event" => $log->getEvent(),
		"url" => $log->getUrl(),
		"member_id" => $log->getMemberId(),
	);
}

function op_api_page_log_deep($log){
	return array(
		"time_s" => 1000 * strtotime($log->getUpdatedAt()),
		"time_c" => $log->getClientEpoc(),
		"event" => $log->getEvent(),
		"url" => $log->getUrl(),
		"member" => op_api_member($log->getMember()),
	);
}

function op_api_element_log($log){
	return array(
		"time_s" => 1000 * strtotime($log->getUpdatedAt()),
		"time_c" => $log->getClientEpoc(),
		"event" => $log->getEvent(),
		"selector" => $log->getSelector(),
		"props" => json_decode($log->getProps(), true),
		"member_id" => $log->getMemberId(),
	);
}

function op_api_element_log_deep($log){
	return array(
		"time_s" => 1000 + strtotime($log->getUpdatedAt()),
		"time_c" => $log->getClientEpoc(),
		"event" => $log->getEvent(),
		"selector" => $log->getSelector(),
		"props" => json_decode($log->getProps(), true),
		"member" => op_api_member($log->getMember()),
	);
}

