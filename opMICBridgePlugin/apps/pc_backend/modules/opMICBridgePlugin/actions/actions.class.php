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
class opMICBridgePluginActions extends sfActions{
  public function executeIndex(opWebRequest $request){
    $this->form = new opMICBridgePluginConfigurationForm();

    if ($request->isMethod(sfRequest::POST)){
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid()){
        $this->form->save();

        $this->getUser()->setFlash('notice', 'Saved configuration successfully.');

        $this->redirect('opMICBridgePlugin/index');
      }
    }
  }
}
