<?php
/**
 * 插件入口地址
 * Plugin entry address
 */
session_start();
// 载入配置文件
// Load configuration file
require 'init.php';

// 载入银联提交处理类
// Load ChinaPay Submit process class
require 'lib/chinapay_submit.class.php';

// 载入插件处理类
// Load payment plugin process class
require 'lib/shopperpay.class.php';

$cps = new ChinaPaySubmit();
$sp = new ShopperPay();

// 处理session数据，并给相关字段加密
$shopper_pay_order = $_SESSION['SHOPPER_PAY_ORDER'];
$shopper_pay_order['PayInfoUrl'] = PAY_INFO_URL;
$sign_data = array(
              $shopper_pay_order['MerOrdId'],
              $shopper_pay_order['GSMerId'],
              $shopper_pay_order['LogisticsId'],
              $shopper_pay_order['ProTotalAmt'],
              $shopper_pay_order['CuryId'],
              $shopper_pay_order['PayInfoUrl'],
    );
$shopper_pay_order['GSChkValue'] = $sp->get_signed_data($sign_data);
$shopper_pay_order['ProductInfo'] = json_encode($shopper_pay_order['ProductInfo']);

$submitUrl = GS_API.'resources/plugin/pre_pay.jsp';
$cps->buildFormSubmit($shopper_pay_order, $submitUrl);




