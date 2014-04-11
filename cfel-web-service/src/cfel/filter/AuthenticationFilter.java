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

package cfel.filter;

import java.io.IOException;

import javax.servlet.Filter;
import javax.servlet.FilterChain;
import javax.servlet.FilterConfig;
import javax.servlet.ServletException;
import javax.servlet.ServletRequest;
import javax.servlet.ServletResponse;
import javax.servlet.annotation.WebFilter;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;

import org.apache.wink.json4j.JSONObject;

import cfel.service.Config;
import cfel.service.PortalImportTask;
import cfel.util.WSClientUtil;

/**
 * Servlet Filter implementation class AuthenticationFilter
 */
@WebFilter("/*")
public class AuthenticationFilter implements Filter {

	private static final String HEADER_API_KEY = "X-Eld-Land-APIKey";
	private static final String SESSION_API_KEY = "valid-apiKey";

	/**
	 * Default constructor.
	 */
	public AuthenticationFilter() {
	}

	/**
	 * @see Filter#destroy()
	 */
	public void destroy() {
	}

	/**
	 * @see Filter#doFilter(ServletRequest, ServletResponse, FilterChain)
	 */
	public void doFilter(ServletRequest request, ServletResponse response, FilterChain chain) throws IOException, ServletException {
		if (!checkApiKey((HttpServletRequest) request, (HttpServletResponse) response)) {
			return;
		}
		chain.doFilter(request, response);
	}

	/**
	 * @see Filter#init(FilterConfig)
	 */
	public void init(FilterConfig fConfig) throws ServletException {
	}

	private boolean checkApiKey(HttpServletRequest request, HttpServletResponse response) throws IOException {
		if (false) { // Temp fix for cookie problem
			if (request.getServletPath().startsWith("/file")) {
				return true;
			}
		}
		HttpSession session = request.getSession(true);
		String apiKey = request.getHeader(HEADER_API_KEY);
		Object sessionKey = session.getAttribute(SESSION_API_KEY);
		if (apiKey != null) {
			if (apiKey.equals(sessionKey)) {
				return true;
			}
		} else {
			if (sessionKey != null) {
				return true;
			} else {
				response.sendError(HttpServletResponse.SC_FORBIDDEN);
				return false;
			}
		}
		JSONObject obj = (JSONObject) WSClientUtil.getJSON(PortalImportTask.ELMEMBER_SEARCH_API + "?apiKey=" + apiKey);
		if (obj != null) {
			session.setAttribute(SESSION_API_KEY, apiKey);
			return true;
		} else {
			session.removeAttribute(SESSION_API_KEY);
			response.sendError(HttpServletResponse.SC_FORBIDDEN);
			return false;
		}
	}

	public static boolean isAdministrator(HttpServletRequest request) {
		HttpSession session = request.getSession();
		return session != null && Config.API_KEY.equals(session.getAttribute(SESSION_API_KEY));
	}
}
