<?php
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
}



class Phttp {

    private static function parseHeaders($headerString) {
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

    public static function get($url, $args, $headers) {
        if($args === null) {
            $args = array();
        }
        if($headers === null) {
            $headers = array();
        }
        if(
            gettype($url) !== "string" || 
            gettype($args) !== "array" ||
            gettype($headers) !== "array"
        ) {
            return null;
        }
       
        if(count($args) > 0) {
            $queryString = "";
            $argsRepr = array();
            foreach($args as $k => $v) {
                $repr = $k."=".$v;
                array_push($argsRepr, $repr);
            }
            $queryString = join('&', $argsRepr);
            $url .= "?".$queryString;    
        }





        $client = curl_init();
        curl_setopt($client, CURLOPT_URL, $url);
                
        if(count($headers) > 0) {
            $processedHeaders = array();
            foreach($headers as $k => $v) {
                $processedHeaders[] = $k.": ".$v;
            }
            echo "\nProcessed headers : ".json_encode($processedHeaders);
            curl_setopt($client, CURLOPT_HTTPHEADER, $processedHeaders);
        }


        //curl_setopt($client, CURLOPT_VERBOSE, 1);
        curl_setopt($client, CURLOPT_HEADER, 1); 
        curl_setopt($client, CURLOPT_RETURNTRANSFER, 1);
        echo "Phttp : GETing  URL : ".$url;


        $curlResponse = curl_exec($client);
        $headerSize = curl_getinfo($client, CURLINFO_HEADER_SIZE);
        $headerString = substr($curlResponse, 0, $headerSize);
        $responseBody = substr($curlResponse, $headerSize); 
        $responseCode = curl_getinfo($client, CURLINFO_HTTP_CODE);
        curl_close($client);

        $headers = Phttp::parseHeaders($headerString);
        $resp = new PhttpResponse($responseCode, $responseBody, $headers); 

        return $resp;
    } 

    public static function post($url, $postFields, $headers) {
         
    } 


}
?>
