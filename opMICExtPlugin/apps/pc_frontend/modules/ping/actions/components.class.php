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
class pingComponents extends sfComponents{
	private function setProps(sfWebRequest $request){
		$this->member = $this->getUser()->getMember();
	}
	public function executePingUI(sfWebRequest $request){
		$this->setProps($request);
		return sfView::SUCCESS;
	}

	public function executeSmtPingUI(sfWebRequest $request){
		$this->setProps($request);
		return sfView::SUCCESS;
	}

	private function setPingStatistics($memberId){
		$start = strtotime("-7 day", date());
		$this->logs = opMICConfirmationLog::searchByMemberId($memberId, array(
			"start" => $start,
		));
	}
	public function executeProfileLog(sfWebRequest $request){
		$memberId = $request->getParameter("id", $this->getUser()->getMemberId());
		$this->setPingStatistics($memberId);
		return sfView::SUCCESS;
	}
	public function executeSmtProfileLog(sfWebRequest $request){
		$memberId = $request->getParameter("id", $this->getUser()->getMemberId());
		$this->setPingStatistics($memberId);
		return sfView::SUCCESS;
	}

	public function executePingMemberList(sfWebRequest $request){
		return sfView::SUCCESS;
	}

	public function executeSmtPingMemberList(sfWebRequest $request){
		return sfView::SUCCESS;
	}
}
