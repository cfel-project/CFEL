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
class statsComponents extends sfComponents{

	private function getMemberIdConditions(sfWebRequest $request, $t_alias){
		if(empty($t_alias)){
			$t_alias = "";
		}else{
			$t_alias = $t_alias.".";
		}
		
		$cond = array();
		$target = $request->getParameter("target");
		$tid = $request->getParameter("tid");
		if("community" === $target){
			if(!empty($tid) && is_numeric($tid)){
				$cond[] = $t_alias."member_id in (select member_id from community_member where community_id=".$tid.")";
			}
		}else if("member" === $target){
			if(!empty($tid) && is_numeric($tid)){
				$cond[] = $t_alias."member_id =".$tid;
			}
		}
		$exclude = $request->getParameter("exclude");
		if(!empty($exclude) && is_numeric($exclude)){
			$cond[] = $t_alias."member_id != ".$exclude;
		}
		return $cond;
	}
	private function getWhere(sfWebRequest $request){
		$cond = $this->getMemberIdConditions($request, "");
		if(0 < count($cond)){
			return " where ".join(" and ",$cond);
		}else{
			return "";
		}
	}

	public function executeStreamGraphByUrl(sfWebRequest $request){
		$t_pagelog = Doctrine::getTable("PageLog");
		$con = $t_pagelog->getConnection();
		$this->results = $con->fetchAll("select url, date(updated_at) as date, count(*) as count from page_log".$this->getWhere($request)." group by date, url order by date");

	}

	private function setPageLogElemByDate(sfWebRequest $request){
		$t_pagelog = Doctrine::getTable("PageLog");
		$con = $t_pagelog->getConnection();
		$this->results = $con->fetchAll("select member_id, event,url, date(updated_at) as date, count(*) as count from page_log".$this->getWhere($request)." group by date, member_id , event, url order by date");
	}

	public function executeBarGraphByEvent(sfWebRequest $request){
		$this->setPageLogElemByDate($request);
	}

	public function setMemberRelationsByActivity(sfWebRequest $request){
		$t_activity = Doctrine::getTable("ActivityData");
		$con = $t_activity->getConnection();
		$cond = $this->getMemberIdConditions($request, "a");
		$this->members = $con->fetchAll("select a.member_id as id, count(*) as count, m.name as name, f.name as image from activity_data as a left join member as m on (a.member_id = m.id) left join member_image as mi on (a.member_id = mi.member_id and mi.is_primary=true) left join file as f on (mi.file_id = f.id) where a.uri is null".(empty($cond) ? "" : " and ".join(" and ", $cond))." group by a.member_id order by count asc");

		$this->relations = $con->fetchAll("select a.member_id as src, b.member_id as trg, count(*) as count from activity_data as a left join activity_data as b on (a.in_reply_to_activity_id = b.id or a.in_reply_to_activity_id = b.in_reply_to_activity_id) where not a.in_reply_to_activity_id is null and a.member_id != b.member_id".(empty($cond) ? "" : " and ".join(" and ", $cond))." group by src, trg order by count desc");
	}
	public function executeRelationsByActivity(sfWebRequest $request){
		$this->setMemberRelationsByActivity($request);
	}
	public function executeSmtRelationsByActivity(sfWebRequest $request){
		$this->setMemberRelationsByActivity($request);
	}
}
