<?php
namespace Graphpish\Util;

/**
 * Call HTTP requests over network
 */
class HttpClient {
	/**
 	 * Fetch web page, follow HEAd redirects
	 * @recursive
 	 */
	static function fetch($url,$recursion=0) {
		if($recursion>10) throw new HttpClientException("Server is redericting way to often, gave up");
		
		list($address,$host,$path)=self::getUrlParts($url);
		$in=self::buildHttpRequestHeaders($host,$path);
		$out=self::simpleRemoteCall($address,$in);
		
		// Parse answer:
		
		$hb=explode("\r\n\r\n",$out,2);
		$headers=$hb[0];
		$body=isset($hb[1])?$hb[1]:'';
		
		if(preg_match("/^HTTP\/1\.[01] 2/",$headers,$m)) { // 2xx satus code?
			if(preg_match("/transfer-encoding: chunked\r\n/i",$headers,$m)) {
				$body=self::unchunkHttp11($body);
			}
			return $body;
		}
		
		// if not see if there are at least location suggestions
		if(preg_match("/Location: (.*)\r\n/",$headers,$m)) {
			return static::fetch($m[1],++$recursion);
		}
		
		// we didn't get anything valuable form the server
		throw new HttpClientException ("Download failed: ".strstr($headers,"\r\n",true));
		
	}
	/**
 	 * Remove relicts from chunked transfer
	 * @see http://www.php.net/manual/de/function.fsockopen.php#96146
 	 */
	private static function unchunkHttp11($data) {
		$fp = 0;
		$outData = "";
		while ($fp < strlen($data)) {
			$rawnum = substr($data, $fp, strpos(substr($data, $fp), "\r\n") + 2);
			$num = hexdec(trim($rawnum));
			$fp += strlen($rawnum);
			$chunk = substr($data, $fp, $num);
			$outData .= $chunk;
			$fp += strlen($chunk);
		}
		return $outData;
	}
	/**
 	 * Extract relevant info from url
 	 */
	private static function getUrlParts($url) {
		$host=parse_url($url,PHP_URL_HOST);
		$path=parse_url($url,PHP_URL_PATH);
		$query=parse_url($url,PHP_URL_QUERY);
		if($query) {
			$path.='?'.$query;
		}
		$ip = gethostbyname($host);
		return array($ip,$host,$path);
	}
	/**
 	 * Send request to ip and fetch all the response
 	 */
	private static function simpleRemoteCall($ip,$in) {
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
				throw new HttpClientException ("socket_create() failed: " . socket_strerror(socket_last_error()));
		}
		$result = socket_connect($socket, $ip, 80);
		if ($result === false) {
				throw new HttpClientException ("socket_connect() failed: ($result) " . socket_strerror(socket_last_error($socket)));
		}
		socket_write($socket, $in, strlen($in));
		$out = '';
		while ($outl = socket_read($socket, 2048)) {
			$out.=$outl;
		}
		// I rely on PHP's gc to close the socket itself, as there is no finally in try/catch
		// socket_close($socket);
		return $out;
	}
	/**
 	 * Compose a basic GET request
 	 */
	private static function buildHttpRequestHeaders($host,$path) {
		$headers = "GET $path HTTP/1.1\r\n";
		$headers .= "Host: $host\r\n";
		$headers .= "User-Agent: graphpishHttpClient/0.9 (+https://github.com/bxt/Graphpish)\r\n";
		$headers .= "Connection: Close\r\n\r\n";
		return $headers;
	}
}
