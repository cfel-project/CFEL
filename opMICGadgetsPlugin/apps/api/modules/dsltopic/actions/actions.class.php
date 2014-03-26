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

class dsltopicActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
	}

	public function executeDelete(sfWebRequest $request){
		$request->checkCSRFProtection();
		$topicId = $request["id"];
		$this->forward404If(empty($topicId), "a topic id is not specified");
		$topic = Doctrine::getTable("CommunityTopic")->findOneById($topicId);
		$this->forward404If(empty($topic));
		$this->forward400If(false === $topic->isEditable($this->getUser()->getMemberId()), "this topic is not yours.");

		$this->communityId = $topic->getCommunity()->getId();
		$topic->delete();

		$activity = Doctrine::getTable('ActivityData')->findOneByUri("@communityTopic_show?id=".$topicId);
		if($activity && $activity->getMemberId() === $this->getUser()->getMemberId()){
			$activity->delete();
		}
	}

	public function executePdf(sfWebRequest $request){
		$topicId = $request["id"];
		$topic = Doctrine::getTable("CommunityTopic")->findOneById($topicId);
		$topic_pdf = Doctrine::getTable("CommunityTopicPDF")->findOneByPostId($topic->getId());
		$this->forward404If(empty($topic_pdf));

		$file = $topic_pdf->getFile();

		opMICGadgetsUtil::downloadFile($file);
	}
}
