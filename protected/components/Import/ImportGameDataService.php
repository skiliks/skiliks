<?php
/**
 * @author slavka
 */
class ImportGameDataService
{
    private $filename = null;

    private $import_id = null;

    private $errors = null;

    private $cache_method = null;

    private $columnNoByName = [];

    private $importedEvents = [];

    public function __construct()
    {
        $this->filename = __DIR__ . '/../../../media/scenario.xlsx';
        $this->import_id = $this->getImportUUID();
        $this->cache_method = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
    }

    public function setFilename($name)
    {
        $this->filename = __DIR__ . '/../../../media/' . $name;
    }

    /**
     * Import characters, requires nothing
     *
     * @return mixed array
     *
     * @throws Exception
     */
    public function importCharacters()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Faces_new');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'id_персонажа', $i)) {
                continue;
            }

            // try to find exists entity 
            $character = Characters::model()->byCode($this->getCellValue($sheet, 'id_персонажа', $i))->find();

            // create entity if not exists {
            if (null === $character) {
                $character = new Characters();
            }
            // create entity if not exists }

            // update data {
            $character->code = $this->getCellValue($sheet, 'id_персонажа', $i);
            $character->title = $this->getCellValue($sheet, 'Должность', $i);
            $character->fio = $this->getCellValue($sheet, 'ФИО - short', $i);
            $character->email = $this->getCellValue($sheet, 'e-mail', $i);
            $character->skype = $this->getCellValue($sheet, 'skype', $i);
            $character->phone = $this->getCellValue($sheet, 'телефон', $i);
            $character->import_id = $this->import_id;

            // save
            $character->save();

            $importedRows++;
        }

        // delete old unused data {
        Characters::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_characters' => $importedRows,
            'errors' => false,
        );
    }

    /**
     * @return mixed array
     */
    public function importLearningGoals()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Номер цели обучения', $i)) {
                continue;
            }

            // try to find exists entity 
            $learningGoal = LearningGoal::model()
                ->byCode($this->getCellValue($sheet, 'Номер цели обучения', $i))
                ->find();

            // create entity if not exists {
            if (null === $learningGoal) {
                $learningGoal = new LearningGoal();
                $learningGoal->code = $this->getCellValue($sheet, 'Номер цели обучения', $i);
            }
            // create entity if not exists }

            // update data {
            $learningGoal->title = $this->getCellValue($sheet, 'Наименование цели обучения', $i);
            $learningGoal->import_id = $this->import_id;

            // save
            $learningGoal->save();

            $importedRows++;

        }

        // delete old unused data {
        LearningGoal::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_learning_goals' => $importedRows,
            'errors' => false,
        );
    }

    public function importMailConstructor()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Constructor');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        $endCol = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());
        for ($col = 0; $col < $endCol; $col++) {
            $constructorCode = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            $constructor = MailConstructor::model()->findByAttributes(['code' => $constructorCode]);
            if ($constructor === null) {
                $constructor = new MailConstructor();
            }
            $constructor->code = $constructorCode;
            $constructor->import_id = $this->import_id;
            $constructor->save();
            for ($row = 2; $row < $sheet->getHighestDataRow(); $row++) {
                $phraseValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                $phrase = MailPhrasesModel::model()->findByAttributes(['code' => $constructorCode, 'name' => $phraseValue]);
                if ($phrase === null) {
                    $phrase = new MailPhrasesModel();
                }
                $phrase->code = $constructor->code;
                $phrase->name = $phraseValue;
                $phrase->import_id = $this->import_id;
                $phrase->save();
                $importedRows ++;
            }
        }

        // delete old unused data {
        MailPhrasesModel::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        MailConstructor::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }


        echo __METHOD__ . " end\n";
        return ['ok' => 1];
    }
    /**
     *
     */
    public function importCharactersPointsTitles()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Номер требуемого поведения', $i)) {
                continue;
            }

            // try to find exists entity 
            $charactersPointsTitle = CharactersPointsTitles::model()
                ->byCode($this->getCellValue($sheet, 'Номер требуемого поведения', $i))
                ->find();

            // create entity if not exists {
            if (null === $charactersPointsTitle) {
                $charactersPointsTitle = new CharactersPointsTitles();
                $charactersPointsTitle->code = $this->getCellValue($sheet, 'Номер требуемого поведения', $i);
            }
            // create entity if not exists }

            // update data {
            $charactersPointsTitle->title = $this->getCellValue($sheet, 'Наименование требуемого поведения', $i);
            $charactersPointsTitle->learning_goal_code = $this->getCellValue($sheet, 'Номер цели обучения', $i);
            $charactersPointsTitle->scale = $this->getCellValue($sheet, 'Единая шкала', $i); // Makr
            $charactersPointsTitle->type_scale = CharactersPointsTitles::getCharacterpointScaleId($this->getCellValue($sheet, 'Тип шкалы', $i));
            $charactersPointsTitle->import_id = $this->import_id;

            // save
            $charactersPointsTitle->save();

            $importedRows++;

        }

        // delete old unused data {
        CharactersPointsTitles::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_character_point_titles' => $importedRows,
            'errors' => false,
        );
    }

    public function importEmails()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        $counter = array(
            'all' => 0,
            'MY' => 0,
            'M' => 0,
            'MSY' => 0,
            'MS' => 0,
            'mark-codes' => 0,
            'mark-0' => 0,
            'mark-1' => 0,
        );

        $emailIds = array(); // to delete old letters after import
        $emailToCopyIds = array(); // to delete old letter-cope relations after import
        $emailToRecipientIds = array(); // to delete old letter-recipient relations after import
        $emailToPointIds = array(); // to delete old letter-point relations after import
        $emailSubjectsIds = array(); // to delete old letter-"theme" relations after import

        $characters = array();
        $charactersList = Characters::model()->findAll();

        foreach ($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }

        // загрузим информацию о поинтах
        $pointsTitles = CharactersPointsTitles::model()->findAll();
        $pointsInfo = array();
        foreach ($pointsTitles as $item) {
            $pointsInfo[$item->code] = $item->id;
        }

        // Get all exist system mail_templates to avoid SQL queries againts each request {
        $existsMailTemplate = array();
        foreach (MailTemplateModel::model()->findAll() as $mailTemplate) {
            $existsMailTemplate[$mailTemplate->code] = $mailTemplate;
        }
        // Get all mail_templates }

        // Get all exist system mail_themes to avoid SQL queries againts each request {
        $existsMailThemes = array();
        foreach (CommunicationTheme::model()->findAll() as $mailTheme) {
            $existsMailThemes[$mailTheme->text] = $mailTheme;
        }
        // Get all mail_themes }

        $exists = array();

        $index = 0;
        $pointsCodes = array();

        $START_COL = $this->columnNoByName['Задержка прихода письма'] + 1;
        $END_COL = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
        for ($columnIndex = $START_COL; $columnIndex <= $END_COL; $columnIndex++) {
            $code = $sheet->getCellByColumnAndRow($columnIndex, 2)->getValue();
            if ($code === null) {
                $END_COL = $columnIndex;
                break;
            }
            $pointsCodes[$columnIndex] = $code;
            $counter['mark-codes']++;
        }

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);
            if (null === $code || '' === $code) {
                continue;
            }
            $sendingDate = date('Y-m-d', (int)PHPExcel_Shared_Date::ExcelToPHP($this->getCellValue($sheet, 'Date', $i)));
            $sendingTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Time', $i), 'hh:mm:ss');

            $fromCode = $this->getCellValue($sheet, 'From _code', $i);
            $toCode = $this->getCellValue($sheet, 'To_code', $i);
            $copies = $this->getCellValue($sheet, 'Copy_code', $i);

            $subject_id = $this->getCellValue($sheet, 'Theme_id', $i);

            // Письмо
            $message = $this->getCellValue($sheet, 'Mail_body', $i);

            $flag = $this->getCellValue($sheet, 'Переключение флагов 1', $i);

            $typeOfImportance = trim($sheet->getCellByColumnAndRow($this->columnNoByName['Mail_type_for_assessment'], $i->key())->getValue());

            if (false === isset($exists[$code])) {
                $exists[$code] = 1;
            } else {
                $exists[$code]++;
            }

            $group = 5;
            $type = 0;
            // определение группы по коду
            $source = null;
            if (preg_match("/MY\d+/", $code)) {
                $group = 1;
                $type = 3;
                $counter['MY']++;
            } else if (preg_match("/M\d+/", $code)) {
                $type = 1;
                $counter['M']++;
            } else if (preg_match("/MSY\d+/", $code)) {
                $group = 3;
                $type = 4;
                $counter['MSY']++;
            } else if (preg_match("/MS\d+/", $code)) {
                $type = 2;
                $counter['MS']++;
            } else {
                assert(false, 'Unknown code: ' . $code);
            }

            if (!isset($characters[$fromCode])) {
                return array(
                    'status' => false,
                    'text' => "cant find character by code $fromCode <br/> Line $index",
                );
            }
            $fromId = $characters[$fromCode];

            // tmp
            $copiesArr = array();
            if (strstr($copies, ',')) {
                $copiesArr = explode(',', $copies);
            }

            if (strstr($toCode, ',')) {
                $toCode = explode(',', $toCode);
            }

            $receivers = array();
            if (is_array($toCode)) {
                $receivers = $toCode;
                $toCode = $toCode[0];
            }

            if (!isset($characters[$toCode])) {
                throw new Exception("Can`t find character by code $toCode");
            }
            $toId = $characters[$toCode];

            $date = explode('-', $sendingDate);
            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }
            $themePrefix = $this->getCellValue($sheet, 'Theme_prefix', $i);
            if ($themePrefix === '-') {
                $themePrefix = null;
            }
            // themes update {
            // for MSx
            $subjectEntity = CommunicationTheme::model()->findByAttributes([
                'code'         => $subject_id,
                'character_id' => $toId,
                'mail_prefix'  => $themePrefix,
                'theme_usage'  => 'mail_outbox'
            ]);

            // for MSYx
            if ($subjectEntity === null) {
                $subjectEntity = CommunicationTheme::model()->findByAttributes([
                    'code'         => $subject_id,
                    'character_id' => $toId,
                    'mail_prefix'  => $themePrefix,
                    'theme_usage'  => 'mail_outbox_old'
                ]);
            }

            // for Mx
            if ($subjectEntity === null) {
                $subjectEntity = CommunicationTheme::model()->findByAttributes([
                    'code'         => $subject_id,
                    'character_id' => $toId,
                    'mail_prefix'  => $themePrefix,
                    'theme_usage'  => 'mail_inbox'
                ]);
            }

            if ($subjectEntity === null) {
                $subjectEntity = CommunicationTheme::model()->findByAttributes([
                    'code'         => $subject_id,
                    'character_id' => null,
                    'mail_prefix'  => $themePrefix
                ]);
            }

            if ($subjectEntity === null) {
                throw new Exception('No subject for mail code '.$code.', theme id '.$subject_id);
            }
            $emailSubjectsIds[] = $subjectEntity->primaryKey;
            // themes update }

            $emailTemplateEntity = MailTemplateModel::model()->findByAttributes(['code' => $code]);
            if ($emailTemplateEntity === null) {
                $emailTemplateEntity = new MailTemplateModel();
                $emailTemplateEntity->code = $code;
            }
            $emailTemplateEntity->group_id = $group;
            $emailTemplateEntity->sender_id = $fromId;
            $emailTemplateEntity->receiver_id = $toId;
            $emailTemplateEntity->subject_id = $subjectEntity->id;
            $emailTemplateEntity->message = $message;
            $emailTemplateEntity->sent_at = $sendingDate . ' ' . $sendingTime;
            $emailTemplateEntity->type = $type;
            $emailTemplateEntity->type_of_importance = $typeOfImportance;
            $emailTemplateEntity->import_id = $this->import_id;
            $emailTemplateEntity->flag_to_switch = (NULL == $flag) ? NULL : $flag;

            $emailTemplateEntity->save();
            $emailIds[] = $emailTemplateEntity->id;

            // учтем поинты (оценки, marks)
            $columnIndex = $START_COL;
            while ($columnIndex < $END_COL) {
                $value = $sheet->getCellByColumnAndRow($columnIndex, $i->key())->getValue();
                ;
                if ($value === null || $value === "") {
                    $columnIndex++;
                    continue;
                }
                $pointCode = $pointsCodes[$columnIndex];
                if (!isset($pointsInfo[$pointCode])) throw new Exception("cant get point id by code $pointCode");
                $pointId = $pointsInfo[$pointCode];

                /** @var MailPointsModel $pointEntity */
                $pointEntity = MailPointsModel::model()->byMailId($emailTemplateEntity->id)->byPointId($pointId)->find();
                if (null === $pointEntity) {
                    $pointEntity = new MailPointsModel();
                }
                $pointEntity->mail_id = $emailTemplateEntity->id;
                $pointEntity->point_id = $pointId;
                $pointEntity->add_value = $value;
                $pointEntity->import_id = $this->import_id;
                $pointEntity->save();

                $emailToPointIds[] = $pointEntity->id;

                if (1 == (int)$value) {
                    $counter['mark-1']++;
                } else {
                    $counter['mark-0']++;
                }

                $columnIndex++;
            }

            foreach (array_values($receivers) as $receiverCode) {
                if (!isset($characters[$receiverCode])) {
                    throw new Exception("cant find receiver by code $receiverCode");
                }
                $receiverId = $characters[$receiverCode];

                // Проверяется не значится ли у нас для такого письма уже такой получатель и если нет то добавляем запись
                $mrt = MailReceiversTemplateModel::model()->byMailId($emailTemplateEntity->id)->byReceiverId($receiverId)->find();
                if (null === $mrt) {
                    $mrt = new MailReceiversTemplateModel();
                    $mrt->mail_id = $emailTemplateEntity->id;
                    $mrt->receiver_id = $receiverId;
                    $mrt->insert();
                }

                $emailToRecipientIds[] = $mrt->id;
            }

            // а теперь учтем копии
            foreach ($copiesArr as $ind => $characterCode) {
                if (!isset($characters[$characterCode])) {
                    return array(
                        'status' => false,
                        'text' => "cant find character by code $characterCode",
                    );
                }
                $characterId = $characters[$characterCode];

                $mct = MailCopiesTemplateModel::model()
                    ->byMailId($emailTemplateEntity->id)
                    ->byReceiverId($characterId)
                    ->find();

                if (null === $mct) {
                    $mct = new MailCopiesTemplateModel();
                    $mct->mail_id = $emailTemplateEntity->id;
                    $mct->receiver_id = $characterId;
                    $mct->insert();
                }

                $emailToCopyIds[] = $mct->id;
            }

            $counter['all']++;
        }

        // remove old entities {
        // copy relations {
        if (0 !== count($emailToCopyIds)) {
            $emailCopyEntities = MailCopiesTemplateModel::model()
                ->byIdsNotIn(implode(',', $emailToCopyIds))
                ->findAll();

            foreach ($emailCopyEntities as $entity) {
                $entity->delete();
            }

            unset($entity);
        }
        // copy relations }

        // recipient relations {
        /** @var MailReceiversModel[] $emailRecipientEntities */
        $emailRecipientEntities = MailReceiversModel::model()
            ->byIdsNotIn(implode(',', $emailToRecipientIds))
            ->findAll();

        foreach ($emailRecipientEntities as $entity) {
            $entity->delete();
        }
        unset($entity);
        // recipient relations }

        // points relations {
        if (0 !== count($emailToPointIds)) {
            $emailPointsEntities = MailPointsModel::model()
                ->byIdsNotIn(implode(',', $emailToPointIds))
                ->findAll();

            foreach ($emailPointsEntities as $entity) {
                $entity->delete();
            }
            unset($entity);
        }
        // points relations }


        // mail templates {
        if (0 !== count($emailIds)) {
            $emailTemplates = MailTemplateModel::model()
                ->byIdsNotIn(implode(',', $emailIds))
                ->findAll();

            foreach ($emailTemplates as $emailTemplate) {
                $emailTemplate->delete();
            }
        }
        // mail templates }
        // remove old entities }

        $html = sprintf(
            'Must be values regarding  (21 Dec 2012)  <br/>
            Lines imported: %s . must be 97<br/>
            <br/>
            Inbox: %s. must be 42<br/>
            - MY: %s. must be 38<br/>
            - M: %s. must be 4<br/>
            <br/>
            Outbox: %s. must be 55<br/>
            - MSY: %s. must be 1<br/>
            - MS: %s. must be 54<br/>
            <br/>
            Marks codes amount: %s must be 114<br/>
            - Marks "0": %s. must be 13<br/>
            - Marks "1": %s. must be 32<br/>
            <br/>
            Email import was finished. <br>
            ',
            $counter['all'],
            $counter['M'] + $counter['MY'],
            $counter['M'],
            $counter['MY'],
            $counter['MS'] + $counter['MSY'],
            $counter['MSY'],
            $counter['MS'],
            $counter['mark-codes'],
            $counter['mark-0'],
            $counter['mark-1']
        );
        MailTemplateModel::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));
        CommunicationTheme::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));

        echo __METHOD__ . " end \n";

        return array(
            'status' => true,
            'text' => $html,
        );
    }

    /**
     * Requires characters, emails (F-S-C)
     *
     * @throws CException
     * @throws Exception
     * @return array
     */
    public function importEmailSubjects()
    {
        echo __METHOD__ . "\n";

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL Themes');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }


        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach ($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }

        $nullCharacter = new Characters();
        $charactersList[] = $nullCharacter;

        $html = '';

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $themeId = $this->getCellValue($sheet, 'Original_Theme_id', $i); // A
            // Определение кода персонажа
            $characterCode = $this->getCellValue($sheet, 'To_code', $i); // A
            if ($characterCode === '' || $characterCode === '-' || NULL == $characterCode) {
                $characterCode = null;
                $characterId = null;
            } else {
                $characterId = $characters[$characterCode];
            }
            // Определим тему письма
            $subjectText = $this->getCellValue($sheet, 'Original_Theme_text', $i);
            //$subjectText = StringTools::fixReAndFwd($subjectText);
            // Phone
            $themeUsage = $this->getCellValue($sheet, 'Theme_usage', $i);
            $phone = $themeUsage === 'phone';
            // Phone W/R
            $phoneWr = $this->getCellValue($sheet, 'Phone W/R', $i);
            // Phone dialogue number
            $phoneDialogNumber = $this->getCellValue($sheet, 'Phone dialogue number', $i);
            // Mail
            $mail = $themeUsage === 'mail_outbox' || $themeUsage === 'mail_inbox';
            // Mail letter number
            $mailCode = $this->getCellValue($sheet, 'Mail letter number', $i);
            if ($mailCode === 'НЕ исход. письмо' || $mailCode === 'MS не найдено') {
                $mailCode = null;
            }
            $mailCode = ('' !== $mailCode) ? $mailCode : null;
            $mailPrefix = $this->getCellValue($sheet, 'Theme_prefix', $i);
            if ($mailPrefix === '' || $mailPrefix === '-') {
                $mailPrefix = null;
            }
            // Mail W/R
            $wr = $this->getCellValue($sheet, 'Mail W/R', $i);
            // Mail constructor number
            $constructorNumber = $this->getCellValue($sheet, 'Mail constructor number', $i);
            // Source of outbox email
            $source = $this->getCellValue($sheet, 'Source', $i);

            $usage = $this->getCellValue($sheet, 'Theme_usage', $i);

            /**
             * @var CommunicationTheme $communicationTheme
             */
            $communicationTheme = CommunicationTheme::model()
                ->byLetterNumber($mailCode)
                ->byText($subjectText)
                ->byCharacter($characterId)
                ->find();

            if (null === $communicationTheme) {
                $communicationTheme = new CommunicationTheme();
            }

            $communicationTheme->text = $subjectText;
            $communicationTheme->letter_number = $mailCode;
            $communicationTheme->character_id = $characterId;
            $communicationTheme->wr = $wr;
            $communicationTheme->constructor_number = $constructorNumber;
            $communicationTheme->phone = $phone;
            $communicationTheme->phone_wr = $phoneWr;
            $communicationTheme->phone_dialog_number = $phoneDialogNumber;
            $communicationTheme->mail = $mail;
            $communicationTheme->mail_prefix = $mailPrefix;
            $communicationTheme->code = $themeId;
            $communicationTheme->source = $source;
            $communicationTheme->theme_usage = $usage;
            $communicationTheme->import_id = $this->import_id;

            $communicationTheme->save();

            if ($communicationTheme->mail) {
                // add fwd for all themes without fwd {
                foreach ($charactersList as $character) {
                    if (!MailPrefix::model()->findByPk(sprintf('fwd%s', $communicationTheme->mail_prefix))) {
                        throw new Exception('MailPrefix ' . 'fwd' . $communicationTheme->mail_prefix . ' not found.');
                    }
                    $goodTheme = CommunicationTheme::model()->findByAttributes([
                        'code' => $communicationTheme->code,
                        'character_id' => $character->primaryKey,
                        'mail_prefix' => sprintf('fwd%s', $communicationTheme->mail_prefix),
                    ]);
                    if ($goodTheme !== null) {
                        $goodTheme->import_id = $this->import_id;
                        $goodTheme->save();
                        continue;
                    }

                    $wrongTheme = new CommunicationTheme();
                    $wrongTheme->mail = 1;
                    $wrongTheme->mail_prefix = sprintf('fwd%s', $communicationTheme->mail_prefix);
                    assert($wrongTheme->mail_prefix !== null);
                    $wrongTheme->wr = 'W';
                    $wrongTheme->code = $communicationTheme->code;
                    $wrongTheme->text = $communicationTheme->text;
                    $wrongTheme->constructor_number = 'B1';
                    $wrongTheme->character_id = $character->primaryKey;
                    $wrongTheme->import_id = $this->import_id;
                    $wrongTheme->save();
                }
                // add fwd for all themes without fwd }

                // add re for all themes without fwd {
                foreach ($charactersList as $character) {
                    if (!MailPrefix::model()->findByPk(sprintf('re%s', $communicationTheme->mail_prefix))) {
                        continue;
                    }
                    $goodTheme = CommunicationTheme::model()->findByAttributes([
                        'code' => $communicationTheme->code,
                        'character_id' => $character->primaryKey,
                        'mail_prefix' => sprintf('re%s', $communicationTheme->mail_prefix),
                    ]);
                    if ($goodTheme !== null) {
                        $goodTheme->import_id = $this->import_id;
                        $goodTheme->save();
                        continue;
                    }


                    $wrongTheme = new CommunicationTheme();
                    $wrongTheme->mail = 1;
                    $wrongTheme->mail_prefix = sprintf('re%s', $communicationTheme->mail_prefix);
                    assert($wrongTheme->mail_prefix !== null);
                    $wrongTheme->wr = 'W';
                    $wrongTheme->code = $communicationTheme->code;
                    $wrongTheme->text = $communicationTheme->text;
                    $wrongTheme->constructor_number = 'B1';
                    $wrongTheme->character_id = $character->primaryKey;
                    $wrongTheme->import_id = $this->import_id;
                    $wrongTheme->save();
                }
                // add re for all themes without fwd }
            }
        }

        // remove all old, unused characterMailThemes after import {
        CommunicationTheme::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));

        $html .= "Email from characters import finished! <br/>";

        echo __METHOD__ . " end \n";

        return array(
            'status' => true,
            'text' => $html,
        );
    }

    public function importTasks()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('to-do-list');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            // Код
            $code = $this->getCellValue($sheet, 'Plan_code', $i);
            if ($code === null)
                continue;
            // Тип старта задачи
            $startType = $this->getCellValue($sheet, 'Plan_type', $i);
            // Список дел в to-do-list
            $name = $this->getCellValue($sheet, 'Список дел в to-do-list', $i);
            // Жесткая
            $startTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Жесткая', $i), 'hh:mm:ss');
            ;

            // Категория
            $category = $this->getCellValue($sheet, 'Категория', $i);
            // Мин.
            $duration = $this->getCellValue($sheet, 'Мин.', $i);

            $task = Task::model()->byCode($code)->find();
            if (!$task) {
                $task = new Task();
                $task->code = $code;
            }

            $task->title = $name;
            $task->start_time = $startTime;
            $task->duration = $duration;
            if ($startTime !== null) {
                $task->type = 2;
            } else {
                $task->type = 1;
            }
            $task->start_type = $startType;
            $task->category = $category;
            $task->import_id = $this->import_id;
            $task->save();
        }
        Task::model()->deleteAll('import_id<>:import_id OR import_id IS NULL', array('import_id' => $this->import_id));

        echo __METHOD__ . " end \n";

        return array(
            'status' => true,
            'text' => sprintf('%s tasks have been imported.', Task::model()->count()),
        );
    }

    /**
     * Импорт задач для писем M-T
     */
    public function importMailTasks()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('M-T');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Mail code', $i)) {
                continue;
            }

            $mail = MailTemplateModel::model()
                ->byCode($this->getCellValue($sheet, 'Mail code', $i))
                ->find();

            if (!$mail) {
                break;
            }

            // try to find exists entity 
            $mailTask = MailTasksModel::model()
                ->byMailId($mail->id)
                ->byName($this->getCellValue($sheet, 'Task', $i))
                ->find();

            // create entity if not exists {
            if (null === $mailTask) {
                $mailTask = new MailTasksModel();
                $mailTask->mail_id = $mail->id;
                $mailTask->name = $this->getCellValue($sheet, 'Task', $i);
            }
            // create entity if not exists }

            // update data {
            $mailTask->duration = $this->getCellValue($sheet, 'Duration', $i);
            $mailTask->code = $this->getCellValue($sheet, 'Mail code', $i);
            $mailTask->wr = $this->getCellValue($sheet, 'Task W/R', $i);
            $mailTask->category = $this->getCellValue($sheet, 'Category', $i);
            $mailTask->import_id = $this->import_id;

            // save
            $mailTask->save();

            $importedRows++;
        }

        // delete old unused data {
        MailTasksModel::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_documents' => $importedRows,
            'errors' => false,
        );
    }

    public function importFlags()
    {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Flags');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Flag_code', $i);
            if ($code === null) {
                continue;
            }
            $flag = Flag::model()->findByAttributes(['code' => $code]);
            if ($flag === null) {
                $flag = new Flag();
            }
            //$flag->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flag->code = $code;
            $flag->description = $this->getCellValue($sheet, 'Flag_name', $i);
            $flag->import_id = $this->import_id;
            $flag->save();
            Flag::model()->deleteAll('import_id <> :import_id', ['import_id' => $this->import_id]);
        }
        return [
            'status' => true,
            'text' => sprintf("imported %d flags", Flag::model()->count())
        ];
    }

    /**
     * Импорт событий из писем
     */
    public function importMailEvents()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);

            if ($code === null) {
                continue;
            }

            $sendingTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Time', $i), 'hh:mm:ss');
            assert($sendingTime !== null);
            $event = EventSample::model()->byCode($code)->find();
            if (!$event) {
                $event = new EventSample();
                $event->code = $code;
            }

            $event->on_ignore_result = 7;
            $event->on_hold_logic = 1;
            $event->trigger_time = $sendingTime;
            $event->import_id = $this->import_id;
            $event->save();
        }

        echo __METHOD__ . " end \n";

        return array(
            'status' => true,
            'text' => sprintf('%s mail events have been imported.', EventSample::model()->count('code LIKE "M%"')),
        );
    }

    public function importMailAttaches()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        $documents = MyDocumentsService::getAllCodes();
        $index = 0;
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);
            $attache = $this->getCellValue($sheet, 'Attachment', $i);

            if ($attache == '' || $attache == '-') continue; // нет аттачей

            $mail = MailTemplateModel::model()->byCode($code)->find();
            $fileId = $documents[$attache];

            $attacheModel = MailAttachmentsTemplateModel::model()->byMailId($mail->id)->byFileId($fileId)->find();
            if ($attacheModel === null) {
                $attacheModel = new MailAttachmentsTemplateModel();
            }
            $attacheModel->mail_id = $mail->id;
            $attacheModel->file_id = $fileId;
            $attacheModel->import_id = $this->import_id;
            $attacheModel->save();

        }

        // delete old unused data {
        MailAttachmentsTemplateModel::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'status' => true,
            'text' => sprintf('%s mail attaches have been imported.', $index),
        );
    }

    /**
     * Импорт документов
     */
    public function importMyDocuments()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Documents');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 1);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Document_code', $i)) {
                continue;
            }

            // try to find exists entity 
            $document = DocumentTemplate::model()
                ->byCode($this->getCellValue($sheet, 'Document_code', $i))
                ->find();

            // create entity if not exists {
            if (null === $document) {
                $document = new DocumentTemplate();
                $document->code = $this->getCellValue($sheet, 'Document_code', $i);
            }
            // create entity if not exists }

            // update data {
            $document->fileName = sprintf('%s.%s', $this->getCellValue($sheet, 'Document_name', $i), $this->getCellValue($sheet, 'Document_extension', $i));

            // may be this is hack, but let it be {
            $document->srcFile = StringTools::CyToEn($document->fileName); // cyrilic to latinitsa
            $document->srcFile = str_replace(' ', '_', $document->srcFile);
            $document->srcFile = str_replace('.xlsx', '.xlsx', $document->srcFile);
            $document->srcFile = str_replace('.docx', '.pdf', $document->srcFile);
            $document->srcFile = str_replace('.pptx', '.pdf', $document->srcFile);
            // may be this is hack, but let it be }

            $document->format = $this->getCellValue($sheet, 'Document_extension', $i);

            $document->type = $this->getCellValue($sheet, 'Document_type', $i);
            $document->hidden = 'start' === $document->type ? 0 : 1;
            $document->import_id = $this->import_id;

            // save
            $document->save();

            /*if ($document->format === 'xls') {
                $excel = ExcelDocumentTemplate::model()->findByAttributes(['file_id' => $document->id]);
                if (null === $excel) {
                    $excel = new ExcelDocumentTemplate();
                    $excel->name = 'unused, TODO: remove';
                    $excel->file_id = $document->primaryKey;
                }
                $excel->save();
            }*/

            $importedRows++;
        }

        // delete old unused data {
        DocumentTemplate::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_documents' => $importedRows,
            'errors' => false,
        );
    }

    /**
     *
     */
    public function importDialogReplicas()
    {
        echo __METHOD__ . "\n";

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        // DialogSubtype
        $subtypes = [];
        foreach (DialogSubtype::model()->findAll() as $subtype) {
            $subtypes[$subtype->title] = $subtype->id;
        }

        $importedRows = 0;

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {

            // in the bottom of excel sheet we have a couple of check sum, that aren`t replics sure.
            $dialog_excel_id = $this->getCellValue($sheet, 'id записи', $i);
            if (NULL == $dialog_excel_id) {
                continue;
            }

            $dialog = Replica::model()
                ->byExcelId($dialog_excel_id)
                ->find();
            if (NULL === $dialog) {
                $dialog = new Replica(); // Создаем событие
                $dialog->excel_id = $dialog_excel_id;
            }

            // a lot of dialog properties: {
            $dialog->code = $this->getCellValue($sheet, 'Event_code', $i);
            $dialog->event_result = 7; // ничего
            $from_character_code = $this->getCellValue($sheet, 'Персонаж-ОТ (код)', $i);
            $dialog->ch_from = Characters::model()->findByAttributes(['code' => $from_character_code])->primaryKey;
            $to_character_code = $this->getCellValue($sheet, 'Персонаж-КОМУ (код)', $i);
            $dialog->ch_to = Characters::model()->findByAttributes(['code' => $to_character_code])->primaryKey;

            $subtypeAlias = $this->getCellValue($sheet, 'Тип интерфейса диалога', $i);
            if (!isset($subtypes[$subtypeAlias])) {
                throw new Exception('Unknown dialog type: ' . $subtypeAlias);
            }
            $dialog->dialog_subtype = (isset($subtypes[$subtypeAlias])) ? $subtypes[$subtypeAlias] : NULL; // 1 is "me"

            $code = $this->getCellValue($sheet, 'Event_result_code', $i);
            $dialog->next_event = $this->getNextEventId($code);

            $dialog->next_event_code = ('-' == $code) ? NULL : $code;
            $text = $this->getCellValue($sheet, 'Реплика', $i);
            $text = preg_replace('/^\s*-[\s ]*/', ' — ', $text);
            $dialog->text = $text;
            $dialog->step_number = $this->getCellValue($sheet, '№ шага в диалоге', $i);
            $dialog->replica_number = $this->getCellValue($sheet, '№ реплики в диалоге', $i);
            $dialog->delay = $this->getCellValue($sheet, 'Задержка, мин', $i);

            $flagCode = $this->getCellValue($sheet, 'Переключение флагов 1', $i);
            if ($flagCode !== '') {
                $flag = Flag::model()->findByAttributes([
                    'code' => $flagCode
                ]);
                //assert($flag, 'Flag for ' . $flagCode);
                $dialog->flag_to_switch = $flag->code;
            } else {
                $dialog->flag_to_switch = null;
            }

            $isUseInDemo = ('да' == $this->getCellValue($sheet, 'Использовать в DEMO', $i)) ? 1 : 0;
            $dialog->demo = $isUseInDemo;
            $dialog->type_of_init = $this->getCellValue($sheet, 'Тип запуска', $i);

            $sound = $this->getCellValue($sheet, 'Имя звук/видео файла', $i);
            $dialog->sound = ($sound == 'нет' || $sound == '-') ? $file = NULL : $sound;

            $isFinal = $this->getCellValue($sheet, 'Конечная реплика (да/нет)', $i);
            $dialog->is_final_replica = ('да' === $isFinal) ? true : false;

            $dialog->import_id = $this->import_id;
            // a lot of dialog properties: }

            $dialog->save();

            $importedRows++;
        }

        // delete old unused data {
        Replica::model()->deleteAll(
            'import_id <> :import_id OR import_id IS NULL',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_dialog_replics' => $importedRows,
            'errors' => false,
        );
    }

    /**
     *
     */
    public function importDialogPoints()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        // link points to excelColums: pint titles placed in row 2 {        
        $points = [];
        foreach (CharactersPointsTitles::model()->findAll() as $point) {
            if (isset($this->columnNoByName[$point->code])) {
                $points[] = $point;
            }
        }
        // link points to excelColums }

        $importedRows = 0;

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            // in the bottom of excel sheet we have a couple of check sum, that aren`t replics sure.
            if (NULL == $this->getCellValue($sheet, 'id записи', $i)) {
                continue;
            }

            $dialog = Replica::model()
                ->byExcelId($this->getCellValue($sheet, 'id записи', $i))
                ->find();

            if (NULL === $dialog) {
                throw new Exception('Try to use unexisi in DB dialog, with ExcelId ' . $this->getCellValue($sheet, 'id записи', $i));
            }

            foreach ($points as $point) {
                $score = $this->getCellValue($sheet, $point->code, $i);

                // ignore empty cells, but we must imort all "0" values!
                if (NULL === $score || '' === $score) {
                    continue;
                }

                $charactersPoints = CharactersPoints::model()
                    ->byDialog($dialog->id)
                    ->byPoint($point->id)
                    ->find();
                if (NULL === $charactersPoints) {
                    $charactersPoints = new CharactersPoints();
                    $charactersPoints->dialog_id = $dialog->id;
                    $charactersPoints->point_id = $point->id;
                }

                $charactersPoints->add_value = $score;
                $charactersPoints->import_id = $this->import_id;

                $charactersPoints->save();

                $importedRows++;
            }
        }

        // delete old unused data {
        CharactersPoints::model()->deleteAll(
            'import_id <> :import_id OR import_id IS NULL',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_characters_points' => $importedRows,
            'errors' => false,
        );
    }

    /**
     * @param string $code
     * @return integer | string
     */
    private function getNextEventId($code)
    {
        $event = EventSample::model()->byCode($code)->find();
        if (null === $event) {
            return null;
        } else {
            return $event->id;
        }
    }

    /**
     *
     */
    public function importEventSamples()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        $importedRows = 0;
        $this->importedEvents = [];

        // Events from dialogs {
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {

            $code = $this->getCellValue($sheet, 'Event_code', $i);

            if ($code === null)
                continue;

            if ($code === '-' || $code === '') {
                continue;
            }
            if (EventSample::model()->countByAttributes(['code' => $code, 'import_id' => $this->import_id])) {
                continue;
            }

            $this->importedEvents[] = $code;

            $event = EventSample::model()->byCode($code)->find();
            if (!$event) {
                $event = new EventSample(); // Создаем событие
                $event->code = $code;
            }

            $event->title = $this->getCellValue($sheet, 'Наименование события', $i);
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->trigger_time = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Начало, время', $i), 'hh:mm:ss');
            $event->import_id = $this->import_id;

            if ($event->validate()) {
                $event->save();
            } else {
                throw new CException($event->getErrors());
            }

            $importedRows++;
        }
        // Events from dialogs }

        // Create crutch events (Hello, Sergey) {
        $event = EventSample::model()->byCode('T')->find();
        if (!$event) {
            $event = new EventSample(); // Создаем событие
            $event->code = 'T';
        }

        $event->title = 'Конечное событие';
        $event->on_ignore_result = 7; // ничего
        $event->on_hold_logic = 1; // ничего
        $event->trigger_time = 0;
        $event->import_id = $this->import_id;
        $event->save();
        // }

        $sheet = $excel->getSheetByName('to-do-list');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $planCode = $this->getCellValue($sheet, 'Plan_code', $i);
            $event = EventSample::model()->byCode($planCode)->find();
            if (!$event) {
                $event = new EventSample(); // Создаем событие
            }
            $event->code = $planCode;
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->import_id = $this->import_id;
            $event->save();
        }

        // delete old unused data {
        EventSample::model()->deleteAll(
            'import_id <> :import_id OR import_id IS NULL',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_documents' => $importedRows,
            'errors' => false,
        );
    }

    /**
     * Get unique import ID
     *
     * @return string
     */
    protected function getImportUUID()
    {
        return uniqid();
    }

    private function importActivityEfficiencyConditions()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Activities');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            // try to find exists entity {
            $activityEfficiencyCondition = ActivityEfficiencyCondition::model()
                ->byActivityId($this->getCellValue($sheet, 'Activity_code', $i))
                ->byType($this->getCellValue($sheet, 'Result_type', $i))
                ->byResultCode($this->getCellValue($sheet, 'Result_code', $i))
                ->find();
            // try to find exists entity }

            // create entity if not exists {
            if (null === $activityEfficiencyCondition) {
                $activityEfficiencyCondition = new ActivityEfficiencyCondition();
                $activityEfficiencyCondition->activity_id = $this->getCellValue($sheet, 'Activity_code', $i);
                $activityEfficiencyCondition->type = $this->getCellValue($sheet, 'Result_type', $i);
                $activityEfficiencyCondition->result_code = $this->getCellValue($sheet, 'Result_code', $i);
            }
            // create entity if not exists }

            // update data {
            $activityEfficiencyCondition->operation = $this->getCellValue($sheet, 'Result_operation', $i);
            $activityEfficiencyCondition->efficiency_value = $this->getCellValue($sheet, 'All_Result_value', $i);
            $activityEfficiencyCondition->fail_less_coeficient = $this->getCellValue($sheet, 'Fail_Less_Coef', $i);
            $activityEfficiencyCondition->import_id = $this->import_id;
            // update data }

            // save
            $activityEfficiencyCondition->save();

            $importedRows++;
        }

        // delete old unused data {
        ActivityEfficiencyCondition::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'imported_activityEfficiencyConditions' => $importedRows,
            'errors' => false,
        );
    }

    /**
     * @return PHPExcel_Reader_Excel2003XML
     */
    private function getReader()
    {
        PHPExcel_Settings::setCacheStorageMethod($this->cache_method);

        if (!isset($this->reader)) {
            $this->reader = PHPExcel_IOFactory::createReader('Excel2007');

            // prevent read string "11:00" like "0.45833333333333" even by getValue()
            $this->reader->setReadDataOnly(true);
        }

        return $this->reader;
    }

    private function getExcel()
    {
        if (!isset($this->excel)) {
            $this->excel = $this->getReader()->load($this->filename);
        }
        return $this->excel;
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param int $row
     * @return void
     */
    private function setColumnNumbersByNames($sheet, $row = 1)
    {
        for ($i = 0; ; $i++) {
            $row_title = $sheet->getCellByColumnAndRow($i, $row)->getValue();
            if (null !== $row_title) {
                $this->columnNoByName[$row_title] = $i;
            } else {
                return;
            }
        }
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param string $columnName
     * @param PHPExcel_Worksheet_RowIterator $i
     * @param int $increment
     * @return mixed
     */
    private function getCellValue($sheet, $columnName, $i, $increment = 0)
    {
        return $sheet->getCellByColumnAndRow(
            $this->columnNoByName[$columnName] + $increment,
            $i->key()
        )->setDataType(PHPExcel_Cell_DataType::TYPE_STRING)->getCalculatedValue();
    }

    /**
     * Import activity
     *
     *
     * @throws Exception
     * @return array
     */
    public function importActivity()
    {
        echo __METHOD__ . "\n";

        $activity_types = array(
            'Documents_leg' => 'document_id',
            'Manual_dial_leg' => 'dialog_id',
            'System_dial_leg' => 'dialog_id',
            'Inbox_leg' => 'mail_id',
            'Outbox_leg' => 'mail_id',
            'Window' => 'window_id'
        );

        $sheet = $this->getExcel()->getSheetByName('Leg_actions');

        // save colums numbers by column titles 
        $this->setColumnNumbersByNames($sheet);

        $activities = array();
        $activity_actions = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            // get Leg_type value
            $leg_type = $sheet->getCellByColumnAndRow($this->columnNoByName['Leg_type'], $i->key())->getValue();

            // we haven`t skype agent jet
            if ($leg_type === 'Skype_leg') {
                continue;
            }

            // get Activity code
            $activityCode = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_code'], $i->key())->getValue();
            if ($activityCode == '') {
                break;
            }

            //try to find exest activity in DB
            $activity = Activity::model()->findByPk($activityCode);

            // create Activity 
            if ($activity === null) {
                $activity = new Activity();
                $activity->id = $activityCode;
            }

            // update activities counter
            $activities[$activity->id] = true;

            // update activity values {
            $activity->parent = $sheet->getCellByColumnAndRow($this->columnNoByName['Parent'], $i->key())->getValue();
            $activity->grandparent = $sheet->getCellByColumnAndRow($this->columnNoByName['Grand parent'], $i->key())->getValue();
            $activity->name = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_name'], $i->key())->getValue();
            $activity->numeric_id = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_id'], $i->key())->getValue();
            $activity->type = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_type'], $i->key())->getValue();

            $category = $sheet->getCellByColumnAndRow($this->columnNoByName['Категория'], $i->key())->getValue();
            $activity->category_id = ($category === '-' ? null : $category);

            $activity->import_id = $this->import_id;
            if (false === $activity->validate()) {
                throw new Exception(print_r($activity->getErrors(), true));
            }
            $activity->save();
            // update activity values }

            // 
            $type = $activity_types[$leg_type];
            $xls_act_value = $sheet->getCellByColumnAndRow($this->columnNoByName['Leg_action'], $i->key())->getValue();
            # Converting XLS codes to our
            if ($xls_act_value === '-') {
                $values = array();
            } else if ($type === 'dialog_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear yet
                    $values = Replica::model()->findAll();
                } else if ($xls_act_value === 'phone talk') {
                    $values = [null];
                } else {
                    $dialogs = Replica::model()->findAllByAttributes(array('code' => $xls_act_value));
                    if (count($dialogs) === 0) {
                        assert($dialogs, 'No such dialog: "' . $xls_act_value . '"');
                    }
                    $values = $dialogs;
                }
            } else if ($type === 'mail_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear yet
                    $values = MailTemplateModel::model()->findAll();
                } else if ($xls_act_value === 'incorrect_sent' or $xls_act_value === 'not_sent') {
                    $values = [null];
                } else {
                    $mail = MailTemplateModel::model()->findByAttributes(array('code' => $xls_act_value));
                    if ($mail === null) {
                        throw new Exception('No such mail: ' . $xls_act_value);
                    }
                    $values = array($mail);
                }
            } else if ($type === 'document_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear yet
                    $values = DocumentTemplate::model()->findAll();
                } else {
                    $document = DocumentTemplate::model()->findByAttributes(array('code' => $xls_act_value));
                    assert($document);
                    $values = array($document);
                }
            } else if ($type === 'window_id') {
                if ($xls_act_value === 'all') {
                    $values = Window::model()->findAll();
                } else {
                    $window = Window::model()->findByAttributes(array('subtype' => $xls_act_value));
                    assert($window);
                    $values = array($window);
                }
            } else {
                throw new Exception('Can not handle type:' . $type);
            }

            // update relation Activiti to Document, Replica replic ro Email {
            foreach ($values as $value) {
                /** @var ActivityAction $activityAction */
                $activityAction = ActivityAction::model()->findByAttributes(array(
                    'activity_id' => $activity->primaryKey,
                    $type => ($value !== null ? $value->primaryKey : null)
                ));
                if ($activityAction === null) {
                    $activityAction = new ActivityAction();
                }
                $activityAction->import_id = $this->import_id;
                $activityAction->activity_id = $activity->id;
                $activityAction->leg_type = $leg_type;
                $activityAction->is_keep_last_category =
                    $sheet->getCellByColumnAndRow($this->columnNoByName['Keep last category'], $i->key())->getValue();
                $activityAction->$type = ($value !== null ? $value->primaryKey : null);
                if (!$activityAction->validate()) {
                    $this->errors = $activityAction->getErrors();
                    throw new Exception(print_r($this->errors, true));
                }
                $activityAction->save();
            }
            // update relation Activity to Document, Replica replic ro Email }

            $activity_actions++;
        }

        // delete old unused data {
        ActivityAction::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));
        Activity::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'activity_actions' => $activity_actions,
            'errors' => false,
            'activities' => count($activities)
        );
    }

    public function importActivityParentEnding()
    {
        echo __METHOD__ . "\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Parent_ending');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $types = [
            'id_записи' => 'dialog_id',
            'outbox' => 'mail_id',
            'inbox' => 'mail_id'
        ];

        $updatedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $parentCode = $this->getCellValue($sheet, 'Parent', $i);
            $endType = $this->getCellValue($sheet, 'Parent_end_type', $i);
            $endCode = $this->getCellValue($sheet, 'Parent_end_code', $i);

            if ($endType == 'id_записи') {
                $entity = Replica::model()->byExcelId($endCode)->find();
            } elseif ($endType == 'outbox' || $endType == 'inbox') {
                $entity = MailTemplateModel::model()->byCode($endCode)->find();
            }

            if (isset($entity)) {
                $parentActivity = ActivityParent::model()->findByAttributes([
                    'parent_code' => $parentCode,
                    $types[$endType] => $entity->id
                ]);

                if (empty($parentActivity)) {
                    $parentActivity = new ActivityParent();
                }

                $parentActivity->parent_code = $parentCode;
                $parentActivity->import_id = $this->import_id;
                $parentActivity->{$types[$endType]} = $entity->id;
                $parentActivity->save();

                $updatedRows++;
            }
        }

        // delete old unused data {
        ActivityParent::model()->deleteAll(
            'import_id <> :import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'updated_activityActions' => $updatedRows,
            'errors' => false,
        );
    }

    public function importAssessmentRules()
    {
        echo __METHOD__ . "\n";

        $reader = $this->getReader();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Result_rules');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $types = [
            'id_записи' => 'dialog_id',
            'outbox' => 'mail_id',
            'inbox' => 'mail_id'
        ];

        $rules = 0;
        $conditions = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $ruleId = $this->getCellValue($sheet, 'Rule_id', $i);
            $type = $this->getCellValue($sheet, 'Result_type', $i);
            $code = $this->getCellValue($sheet, 'Result_code', $i);

            if (empty($ruleId)) {
                break;
            }

            $rule = AssessmentRule::model()->findByPk($ruleId);
            if (empty($rule)) {
                $rule = new AssessmentRule();
                $rule->id = $ruleId;
            }

            $rule->activity_id = $this->getCellValue($sheet, 'Activity_code', $i);
            $rule->operation = $this->getCellValue($sheet, 'Result_operation', $i);
            $rule->value = $this->getCellValue($sheet, 'All_Result_value', $i);
            $rule->import_id = $this->import_id;

            $rule->save();
            $rules++;

            if ($type == 'id_записи') {
                $entity = Replica::model()->byExcelId($code)->find();
            } elseif ($type == 'outbox' || $type == 'inbox') {
                $entity = MailTemplateModel::model()->byCode($code)->find();
            } else {
                $entity = null;
            }

            if (isset($entity)) {
                $condition = AssessmentRuleCondition::model()->findByAttributes([
                    'assessment_rule_id' => $rule->id,
                    $types[$type] => $entity->id
                ]);

                if (empty($condition)) {
                    $condition = new AssessmentRuleCondition();
                }

                $condition->assessment_rule_id = $rule->id;
                $condition->{$types[$type]} = $entity->id;
                $condition->import_id = $this->import_id;

                $condition->save();
                $conditions++;
            }
        }

        // delete old unused data {
        AssessmentRuleCondition::model()->deleteAll(
            'import_id <> :import_id',
            array('import_id' => $this->import_id)
        );
        AssessmentRule::model()->deleteAll(
            'import_id <> :import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return array(
            'assessment_rules' => $rules,
            'assessment_rule_conditions' => $conditions,
            'errors' => false,
        );
    }

    /**
     * Only must to use functions. Has correct import order
     */
    public function importAll()
    {
        $result = [];
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $result['characters'] = $this->importCharacters();
            $result['learning_goals'] = $this->importLearningGoals();
            $result['characters_points_titles'] = $this->importCharactersPointsTitles();
            $result['flags'] = $this->importFlags();
            $result['dialog'] = $this->importDialogReplicas();
            $result['my_documents'] = $this->importMyDocuments();
            $result['character_points'] = $this->importDialogPoints();
            $result['constructor'] = $this->importMailConstructor();
            $result['email_subjects'] = $this->importEmailSubjects();
            $result['emails'] = $this->importEmails();
            $result['mail_attaches'] = $this->importMailAttaches();
            $result['mail_events'] = $this->importMailEvents();
            $result['tasks'] = $this->importTasks();
            $result['mail_tasks'] = $this->importMailTasks();
            $result['event_samples'] = $this->importEventSamples();
            $result['activity'] = $this->importActivity();
            $result['activity_parent_ending'] = $this->importActivityParentEnding();
            $result['flag_rules'] = $this->importFlagsRules();
            $result['assessment_rules'] = $this->importAssessmentRules();

            $transaction->commit();

        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
        return $result;
    }


    public function importFlagsRules() {
        echo __METHOD__."\n";

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Flags');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedFlagToRunMailRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('mail' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            /** @var $emailEvent EventSample */
            $emailEvent = EventSample::model()->findByAttributes([
                'code' => $this->getCellValue($sheet, 'Run_code', $i)
            ]);

            if (NULL === $emailEvent) {
                throw new Exception('Can`t find event sample for email '.$this->getCellValue($sheet, 'Run_code', $i));
            }

            // we run, immediatly after flag was switched, email without trigger time only
            if ('00:00:00' == $emailEvent->trigger_time) {

                // try to find exists entity {
                $mailFlag = FlagRunMail::model()->findByAttributes([
                    'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i),
                    'mail_code' => $this->getCellValue($sheet, 'Run_code', $i),
                ]);
                // try to find exists entity }

                // create entity if not exists {
                if (null === $mailFlag) {
                    $mailFlag = new FlagRunMail();
                }
                $mailFlag->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $mailFlag->mail_code = $this->getCellValue($sheet, 'Run_code', $i);
                $mailFlag->import_id = $this->import_id;
                $mailFlag->save();
                // create entity if not exists }

                $importedFlagToRunMailRows++;
            }

            // Flag bloks mail alvays {
            $mailTemplate = MailTemplateModel::model()->findByAttributes(['code' => $this->getCellValue($sheet, 'Run_code', $i)]);
            $mailFlag = FlagBlockMail::model()->findByAttributes([
                'mail_template_id' => $mailTemplate->primaryKey,
                'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i),
            ]);
            if (null === $mailFlag) {
                $mailFlag = new FlagBlockMail();
            }
            $mailFlag->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            $mailFlag->mail_template_id = $mailTemplate->primaryKey;
            $mailFlag->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $mailFlag->import_id = $this->import_id;
            $mailFlag->save();
            // Flag bloks mail alvays }
        }

        $importedFlagBlockReplica = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('replica' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // try to find exists entity {
            $flagBlockReplica = FlagBlockReplica::model()->findByAttributes([
                'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i),
                'replica_id' => $this->getCellValue($sheet, 'Run_code', $i),
            ]);
            // try to find exists entity }

            // create entity if not exists {
            if (null === $flagBlockReplica) {
                $flagBlockReplica = new FlagBlockReplica();
                $flagBlockReplica->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $flagBlockReplica->replica_id = (int)$this->getCellValue($sheet, 'Run_code', $i);
            }
            // create entity if not exists }

            $flagBlockReplica->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagBlockReplica->import_id = $this->import_id;

            $flagBlockReplica->save();

            $importedFlagBlockReplica++;
        }

        // for Dialogs {
        $importedFlagBlockDialog = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('dialog' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // try to find exists entity {
            $flagBlockDialog = FlagBlockDialog::model()->findByAttributes([
                'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i),
                'dialog_code' => $this->getCellValue($sheet, 'Run_code', $i),
            ]);
            // try to find exists entity }

            // create entity if not exists {
            if (null === $flagBlockDialog) {
                $flagBlockDialog = new FlagBlockDialog();
                $flagBlockDialog->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $flagBlockDialog->dialog_code = $this->getCellValue($sheet, 'Run_code', $i);
            }
            // create entity if not exists }

            $flagBlockDialog->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagBlockDialog->import_id = $this->import_id;

            $flagBlockDialog->save();

            $importedFlagBlockDialog++;
        }
        // for Dialogs }

        // delete old unused data {
        FlagRunMail::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        FlagBlockMail::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );

        FlagBlockReplica::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );

        FlagBlockDialog::model()->deleteAll(
            'import_id<>:import_id',
            array('import_id' => $this->import_id)
        );
        // delete old unused data }

        echo __METHOD__ . " end \n";

        return [
            'imported_Flag_to_run_mail'   => $importedFlagToRunMailRows,
            'imported_Flag_block_replica' => $importedFlagBlockReplica,
            'imported_Flag_block_dialog'  => $importedFlagBlockDialog,
            'errors' => false,
        ];
    }
}

