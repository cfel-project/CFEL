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
function op_api_member_ext($member, $lastones){
	$ret = op_api_member($member);
	$lastone = $lastones[$member->getId()];
	$ret["last_login"] = $member->getLastLoginTime();
	if(!empty($lastone)){
		$ret["last_cnfrm"] = array(
			"id"=>$lastone["id"],
			"value"=>$lastone["value"],
			"time"=>strtotime($lastone["updated_at"]),
		);
	}
	return $ret;
}
