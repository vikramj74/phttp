PHTTP
-------
A simple curl wrapper, which provides a clean object oriented interface to manage HTTP requests. All Phttp requests return a PhttpResponse object, which makes available the following response data :
- HTTP status code : 
  ``` 
  $statusCode = $resp->getStatusCode();
  ```
- Response body : 
  ``` 
  $responseBody = $resp->getBody();
  ```
- Response headers : 
  ``` 
  $responseHeaders = $resp->getHeaders();
  ```

# Basic Usage
```php
require_once('Phttp.php');

try {
  $resp = Phttp::get("http://google.com", null, null);
  echo "\nResponse : ";
  echo "\nStatus code : ".$resp->getStatusCode();
  echo "\nBody : ".$resp->getBody();
  $responseHeaders = $resp->getHeaders();
  if(count($responseHeaders) > 0) {
    echo "\nResponse headers : ";
    foreach($responseHeaders as $hk => $hv) {
            echo "\n$hk : $hv";
    }
  }
  echo "\n";
} catch(PhttpException $pe) {
  echo "\nException occurred : ";
  echo "\n Code : ".$pe->getCode();
  echo "\n Message : ".$pe->getMessage()."\n";
}
```
Available methods
-------------------
## GET 
```php
Phttp::get($url, $queryParameters, $headers);
```
### Parameters :
- URL (string) : The URL at which a GET request should be issued.
- Query Parameters (array) : An array of query parameters. 
- Request headers (array) : An array of headers to be added to the request.

### Example :
```php 
Ex : 
$r = Phttp::get(
    "api.project.com/vehicles", 
    array(
        "min_top_speed" => "250"
    ), 
    array(
        "Auth-Token" => "abcdef123#!"
    )
);
``` 

## POST
```php
Phttp::post($url, $requestBody, $headers);
```
### Parameters :
- URL (string) : The URL at which a POST request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.

### Example : 
```php 
Ex : 
$r = Phttp::post(
    "api.project.com/vehicles", 
    http_build_query(array(
        "name" => "Lamborghini Gallardo",
        "top_speed" => 300
    )),
    array(
        "Auth-Token" => "abcdef123#!",
        "Content-Type" => "application/x-www-form-urlencoded"
    )
);
```

## POST JSON
A helper method to post as JSON an array of values. The 'Content-Type: application/json' header automatically gets added to the request.
```php
Phttp::postJson($url, $jsonArr, $headers);
```
### Parameters :
- URL (string) : The URL for which a POST request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.

### Example :
```php 
Ex : 
$r = Phttp::postJson(
    "api.project.com/users/", 
    array( 
        "name" => "Jane Doe", 
        "age" => 21 
    ), 
    array(
        "Auth-Token" => "abcdef123#!"
    )
);
```

## PUT
```php
Phttp::put($url, $requestBody, $headers);
```
### Parameters :
- URL (string) : The URL for which a PUT request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.

### Example : 
```php 
Ex : 
$r = Phttp::put(
    "api.project.com/apparel", 
    http_build_query(array(
        "id" => 221,
        "type" => "Shirt",
        "size" => "30 M"
    )),
    array(
        "Auth-Token" => "abcdef123#!",
        "Content-Type" => "application/x-www-form-urlencoded"
    )
);
```

## DELETE
```php
Phttp::delete($url, $requestBody, $headers);
```
### Parameters :
- URL (string) : The URL at which a DELETE request should be issued.
- Request body (string) : The body of the request as a string. 
- Request headers (array) : An array of headers to be added to the request.

### Example : 
```php 
Ex : 
$r = Phttp::delete(
    "api.project.com/orders", 
    json_encode(array(
        "id" => "2231",
        "save_copy" => false
    )),
    array(
        "Auth-Token" => "abcdef123#!",
        "Content-Type" => "application/json"
    )
);
```

  
License
---------
The MIT license

Author
--------
Vikram Jaswal :   [@vikramj74](https://github.com/vikramj74 "@vikramj74")


