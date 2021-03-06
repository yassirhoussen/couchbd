<?php

class Client {
	
	/**
	* @params string database source name
	*/
	private $dns = null;

	/**
	* @params rray database source name parsed
	*/
	private $dnsParsed = null;

	/**
	* @params array couch options
	*/
	private $options = null;

	/**
	* @params array allowed HTTP methods for REST dialog
	*/
	private $HTTP_METHODS = array('GET','POST','PUT','DELETE','COPY');

	/**
	* @params boolean tell if curl PHP extension has been detected
	*/
	private $curl = null;
	
	public function __construct($dns, $options = array()) {
		$this->dns 	 = $dns;
		$this->options   = $options;
		$this->dnsParsed = parse_url($this->dns);
		if ( !isset($this->dnsParsed['port']) ) {
			$this->dnsParsed['port'] = 80;
		}
		$this->curl = function_exists('curl_init');
	}
	
	/**
	* parse a CouchDB server response and sends back an array
	* the array contains keys :
	* status_code : the HTTP status code returned by the server
	* status_message : the HTTP message related to the status code
	* body : the response body (if any). If CouchDB server response Content-Type is application/json
	*        the body will by json_decoded() as an associative array
	*
	* @param string $rawData data received from the server
	* @return associative array response
	* @throws Exception
	*/
	public function parseResponse(&$rawData) {
		if ( !strlen($rawData) ) 
			throw new Exception("no data to parse");
		while ( !substr_compare($rawData, "HTTP/1.1 100 Continue\r\n\r\n", 0, 25) ) {
			$rawData = substr($rawData, 25);
		}
		$response = array('body' => null);
		list($headers, $body) = explode( "\r\n\r\n", $rawData, 2);
		$headersArray = explode("\n", $headers);
		$statusLine   = reset($headersArray);
		$statusArray  = explode(' ', $statusLine, 3);
		$response['status_code']    = trim( $statusArray[1] );
		$response['status_message'] = trim( $statusArray[2] );
		if ( strlen($body) ) {
			$response['body'] = preg_match('@Content-Type:\s+application/json@i',$headers) ? json_decode($body, true) : $body ;
		}
		return $response;
	}
	
	/**
	*send a query to the CouchDB server
	*
	* @param string $method HTTP method to use (GET, POST, ...)
	* @param string $url URL to fetch
	* @param array $parameters additionnal parameters to send with the request
	* @param string|array|object $data request body
	*
	* @return string on server response on success, false on error
	*/
	public function query ( $method, $url, $parameters = array() , $data = NULL) {
		if ( $this->curl )
			return $this->_curlQuery(strtoupper($method), $url, $parameters, $data);
	}
	
	/**
	* record a file located on the disk as a CouchDB attachment
	* @param string $url CouchDB URL to store the file
	* @param string $file path to on disk
	*
	* @return string server response
	*/
	public function storeFile ($url, &$file) {
		if ( $this->curl )
			return $this->_curlStoreFile($url, $file);
	}
	
	/**
	* store some data as a CouchDB attachment
	*
	* @param string $url CouchDB URL to store the file to
	* @param string $data data to send as the couch attachment
	*
	* @return string server response
	*/
	public function storeAsData ($url, &$data) {
		if ( $this->curl)
			return $this->_curlStoreAsData($url, $data);
	}

