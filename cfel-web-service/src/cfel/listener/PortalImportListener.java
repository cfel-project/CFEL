/*******************************************************************************
 * Copyright (c) 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/

package cfel.listener;

import java.util.Timer;

import javax.servlet.ServletContext;
import javax.servlet.ServletContextEvent;
import javax.servlet.ServletContextListener;
import javax.servlet.annotation.WebListener;

import cfel.service.Config;
import cfel.service.PortalImportTask;

/**
 * Application Lifecycle Listener implementation class PortalImportListener
 * 
 */
@WebListener
public class PortalImportListener implements ServletContextListener {

	private static final int TIMER_INTERVAL = 60 * 60 * 1000;
	private Timer mTimer = null;

	/**
	 * Default constructor.
	 */
	public PortalImportListener() {
	}

	/**
	 * @see ServletContextListener#contextInitialized(ServletContextEvent)
	 */
	public void contextInitialized(ServletContextEvent event) {
		ServletContext servletContext = event.getServletContext();
		new Config(servletContext);
		(mTimer = new Timer(true)).schedule(new PortalImportTask(servletContext), 0, TIMER_INTERVAL);
	}

	/**
	 * @see ServletContextListener#contextDestroyed(ServletContextEvent)
	 */
	public void contextDestroyed(ServletContextEvent event) {
		mTimer.cancel();
	}

}
