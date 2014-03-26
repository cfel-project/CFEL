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

class opMICConfirmationLog{
	public static function searchByMemberId($memberId, $options){
		$query = Doctrine::getTable("ConfirmationLog")->createQuery("q")
			->andWhere("member_id=?", $memberId)
			->orderBy("updated_at DESC");
		if(!empty($options["limit"])){
			$query->limit($options["limit"]);
		}
		if(!empty($options["start"])){
			$query->andWhere("updated_at>=?", $options["start"]);
		}
		if(!empty($options["end"])){
			$query->andWhere("updated_at<?", $options["end"]);
		}
		return $query->execute();
	}

	public static function searchLastEntries(){
		$t_log = Doctrine::getTable("ConfirmationLog");
		$con = $t_log->getConnection();
		return $con->fetchAll("select c1.* from confirmation_log as c1 left join confirmation_log as c2 on (c1.member_id = c2.member_id and c1.updated_at < c2.updated_at) where c2.member_id is null");
	}
}