	/**
	*send a query to the CouchDB server using PHP cURL API
	*
	* @param string $method HTTP method to use (GET, POST, ...)
	* @param string $url URL to fetch
	* @param array $parameters additionnal parameters to send with the request
	* @param array $data request body
	*
	* @return string on server response is success, false if is error
	*
	* @throws Exception
	*/
	private function _curlQuery ( $method, $url, $parameters = array(), $data = NULL) {
		if ( !in_array($method, $this->HTTP_METHODS )    )
			throw new Exception("Bad HTTP method: $method");
		$url = $this->dns.$url;
		if ( is_array($parameters) && count($parameters) )
			$url = $url.'?'.http_build_query($parameters);
		$http = $this->_curlBuildRequest($method, $url, $data);
		$this->_curlAddCustomOptions ($http);
		curl_setopt($http,CURLOPT_HEADER, true);
		curl_setopt($http,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($http,CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($http);
		curl_close($http);
		return $response;
	}


	/**
	* add user-defined options to Curl resource
	*/
	private function _curlAddCustomOptions ($res) {
		if ( array_key_exists("curl", $this->options) && is_array($this->options["curl"]) ) {
			curl_setopt_array($res, $this->options["curl"]);
		}
	}
	/**
	* build HTTP request to send to the server using PHP cURL API
	*
	* @param string $method HTTP method to use
	* @param string $url the request URL
	* @param array $data the request body. If it's an array or an object, $data is json_encoded()
	* @return resource CURL request resource
	*/
	private function _curlBuildRequest($method, $url, &$data) {
		$http = curl_init($url);
		$http_headers = array('Accept: application/json,text/html,text/plain,*/*') ;
		if ( is_array($data) ) {
			$data = json_encode($data);
		}
		$http_headers[] = 'Content-Type: application/json';
		curl_setopt($http, CURLOPT_CUSTOMREQUEST, $method);
		if ( $method == 'COPY') {
			$http_headers[] = "Destination: $data";
		} elseif ($data) {
			curl_setopt($http, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($http, CURLOPT_HTTPHEADER,$http_headers);
		return $http;
	}
	
	/**
	* record a file located on the disk as a CouchDB attachment
	* uses PHP cURL API
	*
	* @param string $url CouchDB URL to store the file to
	* @param string $file path to the on-disk file
	*
	* @return string server response
	*
	* @throws InvalidArgumentException
	*/
	public function _curlStoreFile ( $url, &$file) {
		if ( !strlen($url) )
			throw new Exception("Attachment URL can't be empty");
		if ( !strlen($file) || !is_file($file) || !is_readable($file) )	
			throw new Exception("Attachment file does not exist or is not readable");
		$content_type =  'application/octet-stream';
		$url = $this->dns.$url;
		$http = curl_init($url);
		$http_headers = array(
			'Accept: application/json,text/html,text/plain,*/*',
			'Content-Type: '.$content_type,
			'Expect: '
		);
		curl_setopt($http, CURLOPT_PUT, 1);
		curl_setopt($http, CURLOPT_HTTPHEADER,$http_headers);
		curl_setopt($http, CURLOPT_UPLOAD, true);
		curl_setopt($http, CURLOPT_HEADER, true);
		curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($http, CURLOPT_FOLLOWLOCATION, true);
		$fstream = fopen($file,'r');
		curl_setopt($http, CURLOPT_INFILE, $fstream);
		curl_setopt($http, CURLOPT_INFILESIZE, filesize($file));
		$this->_curlAddCustomOptions ($http);
		$response = curl_exec($http);
		fclose($fstream);
		curl_close($http);
		return $response;
	}
	/**
	* store some data as a CouchDB attachment
	* uses PHP cURL API
	*
	* @param string $url CouchDB URL to store the file to
	* @param string $data data to send as the attachment content
	*
	* @return string server response
	*
	* @throws InvalidArgumentException
	*/
	public function _curlStoreAsData ($url, &$data) {
		if ( !strlen($url) )
			throw new Exception("Attachment URL can't be empty");
		$content_type =  'application/octet-stream';
		$url  = $this->dns.$url;
		$http = curl_init($url);
		$http_headers = array(
			'Accept: application/json,text/html,text/plain,*/*',
			'Content-Type: '.$content_type,
			'Expect: ',
			'Content-Length: '.strlen($data)
		) ;
		curl_setopt($http, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($http, CURLOPT_HTTPHEADER,$http_headers);
		curl_setopt($http, CURLOPT_HEADER, true);
		curl_setopt($http, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($http, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($http, CURLOPT_POSTFIELDS, $data);
		$this->_curlAddCustomOptions ($http);
		$response = curl_exec($http);
		curl_close($http);
		return $response;
	}

	//getters and setters
	
	public function getDns(){
		return $this->dns;
	}

	public function setDns($dns){
		$this->dns = $dns;
	}

	public function getDnsParsed(){
		return $this->dnsParsed;
	}

	public function setDnsParsed($dnsParsed){
		$this->dnsParsed = $dnsParsed;
	}

	public function getOptions(){
		return $this->options;
	}

	public function setOptions($options){
		$this->options = $options;
	}

	public function getCurl(){
		return $this->curl;
	}

	public function setCurl($curl){
		$this->curl = $curl;
	}
}

?>
