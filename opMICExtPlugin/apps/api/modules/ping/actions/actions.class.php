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

class pingActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
	}

	public function executePost(sfWebRequest $request){
		$logEntry = new ConfirmationLog();
		$logEntry->setMemberId($this->getUser()->getMemberId());
		$logEntry->setValue($request["value"]);
		$logEntry->save();

		return $this->renderJSON(array(
			"status" => "success",
		));
	}
	public function executeMy(sfWebRequest $request){
		$options = array();
		$limit = $request->getParameter("limit");
		$start = $request->getParameter("start");
		$end = $request->getParameter("end");

		if(!empty($limit)){
			$options["limit"] = $limit;
		}
		if(!empty($start)){
			$options["start"] =  date("Y-m-d H:i:s", $start/1000);
		}
		if(!empty($end)){
			$options["end"] = date("Y-m-d H:i:s", $end/1000);
		}

		$this->logs = opMICConfirmationLog::searchByMemberId($this->member->getId(), $options);
		$this->setTemplate("search");
	}
	public function executeLast(sfWebRequest $request){
		$results = opMICConfirmationLog::searchLastEntries();
		return $this->renderJSON(array(
			"status"=> "success",
			"num" => count($results),
			"data" => $results,
		));
	}
}
