<?php
/**
* Request
* 
* Gets the page using the library curl.
* 
* @author Patenko patenkoss@gmail.com
*/
class Request {
	/**
     * @var $lastHTTPCode contains CURLINFO_HTTP_CODE.
	 * @var $lastErrorCode contains curl ErrorCode.
	 * @var $$handler is request header for curl.
	 * @var $userAgentsFile is file for CURLOPT_USERAGENT.
     */
    public $lastHTTPCode = null;
    public $lastErrorCode = null;
    private $handler;
    private $userAgentsFile = 'ua.txt';
    /**
     *
     * @param string $url
     * @return string $response 
     */
    public function make_request($url){
        $this->handler = $this->_create_handle();
        $this->lastErrorCode = null;
        $this->lastHTTPCode = null;
        curl_setopt($this->handler, CURLOPT_URL, $url);

        $response = curl_exec($this->handler);

        if (curl_errno($this->handler)){
            $this->lastErrorCode = curl_errno($this->handler);
			curl_close($this->handler);
            return false;
        } else {
            $this->lastHTTPCode = (int)curl_getinfo($this->handler, CURLINFO_HTTP_CODE);
            if ($this->lastHTTPCode != 200){
				curl_close($this->handler);
                return;
            }
        }
        curl_close($this->handler);
        return trim($response);
    }
    /**
     * Create cURL instance.
     */
    private function _create_handle(){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        /**
         * Get and set identifiable user agent from file. 
         */
        if (isset($this->userAgentsFile) &&
                file_exists($this->userAgentsFile)){
            $userAgents = file($this->userAgentsFile);
            shuffle($userAgents);
            $uagent = $userAgents[array_rand($userAgents)];
            curl_setopt($ch, CURLOPT_USERAGENT, trim($uagent));
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        return $ch;
    }
}
?>