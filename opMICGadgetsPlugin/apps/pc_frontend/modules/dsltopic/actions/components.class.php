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
	private function getTopicPDF(sfWebRequest $request){
		if(!empty($this->communityTopic)){
			return Doctrine::getTable("CommunityTopicPDF")->findOneByPostId($this->communityTopic->getId());
		}else{
			return null;
		}
	}
	private function setPDFInformation(sfWebRequest $request){
		$topic_pdf = $this->getTopicPDF($request);
		if(!empty($topic_pdf)){
			$this->pdf_name = $topic_pdf->getFile()->getOriginalFilename();
			$this->pdf_url = url_for("d_topic/pdf")."/".$topic_pdf->getCommunityTopic()->getId();
		}
	}
	public function executeSmtOverrideShowTopic(sfWebRequest $request){
		$this->setPDFInformation($request);
		return sfView::SUCCESS;
	}
	public function executeOverrideDeleteConfirm(sfWebRequest $request){
		return sfView::SUCCESS;
	}
	public function executeOverrideShowTopic(sfWebRequest $request){
		$this->setPDFInformation($request);
		return sfView::SUCCESS;
	}
	public function executeOverrideEmbedYoutube(sfWebRequest $request){
		return sfView::SUCCESS;
	}
}
