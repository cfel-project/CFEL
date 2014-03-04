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
class dslcamComponents extends sfComponents
{
	private function _set_file_size_info(){
		$this->fmt_file_max = opTimelinePluginUtil::getFileSizeMaxOfFormat();
		$this->file_max = opTimelinePluginUtil::getFileSizeMax();
	}
	public function executeTakePicture(sfWebRequest $request){
		$this->_set_file_size_info();
		return sfView::SUCCESS;
	}
	public function executeSmtFilePicker(sfWebRequest $request){
		$this->_set_file_size_info();
		return sfView::SUCCESS;
	}

	public function executeSmtAddPictureButton(sfWebRequest $request){
		$this->_set_file_size_info();
		return sfView::SUCCESS;
	}
	public function executeSmtAddFilePickButton(sfWebRequest $request){
		$this->_set_file_size_info();
		return sfView::SUCCESS;
	}
}
