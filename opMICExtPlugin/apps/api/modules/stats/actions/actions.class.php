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

class statsActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
	}

	public function executeActivityRelations(sfWebRequest $request){
		$this->result = opMICExtUtil::getActivityStatsAndRelations($request);
	}

	public function executeActivitiesByDate(sfWebRequest $request){
		$this->results = opMICExtUtil::getActivitiesByDate($request);
	}
}
