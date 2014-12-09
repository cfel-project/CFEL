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
	private function getWhere(sfWebRequest $request){
		$cond = opMICExtUtil::getSQLConditions(opMICExtUtil::requestToConditionParam($request), "");
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

	public function executeRelationsByActivity(sfWebRequest $request){
		$this->prmsjson = json_encode(opMICExtUtil::requestToConditionParam($request), JSON_UNESCAPED_UNICODE);
	}
	public function executeSmtRelationsByActivity(sfWebRequest $request){
		$this->prmsjson = json_encode(opMICExtUtil::requestToConditionParam($request), JSON_UNESCAPED_UNICODE);
	}


	public function executeActivitiesByDate(sfWebRequest $request){
		$this->prmsjson = json_encode(opMICExtUtil::requestToConditionParam($request), JSON_UNESCAPED_UNICODE);
	}
	public function execudeSmtActivitiesByDate(sfWebRequest $request){
		$this->prmsjson = json_encode(opMICExtUtil::requestToConditionParam($request), JSON_UNESCAPED_UNICODE);
	}

	private function setEmbedVislinkOptions(sfWebRequest $request){
		$this->timeline_vis_href = public_path("stats/userRelations", true);
		$query_options = array();
		$qt = opMICExtConfig::getRelVisQueryTarget();
		if(strcmp($qt, "all") !== 0){
			$query_options["target"] = $qt;
			$query_options["tid"] = opMICExtConfig::getRelVisQueryTargetId();
		}
		$qeid = opMICExtConfig::getRelVisQueryExcludeId();
		if(!empty($qeid)){
			$query_options["exclude"] = $qeid;
		}
		$this->q_opt_json = json_encode($query_options,JSON_UNESCAPED_UNICODE);
	}

	public function executeEmbedVislinkTimeline(sfWebRequest $request){
		$this->setEmbedVislinkOptions($request);
	}
	public function executeSmtEmbedVislinkTimeline(sfWebRequest $request){
		$this->setEmbedVislinkOptions($request);
	}
}
