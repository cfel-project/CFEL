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

class activityextActions extends opJsonApiActions{

	protected function getFileinfo($request, $tmpfile){
		$metadata = stream_get_meta_data($tmpfile);
		$filename = $request['filename'];
		$tmp_name = $metadata['uri'];
		$sdata = $request['data'];
		$data = base64_decode(substr($sdata, strpos($sdata, ",") + 1));
		fwrite($tmpfile, $data);
		return array(
			'name' => $filename,
			'tmp_name' => $tmp_name,
			'web_base_path' => $request->getUriPrefix().$request->getRelativeUrlRoot(),
			'type' => $request['type'],
			'member_id' => $this->getUser()->getMemberId(),
			'binary' => $data,
			'size' => strlen($data)
		);
	}
	protected function isValidUpload(sfWebRequest $r){
		return isset($r['type']) && isset($r['filename']) && isset($r['data']);
	}
	protected function getOpTimelineInstance($request){
		$user = new opTimelineUser();
		$params = array(
			'image_size' => $this->getRequestParameter('image_size', 'large'),
			'base_url' => $request->getUriPrefix().$request->getRelativeUrlRoot(),
		);
		return new opTimeline($user, $params);
	}
	public function executePostImage(sfWebRequest $request){
		$target = $request['target'];
		$trg_id = $request['target_id'];
		if("POST" == $request->getMethod() && isset($trg_id) && $this->isValidUpload($request)){
			$tmp = tmpfile();
			$fileinfo = $this->getFileInfo($request, $tmp);
		 	$timeline = $this->getOpTimelineInstance($request);
			$activity_image = $timeline->createActivityImageByFileInfoAndActivityId($fileinfo, $trg_id);
			fclose($tmp);
			$ret = array(
				'status' => 'success',
				'id' => $activity_image->getId(),
				'uri' => $activity_image->getUri(),
			);
			return $this->renderJSON($ret);
		}else{
			$this->forward400('insufficient request param or form');
		}
	}
	public function executePostWithImage(sfWebRequest $request){
		if("POST" == $request->getMethod() && $this->isValidUpload($request)){
			$tmp = tmpfile();
			$fileinfo = $this->getFileInfo($request, $tmp);
			$_FILES['timeline-submit-upload'] = $fileinfo;
		}
		$this->forward("activity", "post");
	}
}
