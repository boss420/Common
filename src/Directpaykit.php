<?php
/**
 * 直连接口集成相关的封装类
 * @author JanHao
 * @email 383542899@qq.com
 * @web www.ipasspay.com
 */
namespace boss420\udopay;
class Directpaykit {
	private $mid; //商户ID
	private $site_id; //网站ID
	private $api_key; //网站密钥
	private $rebill_arr = array(); //周期交易提交的数组
	private $domain; //网站域名
	/**
	 * 构造函数，输入三个关键参数
	 */
	public function __construct($mid, $site_id, $api_key, $domain) {
		$this->mid = $mid;
		$this->site_id = $site_id;
		$this->api_key = $api_key;
		$this->domain = $domain;

	}

	/**
	 * 设置交易为周期交易
	 *@param array $post_arr 提交的周期数组
	 */
	public function setRebill($post_arr) {
		$post_arr['rebill_flag'] = 1;
		$this->rebill_arr = $post_arr;
	}

	/**
	 * 提交到网关地址
	 *@param array $post_arr  提交参数的数组
	 *@return json 返回支付的结果JSON数据
	 */
	public function sendGateway($post_arr = array()) {

		//echo $result = $this->mid . $this->site_id . $post_arr['order_amount'] . $post_arr["order_currency"] . $this->api_key;exit;
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $post_arr["oid"] .
			$post_arr['order_amount'] . $post_arr["order_currency"] . $this->api_key);
		$curlPost["source_ip"] = $this->get_real_ip() . ""; //IP地址
		$curlPost["source_url"] = $_SERVER['HTTP_REFERER'] ?: $_SERVER['HTTP_HOST']; //来源地址
		$curlPost["gateway_version"] = "1.0"; //网关版本号
		$curlPost["mid"] = $this->mid; //商户ID
		$curlPost["site_id"] = $this->site_id; //网站ID
		$process_gateway = $this->domain . "/index.php/Gateway/securepay";
		//合并
		$new_array = array_merge($curlPost, $post_arr, $this->rebill_arr);
		//dump($new_array);exit;
		$response = $this->curlSend($process_gateway, $new_array);
		return $response;
	}

	/**
	 * 提交数据到rebill网关
	 * @param $pid int 平台订单号
	 * @param $oid string 商户订单号
	 * @param $order_amount decimal(12,2) 订单金额
	 * @param $order_currency char(3) 订单货币
	 * @return json 返回周期交易的JSON数据
	 */
	public function sendRebill($pid, $oid, $order_amount, $order_currency) {
		//echo $pid . $oid . $order_amount . $order_currency . $this->api_key;
		$curlPost["hash_info"] = hash("sha256", $pid . $oid . $order_amount . $order_currency . $this->api_key);
		$curlPost["pid"] = $pid;
		$curlPost["oid"] = $oid;
		$curlPost["order_amount"] = $order_amount;
		$curlPost["order_currency"] = $order_currency;
		$process_gateway = $this->domain . "/index.php/Gateway/rebilling";

		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 提交退款申请
	 *@param $oid int 订单号，来自平台的
	 *@param $refund_amount decimal(12,2) 退款金额，且必须大于0
	 *@return json 返回退款的JSON数据
	 */
	public function sendRefund($oid, $refund_amount) {
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $oid . $refund_amount . $this->api_key);
		$curlPost["mid"] = $this->mid;
		$curlPost["site_id"] = $this->site_id;
		$curlPost["oid"] = $oid;
		$curlPost["refund_amount"] = $refund_amount;
		$process_gateway = $this->domain . "/index.php/Openapi/Orders/refund";

		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 取消退款申请
	 *@param $oid int 订单号，来自平台
	 *@return json 返回退款的JSON数据
	 */
	public function cancelRefund($oid) {
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $oid . $this->api_key);
		$curlPost["mid"] = $this->mid;
		$curlPost["site_id"] = $this->site_id;
		$curlPost["oid"] = $oid;

		$process_gateway = $this->domain . "/index.php/Openapi/Orders/cancelRefund";
		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 获得部分订单信息
	 *@param $oid int 订单号，来自平台
	 *@param $mh_oid 商户订单号
	 *@return json 返回退款的JSON数据
	 */
	public function getOrderInfo($oid, $mh_oid = "") {
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $oid . $mh_oid . $this->api_key);
		$curlPost["mid"] = $this->mid;
		$curlPost["site_id"] = $this->site_id;
		$curlPost["oid"] = $oid;
		$curlPost["mh_oid"] = $mh_oid;
		$process_gateway = $this->domain . "/index.php/Openapi/Orders/getInfo";
		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 取消一笔预授权
	 *@param $oid int 订单号，来自平台
	 *@return json 返回退款的JSON数据
	 */
	public function void($oid) {
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $oid . $this->api_key);
		$curlPost["mid"] = $this->mid;
		$curlPost["site_id"] = $this->site_id;
		$curlPost["oid"] = $oid;

		$process_gateway = $this->domain . "/index.php/Openapi/Orders/void";
		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 上传一笔交易的运单号
	 *@param $oid int 订单号，来自平台
	 *@param $tracking_company string 物流公司
	 *@param $tracking_number string 运单号
	 *@return json 返回退款的JSON数据
	 */
	// $mid.$site_id.$oid.$tracking_company.$tracking_number.$key
	public function uploadTracking($oid, $tracking_company, $tracking_number) {
		$curlPost["hash_info"] = hash("sha256", $this->mid . $this->site_id . $oid . $tracking_company . $tracking_number . $this->api_key);
		$curlPost["mid"] = $this->mid;
		$curlPost["site_id"] = $this->site_id;
		$curlPost["oid"] = $oid;
		$curlPost["tracking_company"] = $tracking_company;
		$curlPost["tracking_number"] = $tracking_number;
		//dump($curlPost);exit;
		$process_gateway = $this->domain . "/index.php/Openapi/Tracking/upload";
		$response = $this->curlSend($process_gateway, $curlPost, 0);
		return $response;
	}

	/**
	 * 获取IP地址
	 *@return string 返回获取的IP地址
	 */
	private function get_real_ip() {
		$ip = false;
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = FALSE;
			}
			for ($i = 0; $i < count($ips); $i++) {
				if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		}
		return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}

	/**
	 * 私有方法，CURL模拟提交
	 * @param $process_gateway string 提交的网关地址
	 * @param $data_arr 提交的数据数组
	 * @param $type  模式提交的方式 1=》POST 0=》GET
	 */
	private function curlSend($process_gateway, $data_arr, $type = 1) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $process_gateway);
		curl_setopt($ch, CURLOPT_POST, $type);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_arr));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

}