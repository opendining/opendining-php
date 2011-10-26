<?php
    /*
		Open Dining Network PHP Helper Library
		
		Licensed under the MIT License
		Copyright (C) 2011 by Open Networks, LLC

		Permission is hereby granted, free of charge, to any person obtaining a copy
		of this software and associated documentation files (the "Software"), to deal
		in the Software without restriction, including without limitation the rights
		to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
		copies of the Software, and to permit persons to whom the Software is
		furnished to do so, subject to the following conditions:

		The above copyright notice and this permission notice shall be included in
		all copies or substantial portions of the Software.

		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
		IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
		FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
		AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
		LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
		OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
		THE SOFTWARE.
    */    
    
    // check for curl
    if(!extension_loaded("curl"))
	{
        throw(new Exception("The Open Dining Library requires CURL."));
    }
	
    class OpenDiningException extends Exception {}
    
    class OpenDiningClient 
	{
        protected $url;
        protected $key;
        
        public function __construct($key, $url = 'http://api.opendining.net') 
		{
            $this->url = $url;
            $this->key = $key;
        }
        
        /*
         *   Calls the ODN API
         *   $path : the API URL to call, after the trailing slash
         *   $method : the HTTP method to use (default = GET)
         *   $params : a key/value map of data to send 
         */
        public function request($path, $method = 'GET', $params = array(), $as_array = FALSE) {
            $query_string = '';
			
			if (!isset($params['key']))
			{
				$params['key'] = $this->key;
			}
			
            foreach ($params as $key => $value)
			{
                $query_string .= $key. '=' . urlencode($value) . '&';
			}
			
            $query_string = substr($query_string, 0, -1);
            
            // construct full url
            $request_url = $this->url . '/' . $path;
            
            // add GET query string params
            if($method == 'GET')
			{
                $request_url .= (strpos($path, '?') === FALSE ? '?' : '&') . $query_string;
			}
				
            $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			
            switch (strtoupper($method)) 
			{
                case 'GET':
                    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
                    break;
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                    break;
                default:
                    throw(new OpenDiningException($method . ' is not a supported HTTP method.'));
                    break;
            }
                        
            $result = curl_exec($ch);
			
            if ($result === FALSE)
                throw(new OpenDiningException('CURL error: ' . curl_error($ch)));
            
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            return new OpenDiningResult($result, $status, $as_array);
        }
    }
	
	class OpenDiningResult 
	{
        public $data;
        public $status;
        public $error;
        public $is_error;
        
        public function __construct($result, $status, $as_array = FALSE) 
		{
			$this->status = $status;
			
			if ($status >= 400)
			{
				$this->is_error = TRUE;
				$this->error = $result;
			}
			else
			{
				$this->data = json_decode($result, $as_array);
				
				// If JSON encoding failed, set a sensible empty default
				if ($this->data === NULL)
					$this->data = ($as_array ? array() : new StdClass);
			}            
        }
    }