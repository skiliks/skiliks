<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 6/9/13
 * Time: 10:10 AM
 * To change this template use File | Settings | File Templates.
 */

class ZohoUnitTest extends CDbTestCase
{
    /**
     * Проверяем что подменяемый нами файл остался неизменным, с того момента как мы его подменили.
     */
    public function testCrossDomainJst() {
        $curl = curl_init('https://sheet.zoho.com/pages/crossDomain.jsp');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, []);
        curl_setopt($curl, CURLOPT_POSTFIELDS, []);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $responce = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $oneLineResult = str_replace(["\n","\n\r", "\t", ' '], [''], $responce);

        $this->assertEquals(200, $code);

        $this->assertEquals(
            '<script>(function(){varuserAgent=navigator.userAgent;varisOperaBrowser=(userAgent.indexOf("Opera")!=-1)?true:false;varisIEBrowser=(userAgent.toUpperCase().indexOf("IE")>=0)?true:false;if(!isOperaBrowser&&!isIEBrowser){document.domain="zoho.com";//NOOUTPUTENCODING}function_writeDynamicIframe(content,windowArgsInJson,documentArgsInJson){document.open();if(!isOperaBrowser&&!isIEBrowser){document.domain="zoho.com";//NOOUTPUTENCODING}if(windowArgsInJson){for(variinwindowArgsInJson){window[i]=windowArgsInJson[i];}}if(documentArgsInJson){for(variindocumentArgsInJson){document[i]=documentArgsInJson[i];}}document.write(content);document.close();}})();</script>',
            $oneLineResult
        );
    }
}