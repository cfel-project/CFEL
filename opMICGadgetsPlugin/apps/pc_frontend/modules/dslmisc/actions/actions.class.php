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
class dslmiscActions extends sfActions{
	public function executeForwardUri(sfWebRequest $request){
		$uri = str_replace("@communityTopic_show", "@dsl_showtopic_override", $request["uri"]);
		$this->forward404Unless(!empty($uri), "invalid param.");
		$this->redirect($uri);
	}
}
