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
class communityTopic2Actions extends opCommunityTopicPluginTopicActions{
//community instance is set via d_topic@routing.yml
	public function executeShow(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtShow");
		$this->forward("communityTopic", "show");
	}

	public function executeSmtShow(sfWebRequest $request){
//		return parent::executeSmtShow($request);
		$this->id = $request['id'];
		opSmartphoneLayoutUtil::setLayoutParameters(array('community' => $this->community));

		return sfView::SUCCESS;
	}

	public function executeNew(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtNew");
		return parent::executeNew($request);
	}

	public function executeSmtNew(sfWebRequest $request){
		$this->form = new CommunityTopicForm();
		return sfView::SUCCESS;
	}

	public function executeCreate(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtCreate");
		return parent::executeCreate($request);
	}

	public function executeSmtCreate(sfWebRequest $request){
		$this->form = new CommunityTopicForm();
		$this->form->getObject()->setMemberId($this->getUser()->getMemberId());
		$this->form->getObject()->setCommunity($this->community);
		$this->processForm($request, $this->form);

		$this->setTemplate("smtNew");

		return sfView::SUCCESS;
	}
	private function getTopicPDF(sfWebRequest $request){
		if(!empty($this->communityTopic)){
			return Doctrine::getTable("CommunityTopicPDF")->findOneByPostId($this->communityTopic->getId());
		}else{
			return null;
		}
	}
	private function deleteTopicPDF(sfWebRequest $request){
		$prev_pdf = $this->getTopicPDF($request);
		if(!empty($prev_pdf)){
			$prev_pdf->getFile()->delete();
			$prev_pdf->delete();
		}
	}
	private function setPDFInfo(sfWebRequest $request){
		$topic_pdf = $this->getTopicPDF($request);
		if(!empty($topic_pdf)){
			$this->pdf_name = $topic_pdf->getFile()->getOriginalFilename();
			$this->pdf_topic = $topic_pdf->getCommunityTopic()->getId();
		}
	}
	public function executeEdit(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtEdit");
		$this->setPDFInfo($request);

		return parent::executeEdit($request);
	}
	public function executeSmtEdit(sfWebRequest $request){
		$this->form = new CommunityTopicForm($this->communityTopic);
		$this->setPDFInfo($request);
		return sfView::SUCCESS;
	}

	protected function processForm($request, sfForm $form){
		$form_name = $form->getName();
		$form->bind(
			$request->getParameter($form_name),
			$request->getFiles($form_name)
		);
		$is_pdf_delete = $request->getPostParameter("community_topic_pdf_delete");
		if("on" === $is_pdf_delete){
			$this->deleteTopicPDF($request);
		}
		if($form->isValid()){
			$communityTopic = $form->save();

			$__extfiles = $request->getFiles($form_name."_ext");
			if(0 < count($__extfiles)){
				$validator = new sfValidatorFile();
				if(!empty($__extfiles["pdf"]["pdf"]["type"])){
					$this->deleteTopicPDF($request);
					$v_pdf = $validator->clean($__extfiles["pdf"]["pdf"]);
					$file = new File();
					$file->setFromValidatedFile($v_pdf);
					$file->name= "tpp_".$communityTopic->member_id.$file->name;
					$file->save();

					$compdf = new CommunityTopicPdf();
					$compdf->setCommunityTopic($communityTopic);
					$compdf->setFile($file);
					$compdf->save();
				}
			}
			$this->redirect("@dsl_showtopic_override?id=".$communityTopic->getId());
		}
	}

	public function executePdfDownload(sfWebRequest $request){
		$topic_pdf = Doctrine::getTable("CommunityTopicPDF")->findOneByPostId($this->communityTopic->getId());
		$this->forward404Unless(!empty($topic_pdf));
		$file = $topic_pdf->getFile();

		opMICGadgetsUtil::downloadFile($file);
	}

	public function executeUpdate(sfWebRequest $request){
		$this->forwardIf($request->isSmartphone(false), "communityTopic2", "smtUpdate");
		$pdf = $this->getTopicPDF($request);

		return parent::executeUpdate($request);
	}
	public function executeSmtUpdate(sfWebRequest $request){
		$this->form = new CommunityTopicForm($this->communityTopic);
		$this->processForm($request, $this->form);

		$this->setTemplate('smtEdit');

		return sfView::SUCCESS;
	}
	public function executeDelete(sfWebRequest $request){
		$request->checkCSRFProtection();
		$communityId = $this->communityTopic->getCommunity()->getId();
		$topicId = $this->communityTopic->getId();
		$this->communityTopic->delete();

		$activity = Doctrine::getTable('ActivityData')->findOneByUri("@communityTopic_show?id=".$topicId);
		if($activity && $activity->getMemberId() === $this->getUser()->getMemberId()){
			$activity->delete();
		}
		$this->deleteTopicPDF($request);
		$this->getUser()->setFlash("notice", "The %community% topic was deleted successfully.");
		$this->redirect("@dsltopic_list_community?id=".$communityId);
	}
	public function postExecute(){
		if($this->community instanceof Community){
			sfConfig::set("sf_nav_type", "community");
			sfConfig::set("sf_nav_id", $this->community->getId());
		}
	}
}
