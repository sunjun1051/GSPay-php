About The Accout Opening Letter
=========================== 

There are two different kinds of Account Opening Letter, seperately provided by ChinaPay and Globalshopper .


##Account Opening Letter from Globalshopper
After you have submited your application, you will receive an email with subject of 'Globalshopper Account Opening Letter'

There will be the following message in the mail
>Dear Customer:

>Welcome to Globalshopper www.globalshopper.com.cn  

>Thank you for choosing GlobalShopper payment plugin.

>We have already generated the unique  Merchant ID and Key files for your company in our production environment system, please read carefully through this document.

>GSMerId: ******

>Attachments are the key files：
>GS_Pubkey is the public key of Globalshopper.  
>GS_MerPrk.key is the Merchant private key for Globalshopper.   
>GS_MerPubk.key is the Merchant public key for Globalshopper.  

>We do strongly recommend that you should save this document at proper place for future use purpose.  
>If you have any technical issues during the period of development, please contact our merchant support. You can reach them by (tech@globalshopper.com.cn)  


![attachments logo][GS_account_attchment_url]

2 steps after you receive this 'Globalshopper Account Opening Letter’.  
1.Download the private key you need in GSPay plugin SDK.
2.Replace the configuration items in config.php of GSPay plugin SDK with these parameters which get from opening letter and 


##Account Opening Letter from ChinaPay

After you through the certification of ChinaPay.You will receive an email with subject of ‘ChinaPay Account Opening Letter’.

There will be the following message in the mail
>Thank you for choosing ChinaPay! We have already generated the unique Merchant ID for your company in our environment system, please read through this email carefully.  

>PgPubk.key is the public key in the rar compression package of this mail. Please download the private key after logging onto ChinaPay Merchant Console.  

>You could log onto ChinaPay Merchant Console for viewing transaction records. The URL of ChinaPay Merchant Console is: http://console.chinapay.com/newgms/.  

And there will be some attachments in the mail as follows.

![attachments logo][GS_attachments_screenshot_url]

4 steps after you receive this ‘ChinaPay Account Opening Letter’.  

1.You should unzip the mer.zip and get your Merchat ID , operator account and operator inital password.

2.Refer to the document [ChinaPay Merchant Console Guide][GS_CP_Console_Guide_url]

3.Download the private key you need in GSPay plugin SDK.

4.Replace the configuration items in config.php of GSPay plugin SDK with these parameters which get from opening letter and 


 [GS_attachments_screenshot_url]:https://globalshopper.github.io/GSPay-php/assets/gs_attachments_screenshot.png
 [GS_account_attchment_url]:https://globalshopper.github.io/GSPay-php/assets/gs_account_attachment.png
 [GS_CP_Console_Guide_url]:https://globalshopper.github.io/GSPay-php/assets/doc/ChinaPay_Merchant_Console_Guide.pdf
