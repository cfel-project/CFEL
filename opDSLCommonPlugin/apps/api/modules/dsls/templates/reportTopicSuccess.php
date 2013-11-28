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
if(isset($sendObj)){
	$body = implode("\n", array(
				$sendObj["msg"],
				'',
				(public_path($sendObj["path"],true)),
				'',
				'[トピック]',
				$topic->getMember()->getName().': '.$topic->getName(),
				$topic->getBody(),
				'',
				'[コメント]'))."\n".$sendObj["comments"];
	$sent = opMailSend::execute($sendObj["subject"]
			, $sendObj["to"]
			, $sendObj["from"]
			, $body);
	return array(
		'to' => $sendObj["to"],
		'status' => 'success',
		'res' => $sendObj["res"]
	);
}
