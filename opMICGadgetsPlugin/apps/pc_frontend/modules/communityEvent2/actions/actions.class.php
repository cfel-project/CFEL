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
class communityEvent2Actions extends opCommunityTopicPluginEventActions{
	public function postExecute(){
		if($this->community instanceof Community){
			sfConfig::set("sf_nav_type", "community");
			sfConfig::set("sf_nav_id", $this->community->getId());
		}
	}
	public function executeNew(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityEvent2", "smtNew");
		return parent::executeNew($request);
	}

	public function executeSmtNew(sfWebRequest $request){
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
		parent::executeCreate($request);
		$this->setTemplate("smtNew");
		return sfView::SUCCESS;
	}

	public function executeEdit(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityEvent2", "smtEdit");
		return parent::executeEdit($request);
	}
	public function executeSmtEdit(sfWebRequest $request){
		$this->form = new CommunityEventForm($this->communityEvent);
		return sfView::SUCCESS;
	}
	public function executeDelete(sfWebRequest $request){
		$request->checkCSRFProtection();
		$this->forward404Unless($this->communityEvent->isEditable($this->getUser()->getMemberId()));
		$eventId = $this->communityEvent->getId();
		$this->communityEvent->delete();
		$activity = Doctrine::getTable('ActivityData')->findOneByUri("@communityEvent_show?id=".$eventId);
		if($activity && $activity->getMemberId() === $this->getUser()->getMemberId()){
			$activity->delete();
		}

		$this->getUser()->setFlash("notice", "The %community% event was deleted successfully.");
		$this->redirect("@dslevent_list_community?id=".$this->community->getId());
	}
}
