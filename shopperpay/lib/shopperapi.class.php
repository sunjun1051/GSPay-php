<?php
/**
 * 海淘天下接口
 * GlobalShopper Interface
 */

require_once 'shopperpay_core.function.php';

if (!defined('GS_API')) {
	die('Config error: no GS_API');
}

class ShopperAPI
{
	/*
	 * 发送 HTTP 请求到 API 服务器
	 * Send HTTP request to API Server
	 *
	 * @param string $method request method
	 * @param string $url API URL
	 * @param array $data request data
	 * @return mixed result data received form server
	 */
	public function sendRequest($method, $url, $data = array()) //post， url/checklogin, 用户登录信息
	{
		logResult("Shopper Request", array('url' => $url, 'data' => $data));

		# 发送 HTTP 请求并取得返回数据
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('ContentType：application/x-www-form-urlencoded;charset=utf-8'));
		switch ($method) {
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
			case 'GET':
			default:
				if (empty($data)) {
					break;
				}
				$params = array();
				foreach ($data as $k => $v) {
					$params[] = $k . '=' . urlencode($v);
				}
				$url_params = implode('&', $params);
				if (false === strchr($url, '?')) {
					$url .= '?' . $url_params;
				} else {
					$url .= '&' . $url_params;
				}
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$res = curl_exec($ch);
		curl_close($ch);

		logResult("Shopper Response", array('url' => $url, 'data' => $res));
		return $res;
		
	}

	/**
	 * 调用海淘天下API接口(POST类型)
	 * Call GlobalShopper API (POST type)
	 *
	 * @param string $method the GlobalShopper API to call
	 * @param array $params parameters of the API
	 * @return bool|mixed result data
	 */
	public function call($method, $params) //$method CheckLogin | $params 用户登录信息
	{
		$shopper_api_params['parameters'] = json_encode($params);
		$json_str = $this->sendRequest('POST', GS_API . $method, $shopper_api_params);
// 		var_dump($shopper_api_params);
		if ($json_str) {
// 		    var_dump($json_str); echo '<br/>';
//             var_dump(json_decode($json_str, true)); die;
			return json_decode($json_str, true);
		} else {
			return false;
		}
	}

	/**
	 * 调用海淘天下API接口(GET类型)
	 * Call GlobalShopper API (GET type)
	 *
	 * @param string $method the GlobalShopper API to call
	 * @param array $params parameters of the API
	 * @return bool|mixed result data
	 */
	public function query($method, $params)
	{
		$json_str = $this->sendRequest('GET', GS_API . $method, $params);
		if ($json_str) {
			return json_decode($json_str, true);
		} else {
			return false;
		}

	}
	
	
	/**
	 * 数据签名
	 * @return return
	 */
	function sign($data) {
	    $fp=fopen(SIGN_PRIVKEY,"r"); 
	    $private_key=fread($fp,8192);
	    fclose($fp);
	    $res = openssl_pkey_get_private($private_key);
	    if (openssl_sign($data, $out, $res))
	        return (base64_encode($out));
	}
	
	/**
	 * 验证签名
	 * @return return
	 */
	function verify($sign, $data) {
	    $fp=fopen(SIGN_PUBKEY,"r"); 
	    $public_key=fread($fp,8192);   
	    fclose($fp);
	    $sig = base64_decode($sign);
	    $res = openssl_pkey_get_public($public_key);
	    if (openssl_verify($data, $sig, $res) === 1) {
	        return true;
	    }else{
	        return false;
	    }
	}
	
	
// 	public function encrypt($data) {
// 	    file_exists(GS_PUBKEY) or die('公钥不存在，请检查公钥路径');
// 	    $fp=fopen(GS_PUBKEY,"r");
// 	    $public_key=fread($fp,8192);
// 	    fclose($fp);
// 	    $pu_key = openssl_pkey_get_public($public_key);//这个函数可用来判断公钥是否是可用的
// 	    $encrypted = "";
// 	    openssl_public_encrypt($data,$encrypted,$pu_key);//公钥加密
// 	    $encrypted = base64_encode($encrypted);
// 	    return $encrypted;
// 	}
	
	
// 	public function decrypt($data) {
// 	    file_exists(GS_PRIVKEY) or die('私钥不存在，请检查公钥路径');
// 	    $fp=fopen(GS_PRIVKEY,"r");
// 	    $private_key=fread($fp,8192);
// 	    fclose($fp);
// 	    $pi_key =  openssl_pkey_get_private($private_key);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
// 	    $decrypted = "";
// 	    openssl_private_decrypt(base64_decode($data),$decrypted,$pi_key);//私钥解密
// 	    return $decrypted;
// 	}
}