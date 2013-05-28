<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 28.05.13
 * Time: 1:34
 * To change this template use File | Settings | File Templates.
 */

class ZohoStatsCommand extends CConsoleCommand {
    public function actionIndex()
    {
        $pExcel = new PHPExcel();
        $pExcel->setActiveSheetIndex(0);
        $aSheet = $pExcel->getActiveSheet();

        $curl = curl_init('http://ci.dev.skiliks.com/httpAuth/app/rest/builds?locator=buildType:bt6&count=10000');
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, "bot:gfhjkm");
        $result = curl_exec($curl);
        $xml = simplexml_load_string($result);
        $i = 1;
        $aSheet->setCellValueByColumnAndRow(0, $i, 'ID');
        $aSheet->setCellValueByColumnAndRow(1, $i, 'Status');
        $aSheet->setCellValueByColumnAndRow(2, $i, 'Start time');
        $aSheet->setCellValueByColumnAndRow(3, $i, 'End time');
        $aSheet->setCellValueByColumnAndRow(4, $i, 'Time diff');
        foreach ($xml->build as $build) {
            $curl = curl_init('http://ci.dev.skiliks.com' . $build['href']);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_USERPWD, "bot:gfhjkm");
            $result = curl_exec($curl);
            $buildXml = simplexml_load_string($result);

            $i++;
            $aSheet->setCellValueByColumnAndRow(0, $i, $buildXml['id']);
            $aSheet->setCellValueByColumnAndRow(1, $i, $buildXml['status']);
            $startDate = DateTime::createFromFormat("Ymd\\THis+", $buildXml->startDate);
            $endDate = DateTime::createFromFormat("Ymd\\THis+", $buildXml->finishDate);
            $diffDate = $startDate->diff($endDate);
            $aSheet->setCellValueByColumnAndRow(2, $i, $startDate->format("Y-m-d h:i:s"));
            $aSheet->setCellValueByColumnAndRow(3, $i, $endDate->format("Y-m-d h:i:s"));
            $aSheet->setCellValueByColumnAndRow(4, $i, $diffDate->format("%H:%I:%S"));
            $aSheet->setCellValueByColumnAndRow(5, $i, $buildXml->statusText);
        };
        $objWriter = new PHPExcel_Writer_Excel2007($pExcel);
        $objWriter->save('media/zoho.xls');
    }
}