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

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.UnknownHostException;
import java.util.Set;

import com.mongodb.DB;
import com.mongodb.DBCollection;
import com.mongodb.DBCursor;
import com.mongodb.MongoClient;
import com.mongodb.gridfs.GridFS;
import com.mongodb.gridfs.GridFSDBFile;
import com.mongodb.gridfs.GridFSInputFile;

public class DatabaseServive {
	private static DatabaseServive sDS;

	private DB mDB = null;
	private GridFS mFS = null;

	public static synchronized DatabaseServive getInstance() {
		if (sDS == null) {
			sDS = new DatabaseServive(Config.MONGO_HOST, Config.ALBUM_DB);
		}
		return sDS;
	}

	public DatabaseServive(String host, String dbName) {
		try {
			mFS = new GridFS(mDB = new MongoClient(host).getDB(dbName));
		} catch (UnknownHostException e) {
			e.printStackTrace();
		}
	}

	public Set<String> getCollectionNames() {
		return mDB != null ? mDB.getCollectionNames() : null;
	}

	public DBCollection getCollection(String collectionName) {
		return mDB != null && collectionName != null ? mDB.getCollection(collectionName) : null;
	}

	/*
	 * GridFS services
	 */
	public DBCursor getFileList() {
		return mFS != null ? mFS.getFileList() : null;
	}

	public GridFSDBFile getFile(String id) {
		return mFS != null ? mFS.findOne(id) : null;
	}

	public boolean saveFile(String id, InputStream is, String contentType) throws IOException {
		OutputStream os = getFileOutputStream(id, contentType);
		if (os != null) {
			try {
				byte data[] = new byte[4096];
				int len = 0;
				while ((len = is.read(data, 0, data.length)) > 0) {
					os.write(data, 0, len);
				}
				return true;
			} finally {
				os.close();
			}
		}
		return false;
	}

	public OutputStream getFileOutputStream(String id, String contentType) {
		if (mFS != null) {
			try {
				deleteFile(id);
				GridFSInputFile dbFile = mFS.createFile(id);
				if (contentType != null) {
					dbFile.setContentType(contentType);
				}
				return dbFile.getOutputStream();
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		return null;
	}

	public boolean deleteFile(String id) {
		if (getFile(id) == null) {
			return false;
		}
		mFS.remove(id);
		return true;
	}

	public boolean readFile(String id, OutputStream os) throws IOException {
		GridFSDBFile dbFile = getFile(id);
		if (dbFile == null) {
			return false;
		}
		InputStream is = null;
		try {
			is = dbFile.getInputStream();
			byte data[] = new byte[4096];
			int len = 0;
			while ((len = is.read(data, 0, data.length)) > 0) {
				os.write(data, 0, len);
			}
			return true;
		} finally {
			if (is != null) {
				is.close();
			}
		}
	}
}
