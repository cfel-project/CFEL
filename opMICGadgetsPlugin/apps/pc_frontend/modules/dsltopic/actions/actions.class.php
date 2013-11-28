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
class dsltopicActions extends opCommunityTopicPluginTopicActions{
	public function executeListCommunity(sfWebRequest $request){
		$this->communityId = $this->community->getId();
		$this->forwardIf($request->isSmartphone(false), 'dsltopic', 'smtListCommunity');
		return sfView::SUCCESS;
	}
	public function executeSmtListCommunity(sfWebRequest $request){
		$this->communityId = $this->community->getId();
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
