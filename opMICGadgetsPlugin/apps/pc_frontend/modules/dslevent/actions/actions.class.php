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
class dsleventActions extends opCommunityTopicPluginEventActions{
	protected function setListTargetParams(sfWebRequest $request){
		if(empty($this->targetKey)){
			$this->targetKey = $request->getParameter("tky");
		}
		if(empty($this->targetId)){
			$this->target= $request->getParameter("tid");
		}
	}
	public function executeList(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(), 'dslevent', 'smtList');
		$this->setListTargetParams($request);
		return sfView::SUCCESS;
	}
	public function executeSmtList(sfWebRequest $request){
		$this->setListTargetParams($request);
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

	protected function buildACL(sfWebRequest $request){
		$this->acl = opCommunityTopicAclBuilder::buildCollection($this->community, array($this->getUser()->getMember()));
	}

	protected function setCommunityId(sfWebRequest $request){
		$this->setCommunityInstance($request);
		if(!empty($this->community)){
			$this->communityId = $this->community->getId();
		}
	}
	public function executeListCommunity(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(), 'dslevent', 'smtListCommunity');
		$this->setCommunityId($request);
		$this->communityName = $this->community->getName();
		$this->buildACL($request);
		return sfView::SUCCESS;
	}
	public function executeSmtListCommunity(sfWebRequest $request){
		$this->setCommunityId($request);
		$this->communityName = $this->community->getName();
		$this->buildACL($request);
		opSmartphoneLayoutUtil::setLayoutParameters(array('community' => $this->community));
		return sfView::SUCCESS;
	}
	public function postExecute(){
		if ($this->community instanceof Community){
			sfConfig::set('sf_nav_type', 'community');
			sfConfig::set('sf_nav_id', $this->community->getId());
		}
	}
}
