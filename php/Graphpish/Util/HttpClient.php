<?php
namespace Graphpish\Util;

/**
 * Call HTTP requests over network
 */
class HttpClient {
	static function fetch($url,$recursion=0) {
		$recursion++;
		if($recursion>10) throw new \Exception("Server is redericting way to often, gave up");
		
		$host=parse_url($url,PHP_URL_HOST);
		$path=parse_url($url,PHP_URL_PATH);
		$query=parse_url($url,PHP_URL_QUERY);
		if($query) {
			$path.='?'.$query;
		}
		
		$address = gethostbyname($host);
		
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
				throw new \Exception ("socket_create() failed: " . socket_strerror(socket_last_error()));
		}
		$result = socket_connect($socket, $address, 80);
		if ($result === false) {
				throw new \Exception ("socket_connect() failed: ($result) " . socket_strerror(socket_last_error($socket)));
		}
		$in = "GET $path HTTP/1.1\r\n";
		$in .= "Host: $host\r\n";
		$in .= "User-Agent: graphpishHttpClient/0.9 (+https://github.com/bxt/Graphpish)\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$out = '';
		socket_write($socket, $in, strlen($in));
		
		while ($outl = socket_read($socket, 2048)) {
			$out.=$outl;
		}
		$hb=explode("\r\n\r\n",$out,2);
		$headers=$hb[0];
		$body=isset($hb[1])?$hb[1]:'';
		
		// check if we have a 2xx satus code
		if(preg_match("/^HTTP\/1\.[01] 2/",$headers,$m)) {
			if(preg_match("/transfer-encoding: chunked\r\n/i",$headers,$m)) {
				$body=static::unchunkHttp11($body);
			}
			return $body;
		}
		
		// if not see if there are at least location suggestions
		if(preg_match("/Location: (.*)\r\n/",$headers,$m)) {
			return static::fetch($m[1],$recursion);
		}
		
		// we didn't get anything valuable form the server
		throw new \Exception ("Download failed: ".strstr($headers,"\r\n",true));
		
		// I rely on PHP to close the socket itself, as there is no finally in try/catch
		// socket_close($socket);
	}
	// http://www.php.net/manual/de/function.fsockopen.php#96146
	public static function unchunkHttp11($data) {
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
}
