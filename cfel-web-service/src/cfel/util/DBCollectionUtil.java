package cfel.util;

import java.util.Map;

import javax.servlet.http.HttpServletResponse;

import org.bson.types.ObjectId;

import cfel.service.DatabaseServive;

import com.mongodb.DBCollection;
import com.mongodb.DBCursor;
import com.mongodb.DBObject;
import com.mongodb.util.JSON;

public class DBCollectionUtil {
	public static final Object query(String type, Map<String, String> options) throws DBCollectionUtilException{
		DatabaseServive mDS = DatabaseServive.getInstance();
		DBCollection collection = mDS.getCollection(type);
		if (collection == null) {
			throw new DBCollectionUtilException(String.format("Unknown collection %s", type));
		}

		// Get document(s)
		String id = options.get("id");
		String keys = options.get("keys");
		DBObject keysObj = keys != null ? (DBObject) JSON.parse(keys) : null;
		Object result = null;
		if (id != null) {
			result = collection.findOne(new ObjectId(id), keysObj);
		} else {
			String query = options.get("query");
			String sort = options.get("sort");
			String skip = options.get("skip");
			String limit = options.get("limit");
			String count = options.get("count");
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
			throw new DBCollectionUtilException(String.format("Document %s does not exist", id));
		}
		
		return result;
	}
	public static final String query_serialized(String type, Map<String, String> options) throws DBCollectionUtilException{
		return JSON.serialize(query(type, options));
	}
}
