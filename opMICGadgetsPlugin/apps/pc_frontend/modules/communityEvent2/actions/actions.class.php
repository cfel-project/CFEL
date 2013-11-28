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
class communityEvent2Actions extends opCommunityTopicPluginEventActions{
	protected function setCommunityInstance(sfWebRequest $request){
		if(empty($this->community)){
			$paths = split("/", $request->getPathInfo());
			$cid = $paths[count($paths) - 1];
			if("New" != $cid){
				$this->community = opDSLCommunityUtil::getCommunityByID($cid);
			}
		}
		if(empty($this->acl) && !empty($this->community)){
			$this->acl = opCommunityTopicAclBuilder::buildCollection($this->community, array($this->getUser()->getMember()));
		}
	}

	public function executeNew(sfWebRequest $request){
		$this->setCommunityInstance($request);
		$this->forwardIf($request->isSmartphone(false), "communityEvent2", "smtNew");
		return parent::executeNew($request);
	}

	public function executeSmtNew(sfWebRequest $request){
		$this->setCommunityInstance($request);
		$this->forward404Unless(!empty($this->community));
		$this->form = new CommunityEventForm();

		opSmartphoneLayoutUtil::setLayoutParameters(array(
			'community' => $this->community,
		));
		return sfView::SUCCESS;
	}

	public function executeCreate(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityEvent2", "smtCreate");
		return parent::executeCreate($request);
	}
	public function executeSmtCreate(sfWebRequest $request){
		$this->setCommunityInstance($request);
		parent::executeCreate($request);
		$this->setTemplate("smtCreate");
		return sfView::SUCCESS;
	}

	public function postExecute(){
		if ($this->community instanceof Community){
			sfConfig::set('sf_nav_type', 'community');
			sfConfig::set('sf_nav_id', $this->community->getId());
		}
	}
}
