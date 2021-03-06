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
use_helper("Date", "opConfirmationLog", "opMemberExt");

$data = array();
if(isset($members[0]["id"])){
	foreach($members as $member){
		$_member = op_api_member_ext($member, $lastones);
		$data[] = $_member;
	}
}
return array(
	"status" => "success",
	"num" => count($members),
	"data" => $data,
);
