
[TOC]

#Globalshopper payment SDK

GSPay Service SDK support payment, refund, single transaction results query, single order details query functions, following a  introduction of the rules of the integration.

#Environment Deployment
##Environment requirements
- PHP 5.5 and above
- Session mechanisms need to be on the same server
- Extension of mcrypt 、bcmath and curl are required；
- Payment plugin use the mcrypt_cbc function，this function are deprecated in the php version 5.5. So you need to shut down the error warning of Deprecated

##Configuration Items in config.php

- Switch to ChinaPay and Globalshopper test or production environment system.
```php
//Environment switch (true: production environment | false: test environment)
define('ENV_SWITCH', false);
```
- Globalshopper Merchant id 
	- Used as merchant idenfitier in Globalshopper system.
	- Provided by Globalshopper.
	- Different in test and production environment.
	- Provided by Gloalshopper through email after your company register in [Globalshopper Merchant Management Platform][GS_Mer_Platform].
```php
//Globalshopper Merchant Id, provided by GS, String 7.
$shopperpay_config['GSMerId'] = '5020001';
```
- ChinaPay Merchant id 
	- Used as merchant idenfitier in ChinaPay system.
	- Provided by ChinaPay.
	- Different in test and production environment.
	- Provided by ChinaPay through email in [Openning Letter][CP_OpenningLetter] after your company get through [ChinaPay Qualitification Verification][CP_Veri].
```php
//Merchant Id, provided by Chinapay, String 15.
$shopperpay_config['MerId'] = '808080071198021';
```

- Logistic Merchant Id
	- Used as logistic split merchant identifier in ChinaPay system.
	- Provided by ChinaPay.
	- Different in test and production environment.
	- Provided by ChinaPay through email in [Openning Letter][CP_OpenningLetter] after your company get through [ChinaPay Qualitification Verification][CP_Veri].

```php
//Logistic Merchant Id, provided by ChinaPay.
$shopperpay_config['LogisticsId'] = '808080071198022';
```

- ChinaPay public key
	- Used for data signature.
	- Provided by ChinaPay.
	- Different in test and production environment.
	- Provided by ChinaPay through email in [Openning Letter][CP_OpenningLetter] after your company get through [ChinaPay Qualitification Verification][CP_Veri].
```php
//public key of ChinaPay
define('CHINAPAY_PUBKEY', dirname(__FILE__) . '/key/company/PgPubk.key');
```

- Merchant private key
	- Used for veryfy data signature.
	- Provided by ChinaPay.
	- Different in test and production environment.
	- Provided by ChinaPay through email in [Openning Letter][CP_OpenningLetter] after your company get through [ChinaPay Qualitification Verification][CP_Veri].
```php
//private key of ChinaPay
define('CHINAPAY_PRIVKEY', dirname(__FILE__) . '/key/thenatural/MerPrK_808080071198021_20160711103730.key');
```

- Timezone Setting
	- Your system timezone setting
```php
//Timezone setting
date_default_timezone_set('Asia/Tokyo');
```

- Time zone
```php
//time zone, Eastren time zone means '+ ', western time zone means '- '.Less than 3 bytes.
$shopperpay_config['TimeZone'] = '+09';
```

- Country Code
```php
//Country Code, length 4, area code phone code.(US:0001,JP:0081,CN:0086)
$shopperpay_config['CountryId'] = '0086';
```
- Currency ID
```php
//Currency ID, ex:RMB = '156', Japanese yen = 'JPY', Dollar = 'USD'.
$shopperpay_config['CuryId'] = 'USD';
```
- Tag of summer time
```php
//tag of summer time. '1'means use summer time. '0'means do not use summer time.
$shopperpay_config['DSTFlag'] = '0';
```

- Order payment result call back address.
	- [@See Interface Specification 1.Order payment result （See follow process 1.4）][Interface_01]
	- Will be called at least 2 times normally.
	- The first time we will call this to update payment result to you and then skip to your SELLER_RETURN_URL (your customized order confirmation page address).
	- The second time we will call this just to update payment result to you, in case you did not receive the payment result at the first time , this situation will happen when the order submite page is closed before the payment result call back.
	- Maybe we will call this more than 2 times , this is becasue our server can not be sure that your web site server have received the payment result data successfully. We will try to resend 5 times at most.
