<?php
class PhttpResponse {
    private $body = null;
    private $headers = null ;
    private $statusCode = null;
    private $errorMessage= null;
    
    public static function getErrorResponse($errMsg) {
        return new PhttpResponse(null, null, null, $errMsg);
    }

    
    public function __construct($statusCode, $body, $headers, $errMsg = null) {
        $this->statusCode =  $statusCode; 
        $this->body = $body;
        $this->headers = $headers;
        if($errMsg !== null) {
            $this->errorMessage = $errMsg;
        }
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

    public function getErrorMessage() {
        return $this->errorMessage;
    }    
    

}



class Phttp {

    private static function parseResponseHeaders($headerString) {
        $headers = explode("\r\n", $headerString);
        $parsedHeaders = array();
        foreach($headers as $h) {
            if(strpos($h, "HTTP") === FALSE) { 
                list($name, $value) =  array_pad(explode(':', $h, 2), 2, null);
                $name = trim($name);
                $value = trim($value);
                $parsedHeaders[$name] = $value;
            }
        }
        /**
        foreach($parsedHeaders as $k => $v) {
            $parsedHeaders[$k] = explode('; ', $v);
        }
        */

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

    public static function attachRequestHeaders($client, $headers) {
        $processedHeaders = array();
        foreach($headers as $k => $v) {
            $processedHeaders[] = $k.": ".$v;
        }
        echo "\nCustom request headers : ".json_encode($processedHeaders);
        curl_setopt($client, CURLOPT_HTTPHEADER, $processedHeaders);
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
            return PhttpResponse::getErrorResponse("Invalid URL provided");
        }
        if(gettype($args) !== "array") {
            return PhttpResponse::getErrorResponse("Invalid arguments provided");
        }
        if(gettype($headers) !== "array") {
            return PhttpResponse::getErrorResponse("Invalid headers provided");
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


        echo "Phttp : GETing  URL : ".$url;

        $resp = Phttp::getResponse($client);
        curl_close($client);

        return $resp;
    } 

    public static function post($url, $requestBody = null, $headers = null) {

        if($headers === null) {
            $headers = array();
        } 
        
        // argument validation
        if(gettype($url) !== "string") {
            return PhttpResponse::getErrorResponse("Invalid URL provided");
        }
        if($requestBody !== null && gettype($requestBody) !== "string") {
            return PhttpResponse::getErrorResponse("Invalid request body provided");
        }
        if(gettype($headers) !== "array") {
            return PhttpResponse::getErrorResponse("Invalid headers provided");
        }
          
        $client = curl_init();
       
        curl_setopt($client, CURLOPT_URL, $url); 

        if($requestBody !== null) {
           curl_setopt($client, CURLOPT_POSTFIELDS, $requestBody); 
        }
        
        if(count($headers) > 0) {
            Phttp::attachRequestHeaders($client, $headers);
        }   
     
        echo "\n\nPOSTing : \n".$requestBody."\nTo : ".$url;       
        
        $resp = Phttp::getResponse($client);
        curl_close($client);
        
        return $resp;
          
    } 

    public static function postJson($url, $jsonArr = null, $headers = null) {
        if($jsonArr !== null && gettype($jsonArr) !== "array" ) {
            return PhttpResponse::getErrorResponse("Invalid JSON parameter provided");
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


}
?>
