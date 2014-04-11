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

package cfel.servlet;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;

import javax.servlet.ServletConfig;
import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.bson.types.ObjectId;

import cfel.service.DatabaseServive;

import com.mongodb.BasicDBObject;
import com.mongodb.DBCollection;
import com.mongodb.DBCursor;
import com.mongodb.DBObject;
import com.mongodb.WriteResult;
import com.mongodb.gridfs.GridFSDBFile;
import com.mongodb.util.JSON;

/**
 * Servlet implementation class ServiceServlet
 * 
 * 
 * <pre>
 *  GET - Get resource with or without id
 *  
 *  URI:
 *      GET (context-root)/file - Get list of files
 *      GET (context-root)/file/filePath - Get a file
 *      GET (context-root)/collectionName - Get all documents
 *      GET (context-root)/collectionName/filePath - Get a document with filePath
 *  
 *  Optional parameters for DB:
 *      keys: Fields to return
 *      
 *  (when id is not specified)
 *      query: Object for which to search
 *      sort: Object for which to sort
 *      skip: Number of elements to skip
 *      limit: Number of elements to return
 *      count: Returns number of elements instead of DB Object
 *  
 * -------- 
 * 
 *  POST - Insert a new resource or perform special action
 *  
 *  URI:
 *      POST (context-root)/file/filePath - Create a file (file in POST body)
 *      POST (context-root)/collectionName - Insert a document (document in data parameter)
 *  (when action parameter is "update")
 *      POST (context-root)/collectionName - Update a document with query and update parameter
 *      POST (context-root)/collectionName/id - Update a document with id and update parameter
 *  
 *  Optional parameters:
 *      action: Document update action
 *      
 *  (when action parameter is "update")
 *      query: Object for which to search (not used when id is specified)
 *      update: Update action object
 *  
 * --------
 *  
 *  PUT - Update a resource with id
 *  
 *  URI:
 *      PUT (context-root)/file/filePath - Update a file (file in POST body)
 *      PUT (context-root)/collectionName - Update a document (document with id in data parameter)
 *      PUT (context-root)/collectionName/id - Update a document with id (document in data parameter)
 *  
 * -------- 
 * 
 *  DELETE - Delete a resource with id
 *  
 *  URI:
 *      DELETE (context-root)/file/filePath - Delete a file
 *      DELETE (context-root)/collectionName/id - Delete a document
 * </pre>
 */

@WebServlet("/service")
public class ServiceServlet extends HttpServlet {
	private static final long serialVersionUID = 1L;

	private DatabaseServive mDS = DatabaseServive.getInstance();

	/**
	 * @see HttpServlet#HttpServlet()
	 */
	public ServiceServlet() {
		super();
	}

	@Override
	public void init(ServletConfig config) throws ServletException {
		super.init(config);
		System.out.println(JSON.serialize(mDS.getCollectionNames()));
	}