```php
// Merchant order payment result call back address.
define('SELLER_API', 'http://localhost/shopperpay-sun/demo/seller_api.php');
```
- Order payment result page redirect adress.
	- You can customize the order confirm page if you like to show order infromation and payment result.
	- If you did not config this or we can not find the SELLER_RETURN_URL, we will redirect to [Globalshopper's order list page][GS_OrderList_URL]
```php
// Merchant order payment result call back interface API private key.
define('SELLER_RETURN_URL', 'http://localhost/shopperpay-sun/demo/return_url.php');
```

- Refund result notification call back address.
	- [@See 1.Refund result notification（See follow process 4.1）][Interface_01]
	- Will be called after the refund is accepted by ChinaPay.
	- Asynchronous notification.
```php
// Merchant refund result call back address.
define('SELLER_REFUND_API', 'http://localhost/order_refund');
```

#System interact flow chat

- Payment Flow chat

![Payment Flow chat][GS_img_url]

- Query and refund flow chat

![Payment Flow chat][GS_img_url_query]


#Interface Specification

## Order information submission
- Description
	- @See [flow chat 1.1]
	- After the order is submitted, the order information need to be saved to Session, the key is SHOPPER_PAY_ORDER
    - Attention: We have provided a payment result default page, if you want to redefine it, you should also save the redirect page url in Session with key of SELLER_RETURN_URL. (See flow chat 1.5)

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
| MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String | - |
|ProTotalAmt|Transaction Amount|NO|String|10|Ex：10.00|
|ProductInfo|Product Info|YES|Array|-|Array List，element as follows:|


- ProductInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|productName|Product Name|YES|String|15|-|
|productAttr|Product Attributes|NO|String|-|Include name and value, String of json formatted|
|imageUrl|Product image url|YES|String|-|-|
|perPrice|Product price|Yes|String|12|-|
|quantity|Product quantity|Yes|String|-|-|
|perWeight|Weight per product|YES|Decimal(18,4)|-|Including the decimal point and decimal digits (4 bits) a total of 18|
|perVolume|Volume per product|YES|Decimal(18,4)|-|Including the decimal point and decimal digits (4 bits) a total of 18|
|perTotalAmt|Subtotal per product|YES|String|12|-|
|SKU|Product SKU|YES|String|-|Must the same as sku you send to Globalshopper|

- Code Sample 
```jason
{
    "MerOrdId": "2016081700414026",
    "GSMerId": "5020001",
    "LogisticsId": "808080071198022",
    "ProTotalAmt": "240",
    "CuryId": "USD",
    "plugin_version": "v2.0.1",
    "ProductInfo": [
        {
            "productName": "Anti Aging Eye Cream",
            "productAttr": "",
            "imageUrl": "",
            "perPrice": "240.00",
            "quantity": "1",
            "perWeight": "0.5",
            "perVolume": "300",
            "perTotalAmt": "240.00",
            "SKU": "123456789000"
        }
    ]
}
```

##Order payment result notification
- Description
	- @See [flow chat 1.3.2]
	- This interface should be provided by your system.
	- After payment plugin returned the payment result, the payment plugin will call this interface to notify the payment result and logistic package info with http post mode.

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
| MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|OrderInfo|Order payment info|YES|String|Payment result|String of json formatted|
|PackageInfo|Logistic package info|YES|String|Logistic package info ,String of json formatted|-|
|consigneeInfo|Consignee information|YES|String|String of json formatted. It may be used  to display. If you don't need it, don't care it.|

- OrderInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|merid|Merchant num|YES|String|15|Distributed by ChinaPay ,String with 15 byte.|
|orderno|Order num in ChinaPay|YES|String|16|This orderno value in ChinaPay is the same with the orderNo in GlobalShopper system|
|transdate|Transaction Date|YES|String|8|formatted with YYYYMMDD|
|amount|Transaction Amount|YES|String|12|ex："000000001234" means 12.34 RMB or $12.34.|Attention：if the currency code is “JPY”，the “TransAmt” filed’s last two bit must be“00”。|
|currencycode|Country code|YES|String|4|State phone code|
|transtype|Transaction type|YES|String|4|"0001"：consumer transaction，"0002"：refund transaction。|
|status|Transaction status|YES|String|4|“1001” means that the transaction payment is successful. The others mean fail.|
|checkvalue|Signature value|YES|String|-|String of 256 byte and ASCII,Digital signature for key data submitted for this transaction|
|Priv1|Logistic costs|YES|String|-|-|

- PackageInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|freightSource|Freight amount|YES|String|-|-|
|postTaxSource|customs transit tax cost|YES|String|-|-|
|sourceExcise|Sourcethe original consumption tax|YES|String|-|Tax on items purchased by the country|
|sourceFreightSource|domestic freight in the country where the items are purchased|YES|String|-|-|

- consigneeInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|contactName|Contact name|YES|String|-|-|
|contactPhone|Contact mobile phone|YES|String|-|contactPhone and fixedPhone, at least one non empty|
|areaCode|Area code before fixed phone number |NO|String|-|-|
|fixedPhone|Fixed phone number|NO|String|-|If the fixedPhone has an extension number, use “-” to link. |
|email|Email|NO|String|-|-|
|zipCode|Zip code|NO|String|-|-|
|countryName|Country name|YES|String|-|-|
|provinceName|Province name|YES|String|-|-|
|cityName|City name|YES|String|-|-|
|districtName|District name|YES|String|-|-|
|detailAddress1|Detail address1|YES|String|-|-|
|detailAddress2|Detail address2|NO|String|-|If consignee address belongs to America ,it may be used, otherwise it is empty|


- Response json formatted data:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|isSuccess|isSuccess|YES|Bool|1：success，0：fail|
|errorMessage|Reson of failure |NO|String|Decription of failure，null when succuss|


##Single order payment status query
- Description
	- @See [flow chat 2.1]
	- Attention only return the order payment status and information with no packageInfo and consigneeInfo.
	- If you want to query the packageInfo and consigneeInfo you should call the [GSOrder information query][##GSOrder information query] interface.

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
| MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|order_date|Order date returned|YES|String|8|YYYYMMDD format,Same as transdate in interface [Order payment result]|
|resv|Merchant retention area|NO|String|empty string or others|

- Response json formatted data:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|OrderInfo|Order payment info|YES|String|Payment result|String of json formatted|

- OrderInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|merid|Merchant num|YES|String|15|Distributed by ChinaPay ,String with 15 byte.|
|orderno|Order num in ChinaPay|YES|String|16|This orderno value in ChinaPay is the same with the orderNo in GlobalShopper system|
|transdate|Transaction Date|YES|String|8|formatted with YYYYMMDD|
|amount|Transaction Amount|YES|String|12|ex："000000001234" means 12.34 RMB or $12.34.|Attention：if the currency code is “JPY”，the “TransAmt” filed’s last two bit must be“00”。|
|currencycode|Country code|YES|String|4|State phone code|
|transtype|Transaction type|YES|String|4|"0001"：consumer transaction，"0002"：refund transaction。|
|status|Transaction status|YES|String|4|“1001” means that the transaction payment is successful. The others mean fail.|
|checkvalue|Signature value|YES|String|-|String of 256 byte and ASCII,Digital signature for key data submitted for this transaction|
|Priv1|Logistic costs|YES|String|-|-|

##Refund application
- Description
	- @See [flow chat 3.1]
	- Attention this interface just submite a refund application, the result can just tell you wether the application is accepted not the refund.
	- The final refund result will be return through [Refund result notification][##Refund result notification] interface.


| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|order_date|Order date returned|YES|String|8|YYYYMMDD format,Same as transdate in interface [Order payment result]|
|refund_amount|Refund amount|-|String|-|For example 12.34, 95.00 |
|priv1|Merchant private area|-|String|-|The length is less than 40,length 20 is fittable. merchants themselves define the content, but can not be repeated, in order to avoid the refund of duplicate submission, thus verify the content is repeated to determine have already submitted, if submitted that the sum refund has been received and no treatment.|


- Response json formatted data:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|isSuccess|isSuccess|YES|Bool|1：success，0：fail|
|errorMessage|Reson of failure |NO|String|Decription of failure，null when succuss|



## Refund result notification
- Description
	- @See [flow chat 4.1.1]
	- Attention this notification will be send to you after ChinaPay return the final refund result.
	- The Plugin will call the SELLER_REFUND_API in config.php.


| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|OrderInfo|Order payment info|YES|String|Payment result|String of json formatted|

- Response json formatted data:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|isSuccess|isSuccess|YES|Bool|1：success，0：fail|
|errorMessage|Reson of failure |NO|String|Decription of failure，null when succuss|


## GSOrder information query
- Description
	- @See [flow chat 5.1]
	- Attention only return the packageInfo and consigneeInfo with no orderInfo.


| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|MerOrdId | Order Num in your system | Yes( if there is no GSOrdId) | String |-| - |
|GSOrdId|Globalshopper system order Id, related with MerOrdId in your system.|Yes( if there is no MerOrdId)|String|16|Order number in payment result(16 bit)|
|PackageInfo|Logistic package info|YES|String|Logistic package info ,String of json formatted|-|
|consigneeInfo|Consignee information|YES|String|String of json formatted. It may be used  to display. If you don't need it, don't care it.|
|GSOrderStatus|Order status in Globalshopper|YES|String|-|unpay,paid,cancel,shipping,completed|

- PackageInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|freightSource|Freight amount|YES|String|-|-|
|postTaxSource|customs transit tax cost|YES|String|-|-|
|sourceExcise|Sourcethe original consumption tax|YES|String|-|Tax on items purchased by the country|
|sourceFreightSource|domestic freight in the country where the items are purchased|YES|String|-|-|

- consigneeInfo elements as follows:

| Parameter | Introduction | Must Need| Field Type | Field Length | Memo|  
| ---- | ----------------- | ------------------- | ---- | ----------------- | -------------------|
|contactName|Contact name|YES|String|-|-|
|contactPhone|Contact mobile phone|YES|String|-|contactPhone and fixedPhone, at least one non empty|
|areaCode|Area code before fixed phone number |NO|String|-|-|
|fixedPhone|Fixed phone number|NO|String|-|If the fixedPhone has an extension number, use “-” to link. |
|email|Email|NO|String|-|-|
|zipCode|Zip code|NO|String|-|-|
|countryName|Country name|YES|String|-|-|
|provinceName|Province name|YES|String|-|-|
|cityName|City name|YES|String|-|-|
|districtName|District name|YES|String|-|-|
|detailAddress1|Detail address1|YES|String|-|-|
|detailAddress2|Detail address2|NO|String|-|If consignee address belongs to America ,it may be used, otherwise it is empty|


# Version

 - Version 2.0.1
    - Add an interface [7.0 Single order information query]
    - Adjust the interface fields.


        - Replace [OrdId] with [MerOrdId].
        - Add [GSOrdId] filed.
        - Delete [ApiKey] filed.
        - Delete [packageNo],[merId],[orderGuid],[orderNo],[transAmtSource]
        - Delete [IDNumber] 

 - Version 1.0.0

#Related Article
[Openning Letter][CP_OpenningLetter]
[ChinaPay Qualitification Verification][CP_Veri]

#About

This plugin and this sample file is proudly brought to you by the [Globalshopper team][GS]

 [GS]: http://globalshopper.com.cn
 [GS_Mer_Platform]: http://globalshopper.com.cn
 [CP_OpenningLetter]: CP_OpenningLetter.html
 [CP_Veri]:CP_Verify.html
 [Interface_01]:GS_Inter_01.html
 [GS_OrderList_URL]:http://www.globalshopper.com.cn/member/order/list.jhtml

 [GS_img_url]:./assets/gs_payment_flow.png
 [GS_img_url_query]:./assets/gs_payment_flow.png
 [GS_doc_guide]:./assets/1.2_GS%20Pay%20Plugin%20Integration%20Manual%20V2.0.1.doc



