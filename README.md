PHTTP
-------
A simple curl wrapper, which provides a clean object oriented interface to manage HTTP requests. All Phttp requests return a PhttpResponse object, which makes available the following response data :
- HTTP status code : 
  ```php 
  $statusCode = $resp->getStatusCode();
  ```
- Response body
  ```php 
  $responseBody = $resp->getBody();
  ```
- Response headers
  ```php 
  $responseHeaders = $resp->getHeaders();
  ```

# Basic Usage
```php
require_once('Phttp.php');

$resp = Phttp::get("http://www.google.com");
if($resp->getErrorMessage() === null) {
  echo "\nResponse : ";
  echo "\nStatus code : ".$resp->getStatusCode();
  echo "\nBody : ".$resp->getBody();
  $responseHeaders = $resp->getHeaders();
  if(count($responseHeaders) > 0) {
    echo "\nResponse headers : "
    foreach($headers as $hk => $hv) {
      echo "\n$k : $v";
    }
  }
} else {
  echo "\nError : ".$resp->getErrorMessage();
}
```
Available methods
-------------------
# GET 
```php
Phttp::get($url, $queryParameters, $headers);
```
### Parameters :
- URL (string) : The URL at which a GET request should be issued.
- Query Parameters (array) : An array of query parameters. 
- Request headers (array) : An array of headers to be added to the request.
 ```php 
  Ex : 
  $r = Phttp::get("www.google.com/search", array("q" => "Github"), null);
  ``` 
## POST
```php
Phttp::post($url, $requestBody, $headers);
```
### Parameters :
- URL (string) : The URL at which a POST request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.
 ```php 
  Ex : 
  $r = Phttp::post(
    "www.google.com/search", 
    http_build_query(array(
     "a" => 21,
     "b" => 31
    )),
    null
  );
  ```
## POST JSON
A helper method to post as JSON an array of values. The 'Content-Type: application/json' header automatically gets added to the request.
```php
Phttp::postJson($url, $jsonArr, $headers);
```
### Parameters :
- URL (string) : The URL at which a POST request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.
 ```php 
  Ex : 
  $r = Phttp::postJson(
    "www.google.com", 
    array( 
        "a" => 21, 
        "b" => 31 
    ), 
    null
  );
  ```
  
License
---------
The MIT license

Author
--------
Vikram Jaswal :   [@vikramj74](https://github.com/vikramj74 "@vikramj74")


