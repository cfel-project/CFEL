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
class dsleventComponents extends sfComponents{
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
		return sfView::SUCCESS;
	}
	public function executeSmtEventList(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executMyEventList(sfWebRequest $request){
		$this->targetKey = "my";
		$this->u_name = $this->getUser()->getMember()->getName();
		return sfView::SUCCESS;
	}
	public function executeSmtMyEventList(sfWebRequest $request){
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
}
