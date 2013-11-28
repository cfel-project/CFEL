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
class dslstyleComponents extends sfComponents{
	
	public function executeSmtOverrideInformationBox(sfWebRequest $request){
		return sfView::SUCCESS;
	}

	public function executeOverrideInformationBox(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeOverrideComments(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeSmtOverrideComments(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeSmtPageMoveIndicator(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeSmtOverrideStyles(sfWebRequest $request){
		return sfView::SUCCESS;
	}
}