	@Override
	protected void service(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		request.setCharacterEncoding("UTF-8");
		super.service(request, response);
	}

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse
	 *      response)
	 * 
	 *      Get resource
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		String type = request.getParameter("type");
		String id = request.getParameter("id");
		System.out.println("doGet: type=" + type + " id=" + id);

		if ("file".equals(type)) {
			if (id == null) {
				// Get list of files
				sendJSON(mDS.getFileList(), response);
				return;
			}

			// Get a file
			GridFSDBFile file = mDS.getFile(id);
			if (file == null) {
				response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("File %s does not exist", id));
				return;
			}
			String contentType = file.getContentType();
			if (contentType != null) {
				response.setContentType(contentType);
			}
			try {
				sendFile(id, response);
			} catch (Exception e) {
				System.err.println("Send error: " + id);
			}
			return;
		}

		DBCollection collection = mDS.getCollection(type);
		if (collection == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Unknown collection %s", type));
			return;
		}

		// Get document(s)
		String keys = request.getParameter("keys");
		DBObject keysObj = keys != null ? (DBObject) JSON.parse(keys) : null;
		Object result = null;
		if (id != null) {
			result = collection.findOne(new ObjectId(id), keysObj);
		} else {
			String query = request.getParameter("query");
			String sort = request.getParameter("sort");
			String skip = request.getParameter("skip");
			String limit = request.getParameter("limit");
			String count = request.getParameter("count");
			DBObject queryObj = query != null ? (DBObject) JSON.parse(query) : null;
			DBCursor cursor = collection.find(queryObj, keysObj);
			if (sort != null) {
				cursor = cursor.sort((DBObject) JSON.parse(sort));
			}
			if (skip != null) {
				cursor = cursor.skip(Integer.parseInt(skip));
			}
			if (limit != null) {
				cursor = cursor.limit(Integer.parseInt(limit));
			}
			result = "true".equals(count) ? cursor.count() : cursor;
		}
		if (id != null && result == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Document %s does not exist", id));
			return;
		}
		sendJSON(result, response);
	}

	/**
	 * @see HttpServlet#doPost(HttpServletRequest request, HttpServletResponse
	 *      response)
	 * 
	 *      Insert a new resource
	 */
	protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		String type = request.getParameter("type");
		String id = request.getParameter("id");
		String data = request.getParameter("data");
		System.out.println("doPost: type=" + type + " id=" + id + " data=" + data);

		if ("file".equals(type)) {
			// Save a file with id
			doPut(request, response);
			return;
		}

		DBCollection collection = mDS.getCollection(type);
		if (collection == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Unknown collection %s", type));
			return;
		}

		String action = request.getParameter("action");
		if (action != null) {
			// Manipulate database
			if ("update".equals(action)) {
				String query = request.getParameter("query");
				String update = request.getParameter("update");
				String upsert = request.getParameter("upsert");
				String multi = request.getParameter("multi");
				DBObject queryObj = null;
				if (id != null) {
					queryObj = new BasicDBObject("_id", new ObjectId(id));
				} else if (query != null) {
					queryObj = (DBObject) JSON.parse(query);
				}
				DBObject updateObj = update != null ? (DBObject) JSON.parse(update) : null;
				if (queryObj == null || updateObj == null) {
					response.sendError(HttpServletResponse.SC_BAD_REQUEST, "No query or update parameters");
					return;
				}
				sendJSON(collection.update(queryObj, updateObj, "true".equals(upsert), "true".equals(multi)), response);
			} else {
				response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Unknown action %s", action));
			}
			return;
		}

		if (data == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, "No data specified");
			return;
		}

		// Insert a document
		DBObject dataObject = (DBObject) JSON.parse(data);
		Object dataID = dataObject.get("_id");
		if (dataID != null && collection.findOne(dataID) != null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Duplicated id");
			return;
		}
		collection.insert(dataObject);
		sendJSON(dataObject, response);
	}

	/**
	 * @see HttpServlet#doPut(HttpServletRequest, HttpServletResponse)
	 * 
	 *      Update a resource with id
	 */
	protected void doPut(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		String type = request.getParameter("type");
		String id = request.getParameter("id");
		String data = request.getParameter("data");
		System.out.println("doPut: type=" + type + " id=" + id);

		if ("file".equals(type)) {
			// Save a file
			if (id == null) {
				response.sendError(HttpServletResponse.SC_BAD_REQUEST, "No file id specified");
				return;
			}
			InputStream is = null;
			try {
				mDS.saveFile(id, is = request.getInputStream(), request.getContentType());
			} finally {
				if (is != null) {
					is.close();
				}
			}
			return;
		}

		DBCollection collection = mDS.getCollection(type);
		if (collection == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Unknown collection %s", type));
			return;
		}
		if (data == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, "No data specified");
			return;
		}

		// Update a document
		DBObject dataObject = (DBObject) JSON.parse(data);
		Object idObj = dataObject.get("_id"); // = id in source
		if (idObj == null && id != null) {
			idObj = new ObjectId(id); // = id in URI
		}
		if (idObj == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, "No document id specified");
			return;
		}
		collection.update(new BasicDBObject("_id", idObj), dataObject, true, false);
		sendJSON(dataObject, response);
	}

	/**
	 * @see HttpServlet#doDelete(HttpServletRequest, HttpServletResponse)
	 * 
	 *      Delete a resource
	 */
	protected void doDelete(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		String type = request.getParameter("type");
		String id = request.getParameter("id");
		System.out.println("doDelete: type=" + type + " id=" + id);

		if (id == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, "Delete all is not supported");
			return;
		}

		if ("file".equals(type)) {
			// Delete a file
			if (mDS.getFile(id) == null) {
				response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("File %s does not exist", id));
				return;
			}
			mDS.deleteFile(id);
			return;
		}

		DBCollection collection = mDS.getCollection(type);
		if (collection == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Unknown collection %s", type));
			return;
		}

		// Delete a document
		DBObject obj = collection.findOne(new ObjectId(id));
		if (obj == null) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, String.format("Document %s does not exist", id));
			return;
		}
		sendJSON(collection.remove(obj), response);
	}

	/*
	 * 
	 */
	private void sendJSON(Object obj, HttpServletResponse response) throws IOException {
		response.setCharacterEncoding("UTF-8");
		response.setContentType("application/json");
		response.addHeader("Access-Control-Allow-Origin", "*");
		if (obj instanceof WriteResult) {
			String error = ((WriteResult) obj).getError();
			if (error != null) {
				obj = error;
			} else {
				obj = "OK";
			}
		}
		OutputStream os = null;
		try {
			(os = response.getOutputStream()).write(JSON.serialize(obj).getBytes("UTF-8"));
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			if (os != null) {
				os.close();
			}
		}
	}

	private void sendFile(String id, HttpServletResponse response) throws IOException {
		OutputStream os = null;
		try {
			mDS.readFile(id, os = response.getOutputStream());
		} finally {
			if (os != null) {
				os.close();
			}
		}
	}

}
