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

class opMICExtUtil{
	public static function requestToConditionParam(sfWebRequest $request){
		$ret = array();
		foreach(array("target", "tid", "exclude", "start", "end") as $key){
			$val = $request->getParameter($key);
			if(!empty($val)){
				$ret[$key] = $val;
			}
		}
		return $ret;
	}
	public static function getSQLConditions($params, $t_alias){
		if(empty($t_alias)){
			$t_alias = "";
		}else{
			$t_alias = $t_alias.".";
		}
		
		$cond = array();
		$target = $params["target"];
		$tid = $params["tid"];
		if("community" === $target){
			if(!empty($tid) && is_numeric($tid)){
				$cond[] = $t_alias."member_id in (select member_id from community_member where community_id=".$tid.")";
			}
		}else if("member" === $target){
			if(!empty($tid) && is_numeric($tid)){
				$cond[] = $t_alias."member_id =".$tid;
			}
		}
		$exclude = $params["exclude"];
		if(!empty($exclude) && is_numeric($exclude)){
			$cond[] = $t_alias."member_id != ".$exclude;
		}
		$start = $params["start"];
		if(!empty($start) && is_numeric($start)){
			$cond[] = $t_alias."updated_at >= '".date("Y-m-d H:i:s", $start/1000)."'";
		}
		$end = $params["end"];
		if(!empty($end) && is_numeric($end)){
			$cond[] = $t_alias."updated_at < '".date("Y-m-d H:i:s", $end/1000)."'";
		}
		return $cond;
	}

	public static function getActivityStatsAndRelations(sfWebRequest $request){
		$t_activity = Doctrine::getTable("ActivityData");
		$con = $t_activity->getConnection();
		$cond = opMICExtUtil::getSQLConditions(
			opMICExtUtil::requestToConditionParam($request),
			 "a");
		$cond[] = "a.uri is null";
		$members = $con->fetchAll("select a.member_id as id, count(*) as count, m.name as name, f.name as image from activity_data as a left join member as m on (a.member_id = m.id) left join member_image as mi on (a.member_id = mi.member_id and mi.is_primary=true) left join file as f on (mi.file_id = f.id)".(empty($cond) ? "" : " where ".join(" and ", $cond))." group by a.member_id order by count asc");

		$relations = $con->fetchAll("select a.member_id as src, b.member_id as trg, count(*) as count from activity_data as a left join activity_data as b on (a.in_reply_to_activity_id = b.id or a.in_reply_to_activity_id = b.in_reply_to_activity_id) where not a.in_reply_to_activity_id is null and a.member_id != b.member_id".(empty($cond) ? "" : " and ".join(" and ", $cond))." group by src, trg order by count desc");

		return array(
			"members" => $members,
			"relations" => $relations,
		);
	}

	public static function getActivitiesByDate(sfWebRequest $request){
		$t_activity = Doctrine::getTable("ActivityData");
		$con = $t_activity->getConnection();
		$cond = opMICExtUtil::getSQLConditions(
			opMICExtUtil::requestToConditionParam($request));
		$cond[] = "uri is null";
		return $con->fetchAll("select date(updated_at) as date, count(*) as count from activity_data".(empty($cond) ? "" : " where ".join(" and ", $cond))." group by date order by date asc");
	}
}