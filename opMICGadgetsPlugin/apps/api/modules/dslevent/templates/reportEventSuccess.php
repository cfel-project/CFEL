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
if(isset($sendObj)){
	$body = "イベントの企画者から更新通知が送信されました。詳細は以下のリンクから確認してください。\n\n".
			implode("\n", array(
				'[リンク] : '.(public_path($sendObj["path"],true)),
				'',
				'[企画者] : '.$event->getMember()->getName(),
				'[タイトル] : '.$event->getName(),
				'[開催日時] : '.op_format_date($event->getOpenDate(), 'D').($event->getOpenDate() ? ' '.$event->getOpenDateComment() : ''),
				'[予約区分] : '.$event->getArea(),
				'[本文]　: '.$event->getBody(),
				'',
				'[コメント]'))."\n".$sendObj["comments"];
	$sent = opMailSend::execute($sendObj["subject"]
			, explode(",",$sendObj["to"])
			, $sendObj["from"]
			, $body);
	return array(
		'to' => $sendObj["to"],
		'status' => 'success'
	);
}