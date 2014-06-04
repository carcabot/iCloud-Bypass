<?php
/* iCloud ByPass
 * @Author: CarcaBot
 */
switch($_GET['action']) {

case 'showSettings':
header("Content-type: application/xml");

echo <<<XML
<plist version="1.0">
  <dict>
    <key>iphone-activation</key>
    <dict>
      <key>ack-received</key>
      <true/>
      <key>show-settings</key>
      <true/>
    </dict>
  </dict>
</plist>
XML;
exit;
break;

case 'deviceActivation':
header("Content-type: text/html");

if(isset($_POST['activation-info-base64'])) {
header("Cteonnt-Length: 8059");
header("Content-Length: 8059");
$ainfo = base64_decode($_POST['activation-info-base64']);
$xml = new SimpleXMLElement($ainfo);
$data = base64_decode((string)$xml->data);
$xml = new SimpleXMLElement($data);
$ActivationRandomness = trim((string)$xml->dict->string[0]);
$UniqueDeviceID = trim((string)$xml->dict->string[17]);
$InternationalMobileEquipmentIdentity = trim((string)$xml->dict->string[8]);
$DeviceCertRequest = str_replace("\n", "", trim((string)$xml->dict->data[1]));
$str = base64_encode(sprintf("{
	\"InternationalMobileEquipmentIdentity\" = \"%s\";
	\"ActivityURL\" = \"https://albert.gcsis-apple.com.akadns.net/deviceservices/activity\";
	\"ActivationRandomness\" = \"%s\";
	\"UniqueDeviceID\" = \"%s\";
	\"CertificateURL\" = \"https://albert.gcsis-apple.com.akadns.net/deviceservices/certifyMe\";
	\"PhoneNumberNotificationURL\" = \"https://albert.gcsis-apple.com.akadns.net/deviceservices/phoneHome\";
	\"WildcardTicket\" = \"MIIBjQIBATALBgkqhkiG9w0BAQUxWJ8/BCLqPjafQAThAFoAn0sUgrI5poKFjZt2QQ6qXRyXk292GJqfh20HATA0ADlzWZ+XPQwAAAAA7u7u7u7u7u+flz4EAAAAAJ+XPwQBAAAAn5dABAAAAAAEgYCP3n1dhKvHn8WlR9xZx8K2XkIXOXi5Z6RCeL7p6DL899vU6QYfPwoyM7hCKvsn0DQ/kOl7M/55f84WjEcFipxmdJ81mzkTJzK5PD+YwgS9Th0921zxetEPmQuRYuZpQEzMF/MjkLP3Qqe3Mllh3l/aAShsQD1B7Vzn7Y+kFq6DnKOBnTALBgkqhkiG9w0BAQEDgY0AMIGJAoGBAO06P2TR6CCcwVIGNO9+L7IJfgAsQ0Pn+Krr5kqzwLYAxDUmcGlax16RfnESgQAapA2tNGHBDcdnbUtbbhyygzZ9czGS2mUaYivVHojDzWS+ppYNdgVi8FY5RThhA/FOqX18DsOg/xQg0YL8hgZ+8LMPLBGuwHZWJjBxppL8IDj9AgMBAAE=\n\";
}",$InternationalMobileEquipmentIdentity, $ActivationRandomness, $UniqueDeviceID));

$pkeyid = openssl_pkey_get_private(file_get_contents("certs/private_key.pem"));
// compute signature
openssl_sign(base64_encode($str), $signature, $pkeyid);
openssl_free_key($pkeyid);
echo '
<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="keywords" content="iTunes Store" /><meta name="description" content="iTunes Store" /><title>iPhone Activation</title><link href="http://static.ips.apple.com/ipa_itunes/stylesheets/shared/common-min.css" charset="utf-8" rel="stylesheet" /><link href="http://static.ips.apple.com/deviceservices/stylesheets/styles.css" charset="utf-8" rel="stylesheet" /><link href="http://static.ips.apple.com/ipa_itunes/stylesheets/pages/IPAJingleEndPointErrorPage-min.css" charset="utf-8" rel="stylesheet" /><script id="protocol" type="text/x-apple-plist"><plist version="1.0">
  <dict>
    <key>iphone-activation</key>
    <dict>
      <key>activation-record</key>
      <dict>
        <key>FairPlayKeyData</key>
        <data>LS0tLS1CRUdJTiBDT05UQUlORVItLS0tLQpBQUVBQVp6c3FTV1pUMW16ZUlFQXYrUW1xZE1XamJjWHd2N1VNZzczVU16cFNPSkVsUHRHbkltMlJyeEtrVjBnCkVTZ2lRWXRUQzJpTkVFSlNiWDM3OVhnclpWRmlDN0QxMnpmNDJVSytmYStSWk9jaG5OZWo3ekZOaGdHc1lqbnAKQXk4RjVtelJLZXNzMVVzMnFJb0NIU08rOFNMZFBnSWYvRWFINEpLT2lSRm9pdXhIQkNYVk9qeEZ0VUQzTjFmKwpESzUxdTFjOG1yRnQxQi8xQjl5K1BxSlN2emRHRmhnV0RIejdaZ3RpQVZRUWZXYjJlRHNaQ1pGTmlrR0lyMVZCCmhPRjFXSXNJMWFGWmI0QlZaM0owaEdnZXVzbHhyVXp6d2dkdlNoU1NvMVU3eUJ5TnJWNjhVNGVLZHFyZnMrbXIKNG0wbi9QSGZBdGxPajlQdWxNMHN4MnJHYWYvQ3ZzOWxKaVJSMk1XMHRFcTgvL1kyaEViRzF6TDdLbXlrcGZRbwpZcmpqS1hKK0pSNzRFTHJOQ3hlMDJtcmgybk9WL0RMbkZXOURjSHQ4TTJiM2R6MGVtc0pUQUlsK3ZrZ2RqcUZrClIwVnFJWTNxYjZRWTV2OVZ5dVp1Y1hPNGQwQ1QyZEI1NE5nbnQvVnpYaUJiT1d1blBVRDdEd2RSWFE3TnVQaW4Kb1J3NWRrdVZ5SEIvdE5oQjdVOWJqSG82S3Z5UEZRWW1rQkxuREpDR2tOZk1FbXdCR0pXeGkvOXZLYkpTUExQKwpYKzBmOXd0L01aMTh0anZRV0hjaUp0dmdTUXNHdy81aVZXN2JBc1NFTWdPYkYwTng1LzZxZzUvSWJlZXVjWFNpCjUxdUg1RDVhV3pkbHQwZVdIemoyNnppTDkzY2hHcHM0enEwZG9oQVQ3TGZBYUVBTlNmcjB0anlrWDFoc09CMXQKcURHdTZwd2tocStRcnFHYmQ0V01oYXhEWGl1SlE2M0tpMExyNlR3L1A5cTl5aCtDNzlWSzFITDNZa3lKWEwvUwovS2FqVS9KRlBJeXdGZU5QQ2hSSTRoMFV6VUErSDZ2NkFoWXo1Mk44aXZHTzQ5aDlWT2VQRVdLK1JaYzcvcjFmCjBEcUptbURuWlF6Z1FQem9uMnFDUHY1YVFPTGdGMTdrN3hjZzhwMWhFZUw3NmcxSWhzMFo4bkVXUUx6SzR5dGoKTk5pSnRsSjhmalhTYTVTSTRHRHpWWCtrL3NPMWNEaWJpMXMzeUs1VDI1TndMMk9BME9TdmUzRTJIY0ZCb0lJWApGQ0M5dkk4MGhuclA1QjQzeVJFYTBlSjBPVVJ4eXFVM3c4eEczV3ZEK3g2cTYzMWVFL3NBMmlnZU5jNXpudDd3ClV0MkFCZXd5VGI5QS84WTQ4WUtBeTZwR1BMOHMvbnFmZ2wyRFpVUGkzaCtyRkJvSzRXMCtOVWsxckNJWDZPeWYKZHZ2QlRMOS8yeEpKcGtaSy9NMGRaRFprdHNvRWtOa0o5WUZDOE5VUWFUZWQvaVRhUUNYNmd6L0JRTm9kTXRsUQp6VjRZeCtZOXJwWjBlS0hnNGVRU0NFUk9uc3dxanM5S01obDVmNW4zTUF2WTBISkJHMW8zMm5rTHViU0puUlRRCmoyaWxQd0lRVW1qb2RZSjdLS21ibHpQYTRydVFDbWZ0ZFoxb3JSQng5b052eVlKNXVGU3g0a0EwVGhoN0pSakYKZFlpZ1dkcTYwSktnSEFIa0xjOEQ0cU9VWkR1RTVSWmdqYjZGcFRxRjZRemMrd1BVSlh6aW5ockJRR2ZFcFBrNApWYmEwc2lManVyd0tsdkpPZXM3QUZpNWx4aVhWQ0NrMWI3N2Y2YkpkeFAwK1RJaytrd0NXRXZFZXVzb2JETHN0CnBPVDUzRkxFR25DcloyLzVtdndyV1RxczlOV1FPSVhPd3g4T1lXN0N6UkwvZzdGbDdBdU1XTkVPTkNmS3dYK3QKbXBPR3hkK2J1QWlzUk4veFpEdEs5VHNNSmlRbmgzTS9FWmxtQ0ptaFQ3dmVxYlhyCi0tLS0tRU5EIENPTlRBSU5FUi0tLS0tCg==</data>
        <key>AccountTokenCertificate</key>
        <data>LS0tLS1CRUdJTiBDRVJUSUZJQ0FURS0tLS0tCk1JSURaekNDQWsrZ0F3SUJBZ0lCQWpBTkJna3Foa2lHOXcwQkFRVUZBREI1TVFzd0NRWURWUVFHRXdKVlV6RVQKTUJFR0ExVUVDaE1LUVhCd2JHVWdTVzVqTGpFbU1DUUdBMVVFQ3hNZFFYQndiR1VnUTJWeWRHbG1hV05oZEdsdgpiaUJCZFhSb2IzSnBkSGt4TFRBckJnTlZCQU1USkVGd2NHeGxJR2xRYUc5dVpTQkRaWEowYVdacFkyRjBhVzl1CklFRjFkR2h2Y21sMGVUQWVGdzB3TnpBME1UWXlNalUxTURKYUZ3MHhOREEwTVRZeU1qVTFNREphTUZzeEN6QUoKQmdOVkJBWVRBbFZUTVJNd0VRWURWUVFLRXdwQmNIQnNaU0JKYm1NdU1SVXdFd1lEVlFRTEV3eEJjSEJzWlNCcApVR2h2Ym1VeElEQWVCZ05WQkFNVEYwRndjR3hsSUdsUWFHOXVaU0JCWTNScGRtRjBhVzl1TUlHZk1BMEdDU3FHClNJYjNEUUVCQVFVQUE0R05BRENCaVFLQmdRREZBWHpSSW1Bcm1vaUhmYlMyb1BjcUFmYkV2MGQxams3R2JuWDcKKzRZVWx5SWZwcnpCVmRsbXoySkhZdjErMDRJekp0TDdjTDk3VUk3ZmswaTBPTVkwYWw4YStKUFFhNFVnNjExVApicUV0K25qQW1Ba2dlM0hYV0RCZEFYRDlNaGtDN1QvOW83N3pPUTFvbGk0Y1VkemxuWVdmem1XMFBkdU94dXZlCkFlWVk0d0lEQVFBQm80R2JNSUdZTUE0R0ExVWREd0VCL3dRRUF3SUhnREFNQmdOVkhSTUJBZjhFQWpBQU1CMEcKQTFVZERnUVdCQlNob05MK3Q3UnovcHNVYXEvTlBYTlBIKy9XbERBZkJnTlZIU01FR0RBV2dCVG5OQ291SXQ0NQpZR3UwbE01M2cyRXZNYUI4TlRBNEJnTlZIUjhFTVRBdk1DMmdLNkFwaGlkb2RIUndPaTh2ZDNkM0xtRndjR3hsCkxtTnZiUzloY0hCc1pXTmhMMmx3YUc5dVpTNWpjbXd3RFFZSktvWklodmNOQVFFRkJRQURnZ0VCQUY5cW1yVU4KZEErRlJPWUdQN3BXY1lUQUsrcEx5T2Y5ek9hRTdhZVZJODg1VjhZL0JLSGhsd0FvK3pFa2lPVTNGYkVQQ1M5Vgp0UzE4WkJjd0QvK2Q1WlFUTUZrbmhjVUp3ZFBxcWpubTlMcVRmSC94NHB3OE9OSFJEenhIZHA5NmdPVjNBNCs4CmFia29BU2ZjWXF2SVJ5cFhuYnVyM2JSUmhUekFzNFZJTFM2alR5Rll5bVplU2V3dEJ1Ym1taWdvMWtDUWlaR2MKNzZjNWZlREF5SGIyYnpFcXR2eDNXcHJsanRTNDZRVDVDUjZZZWxpblpuaW8zMmpBelJZVHh0UzZyM0pzdlpEaQpKMDcrRUhjbWZHZHB4d2dPKzdidFcxcEZhcjBaakY5L2pZS0tuT1lOeXZDcndzemhhZmJTWXd6QUc1RUpvWEZCCjRkK3BpV0hVRGNQeHRjYz0KLS0tLS1FTkQgQ0VSVElGSUNBVEUtLS0tLQo=</data>
        <key>DeviceCertificate</key>
        <data>'.$DeviceCertRequest.'</data>
        <key>AccountTokenSignature</key>
        <data>'.base64_encode($signature).'</data>
        <key>AccountToken</key>
        <data>'.$str.'</data>
      </dict>
      <key>unbrick</key>
      <true/>
    </dict>
  </dict>
</plist></script><script>var protocolElement = document.getElementById("protocol");var protocolContent = protocolElement.innerText;iTunes.addProtocol(protocolContent);</script></head><body></body></html>
';
} else {
header("Content-type: text/html");
header("Content-Length: 2245");
echo <<<HTML
<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="keywords" content="iTunes Store" /><meta name="description" content="iTunes Store" /><title>iPhone Activation</title><link href="http://static.ips.apple.com/ipa_itunes/stylesheets/shared/common-min.css" charset="utf-8" rel="stylesheet" /><link href="http://static.ips.apple.com/deviceservices/stylesheets/styles.css" charset="utf-8" rel="stylesheet" /><link href="http://static.ips.apple.com/ipa_itunes/stylesheets/pages/IPAJingleEndPointErrorPage-min.css" charset="utf-8" rel="stylesheet" /><script id="protocol" type="text/x-apple-plist"><plist version="1.0">
  <dict>
    <key>iphone-activation</key>
    <dict>
      <key>ack-received</key>
      <true/>
    </dict>
  </dict>
</plist></script><script>var protocolElement = document.getElementById("protocol");var protocolContent = protocolElement.innerText;iTunes.addProtocol(protocolContent);</script></head><body><div id="jingle-page-wrapper"><div id="jingle-page-wrapper-header"><div id="secure"><img src="http://static.ips.apple.com/ipa_itunes/images/lock.png"/></div><div id="banner"><div id="apple-logo"><img src="http://static.ips.apple.com/ipa_itunes/images/apple_chrome.png"/></div><div id="carrier-logo"></div></div></div><div id="jingle-page-wrapper-content"><form method="post" id="jingle-page-form" action=https://albert.apple.com/deviceservices/deviceActivation><div id="jingle-page-content"><div id="IPAJingleEndPointErrorPage"><h1>Congratulations, your iPhone has been unlocked.</h1><p>To set up and sync this iPhone, click Continue.</p></div></div></form><div id="ContinueButtonForm"><form method="post" id="ContinueButtonForm" action=https://albert.apple.com/deviceservices/showSettings><div id="form-submit-buttons"><input type="submit" value="Continue" id="form-submit-buttons-left"/></div></form></div></div></div><div id="jingle-page-wrapper-footer"><div id="footer"><div id="legal">Copyright &copy; 2012 Apple Inc. All rights reserved.| <a target="_blank" href="http://www.apple.com/legal/iphone/us/privacy/">Privacy Policies</a>| <a target="_blank" href="http://www.apple.com/legal/iphone/us/terms/">Terms &amp; Conditions</a></div></div></div></body></html>
HTML;
}
exit;
break;

case 'checkUnbrickHealth':
header("Content-Type: application/xml");
echo '<plist version="1.0">
  <dict>
    <key>Status</key>
    <string>UP</string>
  </dict>
</plist>';

exit;
break;
}
?>
