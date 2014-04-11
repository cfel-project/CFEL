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
class simpleAuthActions extends opAuthMailAddressPluginAction{
  public function executePasswordRecovery($request)
  {
    $this->form = new opSimplePasswordRecoveryForm();
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request['password_recovery']);
      if ($this->form->isValid())
      {
        $this->form->sendMail();

        $this->getUser()->setFlash('notice', 'Sent you a mail for completing your password recovery. If you cannot receive the mail, please retry a password recovery process.');
        $this->redirect('member/login');
      }
    }
  }

}
