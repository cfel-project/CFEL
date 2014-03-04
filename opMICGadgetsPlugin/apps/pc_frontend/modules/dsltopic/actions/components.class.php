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
class dsltopicComponents extends sfComponents{
	
	public function executeSmtOverrideShowTopic(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeOverrideDeleteConfirm(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeOverrideShowTopic(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeOverrideEmbedYoutube(sfWebRequest $request){
		return sfView::SUCCESS;
	}
}
