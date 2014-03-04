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

package cfel.service;

import java.util.Enumeration;

import javax.servlet.ServletContext;

public class Config {

	public static String PORTAL_ROOT, PORTAL_URL, API_KEY, ALBUM_ROOT, MONGO_HOST, ALBUM_DB;

	public Config(ServletContext servletContext) {
		Enumeration<String> enumNames = servletContext.getInitParameterNames();
		while (enumNames.hasMoreElements()) {
			String name = enumNames.nextElement();
			System.out.println(name + "=" + servletContext.getInitParameter(name));
		}
		PORTAL_ROOT = servletContext.getInitParameter("portal_root");
		PORTAL_URL = servletContext.getInitParameter("portal_url");
		API_KEY = servletContext.getInitParameter("api_key");
		ALBUM_ROOT = servletContext.getInitParameter("album_root");
		MONGO_HOST = servletContext.getInitParameter("mongo_host");
		ALBUM_DB = servletContext.getInitParameter("album_db");
	}

}
