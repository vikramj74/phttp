<?php
class PhttpException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class PhttpResponse {
    private $body = null;
    private $headers = null ;
    private $statusCode = null;
    
    
    public function __construct($statusCode, $body, $headers) {
        $this->statusCode =  $statusCode; 
        $this->body = $body;
        $this->headers = $headers;
    }
    
    public function getHeaders() {
        return $this->headers;
    }    

    
    public function getBody() {
        return $this->body;
    }    

    public function getStatusCode() {
        return $this->statusCode;
    }    

    public function __toString() {
        $str  = "\nPhttpResponse : \n\n Status code : ".$this->statusCode;
        $str .= "\n Body : ".$this->body;
        $str .= "\n Headers : ";
        foreach($this->headers as $k => $v) {
            $str .= "\n  $k => $v";
        }
        return $str."\n";
    }


    public function toJson() {
        return json_encode(array(
            "statusCode" => $this->statusCode,
            "body" => $this->body,
            "headers" => $this->headers
        ));
    }    

}



class Phttp {

    
    const GET_REQUEST = "GET",
          POST_REQUEST = "POST",
          PUT_REQUEST = "PUT",
          DELETE_REQUEST = "DELETE",
          OPTIONS_REQUEST = "OPTIONS",
          HEAD_REQUEST = "HEAD";
          


    private static function parseResponseHeaders($headerString) {
        $headers = explode("\r\n", $headerString);
        $parsedHeaders = array();
        foreach($headers as $h) {
            if(strpos($h, "HTTP") === FALSE) { 
                list($name, $value) =  array_pad(explode(':', $h, 2), 2, null);
                $name = trim($name);
                $value = trim($value);
                if(empty($name) && empty($value)) {
                    continue;
                }
                $parsedHeaders[$name] = $value;
            }
        }
         
        return $parsedHeaders;
    }


    private static function getResponse($client) {
        curl_setopt($client, CURLOPT_HEADER, 1); 
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        $curlResponse = curl_exec($client);
        $headerSize = curl_getinfo($client, CURLINFO_HEADER_SIZE);
        $headerString = substr($curlResponse, 0, $headerSize);
        $responseBody = substr($curlResponse, $headerSize); 
        $responseCode = curl_getinfo($client, CURLINFO_HTTP_CODE);

        $headers = Phttp::parseResponseHeaders($headerString);
        $resp = new PhttpResponse($responseCode, $responseBody, $headers); 
        return $resp; 
    }

    private static function attachRequestHeaders($client, $headers) {
        $processedHeaders = array();
        foreach($headers as $k => $v) {
            $processedHeaders[] = $k.": ".$v;
        }
        curl_setopt($client, CURLOPT_HTTPHEADER, $processedHeaders);
    }

    private static function makeRequest($requestMethod, $url, $requestBody = null, $headers = null) {

        if($headers === null) {
            $headers = array();
        } 
        
        // argument validation
        if(gettype($url) !== "string") {
            throw new PhttpException("Invalid URL provided", 1);
        }
        if($requestBody !== null && gettype($requestBody) !== "string") {
            throw new PhttpException("Invalid request body provided", 2);
        }
        if(gettype($headers) !== "array") {
            throw new PhttpException("Invalid headers provided", 3);
        }
          
        $client = curl_init();
       
        curl_setopt($client, CURLOPT_URL, $url); 

        if($requestMethod !== Phttp::POST_REQUEST) {
            curl_setopt($client, CURLOPT_CUSTOMREQUEST, $requestMethod);
        } 

        if($requestBody !== null) {
           curl_setopt($client, CURLOPT_POSTFIELDS, $requestBody); 
        }
        
        if(count($headers) > 0) {
            Phttp::attachRequestHeaders($client, $headers);
        }   
     
        
        $resp = Phttp::getResponse($client);
        curl_close($client);
        
        return $resp;
          
    }

    public static function get($url, $args, $headers) {
        // defaults
        if($args === null) {
            $args = array();
        }
        if($headers === null) {
            $headers = array();
        }

        // argument validation
        if(gettype($url) !== "string") {
            throw new PhttpException("Invalid URL provided", 1);
        }
        if(gettype($args) !== "array") {
            throw new PhttpException("Invalid query parameters provided", 4);
        }
        if(gettype($headers) !== "array") {
            throw new PhttpException("Invalid headers provided", 3);
        }
       
        if(count($args) > 0) {
            $queryString = http_build_query($args);
            $url .= "?".$queryString;    
        }

        $client = curl_init();
        curl_setopt($client, CURLOPT_URL, $url);
                
        if(count($headers) > 0) {
            Phttp::attachRequestHeaders($client, $headers);
        }



        $resp = Phttp::getResponse($client);
        curl_close($client);

        return $resp;
    } 


    public static function post($url, $requestBody = null, $headers = null) {
        return Phttp::makeRequest(Phttp::POST_REQUEST, $url, $requestBody, $headers);
    }

    public static function postJson($url, $jsonArr = null, $headers = null) {
        if($jsonArr !== null && gettype($jsonArr) !== "array" ) {
            throw new PhttpException('Invalid JSON body provided', 5);
        }

        $jsonPayLoad = null;
        if($jsonArr !== null) {
            $jsonPayLoad = json_encode($jsonArr);   
        }
        
        if($headers === null) {
            $headers = array();
        }
         
        $headers["Content-Type"] = "application/json";

        return Phttp::post($url, $jsonPayLoad, $headers);
    } 


    public static function put($url, $requestBody = null, $headers = null) { 
        return Phttp::makeRequest(Phttp::PUT_REQUEST, $url, $requestBody, $headers);
    }

    public static function delete($url, $requestBody = null, $headers = null) {
        return Phttp::makeRequest(Phttp::DELETE_REQUEST, $url, $requestBody, $headers);
    }
}
?>
