<?php

set_time_limit(0);

/**
 * Сервис по работе с документом Excel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentService {
    
    /**
     * Копирование документа
     * 
     * @param string $documentName наименование документа
     * @param int $simId идентификатор симуляции
     * @return int идентификатор скопированного документа
     */
    public static function copy($documentTemplateId, $simId) {
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try
        {
        
            $template = ExcelDocumentTemplate::model()->byId($documentTemplateId)->find();
            if (!$template) throw new Exception("cant find document template by id $documentTemplateId");

            
            
            $templateId = $template->id;
            $fileTemplateId = $template->file_id;
            
            // определить идентификатор файла в симуляции
            $file = MyDocumentsModel::model()->bySimulation($simId)->byTemplateId($fileTemplateId)->find();

            // создаем документ
            $document = new ExcelDocumentModel();
            $document->document_id = $templateId;
            $document->sim_id = $simId;
            $document->file_id = $file->id;
            $document->insert();
            $documentId = $document->id;

            // Копируем рабочие листы
            $woksheets = ExcelWorksheetTemplate::model()->byDocument($templateId)->findAll();
            $worksheetCollection = array();  // рабочие листы, которые нужно скопировать
            $worksheetMap = array();
            foreach($woksheets as $worksheet) {
                Logger::debug("load ws : ".$worksheet->name);
                $newWorksheet = new ExcelWorksheetModel();
                $newWorksheet->document_id = $documentId;
                $newWorksheet->name = $worksheet->name;
                $newWorksheet->cellWidth = $worksheet->cellWidth;
                $newWorksheet->cellHeight = $worksheet->cellHeight;
                $newWorksheet->insert();

                $worksheetCollection[] = $worksheet->id;
                $worksheetMap[$worksheet->id] = $newWorksheet->id;
            }

            // копируем ячейки

            $sql = "insert into excel_worksheet_cells 
                    (worksheet_id, `string`, `column`, `value`, read_only, 
                    `comment`, formula, colspan, rowspan, `bold`, `color`, 
                    `font`, `fontSize`, `borderTop`, `borderBottom`, `borderLeft`, `borderRight`)
                    select 
                        :newWorksheetId, 
                        `string`, 
                        `column`, 
                        t.value, 
                        t.read_only, 
                        t.comment, 
                        t.formula, 
                        t.colspan, 
                        t.rowspan,
                        t.bold, 
                        t.color, 
                        t.font, 
                        t.fontSize,
                        t.borderTop, 
                        t.borderBottom,
                        t.borderLeft,
                        t.borderRight
                    from excel_worksheet_template_cells as t
                    where t.worksheet_id = :oldWorksheetId";
            $command = $connection->createCommand($sql);       
            foreach($worksheetMap as $oldWorksheetId=>$newWorksheetId) {
                $command->bindParam(":newWorksheetId", $newWorksheetId, PDO::PARAM_INT);
                $command->bindParam(":oldWorksheetId", $oldWorksheetId, PDO::PARAM_INT);
                $command->execute();
            }
        
          $transaction->commit();
        }
        catch(Exception $e) // в случае ошибки при выполнении запроса выбрасывается исключение
        {
            $transaction->rollBack();
        }
        
        
        return $documentId;
    }
}

?>
