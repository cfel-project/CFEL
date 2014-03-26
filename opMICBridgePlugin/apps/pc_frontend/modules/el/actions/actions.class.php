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
class elActions extends sfActions{
	private function setCommonProps(sfWebRequest $request){
		$this->token = $this->getUser()->getMemberApiKey();
		$this->baseUrl = opMICBridgeConfig::getEldLandBaseUrl();
	}
	public function executeElframe(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "el", "smtElframe");
		$this->setCommonProps($request);
	}
	public function executeEllink(sfWebRequest $request){
		$this->setCommonProps($request);
	}
	public function executeSmtElframe(sfWebRequest $request){
		$this->setCommonProps($request);
	}
}
