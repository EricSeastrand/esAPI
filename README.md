esAPI + esDB
=====

Makes creating new APIs fast, simple, and secure, by dynamically preparing SQL statements, based on GET/POST parameters. Get started in 4 easy steps!

**Clone** a copy of the *esAPI* repo
```bash
git clone git@github.com:willcodeforfood/esAPI.git
```

**Configure** the database connection, with your own database creds  
```bash
vim esAPI/esDB.conf.php
```

   **Create** your application, by defining *routes* in routes.json  
```bash
vim esAPI/routes.json
```

**Debug** your routes, by tailing the error_log file  
```bash
touch esAPI/error_log
chmod 0777 esAPI/error_log
tail -f esAPI/error_log
```

Examples
===

#### *SELECT* routes fetch specific fields/columns from your database and return the matched dataset

##### HTTP Request
> /esAPI/?action=Message_GetNew&messageId=150&convoId=3

##### Response
```json
{
	"ok":true,
	"result":[{
		"Message.message_id":159,
		"Message.content":"Anyone in here?",
		"Message.user_id":40,
		"User.name":"Gohan",
		"Message.time":1396279063
	}]
}
```

##### How to define this route in routes.json
```json
"Message_GetNew" : {
	"select" : "Message",
	"fields" : [
		"Message.message_id",
		"Message.content",
		"Message.user_id",
		"User.name",
		"unix_timestamp(Message.timestamp) as Message.time"
	],
	"join": {
		"Message.user_id" : "User.user_id"
	},
	"where" : {
		"Message.convo_id": "_convoId",
		"Message.message_id": ">messageId"
	}
}
```

##### MySQL Query that esAPI prepares dynamically
```sql
SELECT
	Message.message_id, Message.content, Message.user_id,
	User.name, unix_timestamp(Message.timestamp)
FROM Message
	LEFT JOIN (User) ON (Message.user_id=User.user_id)
WHERE Message.convo_id = ? AND Message.message_id > ?
```

##### Placeholder Values
```php
[placeholderValues] => Array (
	[0] => 3
	[1] => 150
)
```

---


#### *INSERT* routes create new rows in the database, and return the new row's ID

##### HTTP Request
> esAPI/?action=Message_Send&userId=44&convoId=3&content=Over%20Here!

##### Response
```json
{"ok":true,"result":163}
```

##### MySQL Query that esAPI prepares dynamically
```sql
INSERT INTO Message ( user_id, content, convo_id ) VALUES ( ?, ?, ? )
```

##### Placeholder Values
*The value of $_SESSION['userId'], and $_GET['convoId'] and $_GET['content'], are passed to MySQL AFTER preparing the query, to prevent SQL injection attacks.*
```php
[placeholderValues] => Array (
	[0] => 44
	[1] => Over Here!
	[2] => 3
)
```

##### How to define this route in routes.json
```json
"Message_Send": {
	"insert": {
		"Message.user_id"  : "+userId",
		"Message.content"  : "_content",
		"Message.convo_id" : "_convoId"
	}
}
```

---
 

#### *UPDATE* routes modify row(s) in the database, and return the number of rows modified

##### HTTP Request
> esAPI/?action=User_Location_Update&lattitude=29.901761299999997&longitude=-95.58800199999999

##### Response
```json
{"ok":true,"result":1}
```

##### MySQL Query that esAPI prepares dynamically
```sql
UPDATE  User SET  User.lat=?, User.lng=? WHERE  User.user_id = ?
```

##### Placeholder Values
*The value of $_GET['lattitude'] and $_GET['longitude'], as well as $_SESSION['userId'],are passed to MySQL AFTER preparing the query, to prevent SQL injection attacks.*
```php
[placeholderValues] => Array (
	[0] => 29.901764999999997
	[1] => -95.5879586
	[2] => 45
)
```

##### How to define this route in routes.json
```json
"User_Location_Update": {
	"update": "User",
	"set" : {
		"User.lat": "_lattitude",
		"User.lng": "_longitude"
	},
	"where": {
		"User.user_id": "+userId"
	}
}
```

---


#### *DELETE* routes delete row(s) from the database, and return the number of rows that were deleted

##### HTTP Request
> esAPI/?action=Message_Delete&messageId=123

##### Response
```json
{"ok":true,"result":1}
```

##### MySQL Query that esAPI prepares dynamically
```sql
DELETE FROM Message WHERE message_id = ?
```

##### Placeholder values
```php	
[placeholderValues] => Array (
	[0] => 123
)
```

##### How to define this route in routes.json
```json
"Message_Delete": {
	"delete": "Message",
	"where" : {
		"Message.message_id": "_messageId"
	}
}
```

---

### Request parameter substitution prefixes

'_' : Required field
'&' : Not required, assumed to be blank if undefined
'#' : Not required, excluded from query if undefined
'+' : Substitutes $_SESSION[ $valueKey ], or blank if undefined
'>' : Only for WHERE clauses -- Greater Than $_REQUEST[ $valueKey ]
'<' : Only for WHERE clauses -- Less than $_REQUEST[ $valueKey ]

*If the value is NOT prefixed by any of these characters, the value itself will be plugged into the query.*