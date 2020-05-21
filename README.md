# bitlyV4_php
bitly url shorten api V4, php version

官方文档: https://dev.bitly.com/v4_documentation.html#section/Migrating-from-V3

# 1. 群组id的获取

GET https://api-ssl.bitly.com/v4/groups HTTP/1.1

Host: api-ssl.bitly.com

Authorization: Bearer {YOUR_ACCESS_TOKEN}

Accept: application/json

返回数据:

````json
{
    "groups": [
        {
            "created": "2019-08-31T03:08:54+0000",
            "modified": "2019-08-31T03:08:54+0000",
            "bsds": [],
            "guid": "xxxxxxx", //缩短网址时需要传递这个guid
            "organization_guid": "xxxxxxx",
            "name": "abensky",
            "is_active": true,
            "role": "org-admin",
            "references": {
                "organization": "https://api-ssl.bitly.com/v4/organizations/xxxxxxxx"
            }
        }
    ]
}
````

# 2. 缩短网址

POST https://api-ssl.bitly.com/v4/shorten HTTP/1.1

Host: api-ssl.bitly.com

**Header设置:**

  Authorization: Bearer {YOUR_ACCESS_TOKEN}

  Content-Type: application/json

**body数据:**

  {"long_url": "https://www.takeyourtrip.com","group_guid": "前面获取到的群组groups.guid"}
  
返回结果示例 You will get a response that will contain values like:

````json
{
  "references": {
	"property1": "string",
	"property2": "string"
  },
  "archived": true,
  "tags": [],
  "created_at": "string",
  "title": "string",
  "deeplinks": [],
  "created_by": "string",
  "long_url": "string",
  "custom_bitlinks": [],
  "link": "string",
  "id": "string"
}
````

实际返回数据:

````json
{
    "created_at": "2019-08-31T03:13:46+0000",
    "id": "bit.ly/2ZL5Ood",
    "link": "http://bit.ly/2ZL5Ood",
    "custom_bitlinks": [],
    "long_url": "https://www.takeyourtrip.com/",
    "archived": false,
    "tags": [],
    "deeplinks": [],
    "references": {
        "group": "https://api-ssl.bitly.com/v4/groups/xxxxxx"
    }
}
````
错误返回信息示例:

{"message": "FORBIDDEN","resource": "bitlinks", "description": "You are currently forbidden to access this resource."}

{"message":"INVALID_ARG_LONG_URL","resource":"bitlinks","description":"The value provided is invalid.","errors":[{"field":"long_url","error_code":"invalid"}]}

> 经过测试, group_guid 参数可以不带! 或许是因为个人版只有一个群组.
