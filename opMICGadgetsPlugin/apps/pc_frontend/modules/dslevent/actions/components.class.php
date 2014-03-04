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
class dsleventComponents extends sfComponents{
	protected function processListRequestParams(sfWebRequest $request){
		$dur_labels = array(
			"d"=>"日",
			"w"=>"週",
		);
		$dur_days = array(
			"d" => 1,
			"w" => 7,
		);
		$dur = $request->getParameter("dur");
		if(!empty($dur)){
			$dur_type = substr($dur, 0, 1);
			$dur_len = (int)substr($dur, 1);
			$suffix = (1 == $dur_len ? "" : $dur_len).$dur_labels[$dur_type];
			$this->label_prev = "前の".$suffix;
			$this->label_next = "次の".$suffix;
			$this->days = $dur_len * $dur_days[$dur_type];
			$this->dur_type = $dur_type;
		}
	}
	public function executeMyEventGrid(sfWebRequest $request){
		$this->targetKey = "my";
		$this->u_name = $this->getUser()->getMember()->getName();
		return sfView::SUCCESS;
	}
	public function executeSmtMyEventGrid(sfWebRequest $request){
		$this->targetKey = "my";
		$this->u_name = $this->getUser()->getMember()->getName();
		return sfView::SUCCESS;
	}
	public function executeEventList(sfWebRequest $request){
		$this->processListRequestParams($request);
		return sfView::SUCCESS;
	}
	public function executeSmtEventList(sfWebRequest $request){
		$this->processListRequestParams($request);
		return sfView::SUCCESS;
	}
	public function executMyEventList(sfWebRequest $request){
		$this->processListRequestParams($request);
		$this->targetKey = "my";
		$this->u_name = $this->getUser()->getMember()->getName();
		return sfView::SUCCESS;
	}
	public function executeSmtMyEventList(sfWebRequest $request){
		$this->processListRequestParams($request);
		$this->targetKey = "my";
		$this->u_name = $this->getUser()->getMember()->getName();
		return sfView::SUCCESS;
	}
	protected function setCommunityInstance(sfWebRequest $request){
		if(empty($this->community)){
			$cid = $request->getParameter("id");
			if(empty($cid)){
				$paths = split("/", $request->getPathInfo());
				$cid = $paths[count($paths) - 1];
			}
			if("New" != $cid){
				$this->community = opDSLCommunityUtil::getCommunityByID($cid);
			}
		}
	}

	protected function setCommunityId(sfWebRequest $request){
		$this->setCommunityInstance($request);
		if(!empty($this->community)){
			$this->communityId = $this->community->getId();
		}
	}

	protected function setCommunityTarget(sfWebRequest $request){
		$this->setCommunityId($request);
		$this->targetKey = "community";
		$this->targetId = $this->communityId;
		$this->moreUrl = "dslevent/listCommunity";
		$this->moreUrlPath = $this->communityId;
	}

	public function executeCommunityEventSummary(sfWebRequest $request){
		$this->limit = 4;
		$this->setCommunityTarget($request);
		return sfView::SUCCESS;
	}
	public function executeSmtCommunityEventSummary(sfWebRequest $request){
		$this->limit=4;
		$this->setCommunityTarget($request);
		return sfView::SUCCESS;
	}

	public function executeEventSummary(sfWebRequest $request){
		$this->limit = 4;
		return sfView::SUCCESS;
	}

	public function executeSmtEventSummary(sfWebRequest $request){
		$this->limit = 4;
		return sfView::SUCCESS;
	}

	protected function setEventCategoryOptions(sfWebRequest $request){
		$sOptions = trim(opMICGadgetsConfig::getEventCategoryConfig());
		if(!empty($sOptions)){
			$entries = json_decode($sOptions, true);
			$names = $entries[$this->community->getId()];
			if(empty($names)){
				$names = $entries["*"];
			}
			if(!empty($names)){
				$ret = array();
				foreach($names as $name){
					$ret[] = array(
						"name" => $name,
						"title" => $name,
					);
				}
				$this->optionsjson = json_encode($ret);
			}
		}
	}
	
	protected function setCommunityEventInstance(sfWebRequest $request){
		if(empty($this->communityEvent)){
			$eid = $request->getParameter("id");
			if(empty($eid)){
				$paths = split("/", $request->getPathInfo());
				$eid = $paths[count($paths) - 1];
				if (strpos($eid, "#")!==false) {
					$eid = substr($cid, 0, strpos($eid, "#"));
				}
			}
			$this->communityEvent = opDSLCommunityUtil::getCommunityEventByID($eid);
		}
	}
	
	public function executeComEventFormFix(sfWebRequest $request){
		$this->setCommunityInstance($request);
		$this->setEventCategoryOptions($request);
		return sfView::SUCCESS;
	}

	public function executeSmtComEventFormFix(sfWebRequest $request){
		$this->setCommunityInstance($request);
		$this->setEventCategoryOptions($request);
		return sfView::SUCCESS;
	}

	protected function setEventNotificationSendParam(sfWebRequest $request){
		if(isset($this->communityEvent) && $this->getUser()->getMember()->getId() == $this->communityEvent->getMember()->getId()){
			$commId = isset($this->community) ? $this->community->getId() : $this->communityEvent->getCommunity()->getId();
			$this->sendParam = array(
				'commId' => $commId,
				'eventId' => $this->communityEvent->getId()
			);
		}
	}
	public function executeComEventShowFix(sfWebRequest $request){
		$this->setCommunityEventInstance($request);
		$this->setEventNotificationSendParam($request);
	}
	
	public function executeSmtOverrideShowEvent(sfWebRequest $request){
		$this->setCommunityEventInstance($request);
		$this->setEventNotificationSendParam($request);
		return sfView::SUCCESS;
	}
}