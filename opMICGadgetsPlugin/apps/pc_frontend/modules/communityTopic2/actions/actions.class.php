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
class communityTopic2Actions extends opCommunityTopicPluginTopicActions{
//community instance is set via d_topic@routing.yml
	public function executeNew(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtNew");
		return parent::executeNew($request);
	}

	public function executeSmtNew(sfWebRequest $request){
		$this->form = new CommunityTopicForm();
		return sfView::SUCCESS;
	}

	public function executeCreate(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtCreate");
		return parent::executeCreate($request);
	}

	public function executeSmtCreate(sfWebRequest $request){
		$this->form = new CommunityTopicForm();
		$this->form->getObject()->setMemberId($this->getUser()->getMemberId());
		$this->form->getObject()->setCommunity($this->community);
		$this->processForm($request, $this->form);

		$this->setTemplate("smtNew");

		return sfView::SUCCESS;
	}
	public function executeEdit(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtEdit");
		return parent::executeEdit($request);
	}
	public function executeSmtEdit(sfWebRequest $request){
		$this->form = new CommunityTopicForm($this->communityTopic);
		return sfView::SUCCESS;
	}

	public function executeUpdate(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtUpdate");
		return parent::executeUpdate($request);
	}
	public function executeSmtUpdate(sfWebRequest $request){
		$this->form = new CommunityTopicForm($this->communityTopic);
		$this->processForm($request, $this->form);

		$this->setTemplate('smtEdit');

		return sfView::SUCCESS;
	}
	public function executeDelete(sfWebRequest $request){
		$request->checkCSRFProtection();
		$communityId = $this->communityTopic->getCommunity()->getId();
		$this->communityTopic->delete();
		$this->getUser()->setFlash("notice", "The %community% topic was deleted successfully.");
		$this->redirect("@dsltopic_list_community?id=".$communityId);
	}
	public function postExecute(){
		if($this->community instanceof Community){
			sfConfig::set("sf_nav_type", "community");
			sfConfig::set("sf_nav_id", $this->community->getId());
		}
	}
}
