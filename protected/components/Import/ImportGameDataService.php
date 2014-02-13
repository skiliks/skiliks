<?php

/**
 * @author slavka
 * @property Scenario $scenario
 * @property string scenario_slug
 */
class ImportGameDataService
{
    private $filename = null;

    private $import_id = null;

    private $errors = null;

    private $cache_method = null;

    private $columnNoByName = [];

    private $importedEvents = [];

    private $environment = 'console';

    private $dbLogInstance = null;

    public function __construct($type, $environment = 'console', $dbLogInstance = null)
    {
        // init data from arguments:
        $this->scenario_slug = $type;
        $this->environment = $environment;
        $this->dbLogInstance = $dbLogInstance;

        // find .xlsx file
        $files = glob(__DIR__ . "/../../../media/scenario_$type*.xlsx");
        $files = array_combine($files, array_map("filemtime", $files));
        arsort($files);

        // other{
        $this->filename = key($files);

        $this->logging("Import from file {$this->filename}.");

        $this->import_id = $this->getImportUUID();
        $this->cache_method = null;

        $this->setScenario();
        // other}
    }

    public function setFilename($name)
    {
        $name = str_replace('..', '', $name);
        $name = str_replace('/', '', $name);
        $name = str_replace('\\', '', $name);

        $this->filename = __DIR__ . '/../../../media/' . $name;
    }
    public function getFilename()
    {
        return $this->filename;
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
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Faces_new');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'id_персонажа', $i)) {
                continue;
            }

            // try to find exists entity 
            $character = $this->scenario
                ->getCharacter(['code' => $this->getCellValue($sheet, 'id_персонажа', $i)]);

            // create entity if not exists {
            if (null === $character) {
                $character = new Character();
            }
            // create entity if not exists }

            // update data {
            $character->code = $this->getCellValue($sheet, 'id_персонажа', $i);
            $character->title = $this->getCellValue($sheet, 'Должность short', $i);
            $character->fio = $this->getCellValue($sheet, 'ФИО - short', $i);
            $character->email = $this->getCellValue($sheet, 'e-mail', $i);
            $character->skype = $this->getCellValue($sheet, 'skype', $i);
            $character->phone = $this->getCellValue($sheet, 'телефон', $i);
            $character->import_id = $this->import_id;
            $character->scenario_id = $this->scenario->primaryKey;
            $character->sex = $this->getCellValue($sheet, 'Sex', $i);

            // save
            $character->save();

            $importedRows++;
        }

        // delete old unused data {
        Character::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_characters' => $importedRows,
            'errors'              => false,
        );
    }

    public function importLearningAreas()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Номер области обучения', $i)) {
                continue;
            }
            if("нет" === $this->getCellValue($sheet, 'Оценивается в Релиз 1 (да/нет)', $i)){
                continue;
            }
            // try to find exists entity
            $learningArea = $this->scenario->getLearningArea([
                'code' => $this->getCellValue($sheet, 'Номер области обучения', $i)
            ]);

            // create entity if not exists {
            if (null === $learningArea) {
                $learningArea = new LearningArea();
                $learningArea->code = $this->getCellValue($sheet, 'Номер области обучения', $i);
            }
            // create entity if not exists }

            // update data {
            $learningArea->title = $this->getCellValue($sheet, 'Наименование области обучения', $i);
            $learningArea->import_id = $this->import_id;
            $learningArea->scenario_id = $this->scenario->primaryKey;

            // save
            $learningArea->save();

            $importedRows++;

        }

        // delete old unused data {
        LearningArea::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_learning_areas' => $importedRows,
            'errors'                  => false,
        );
    }

    /**
     * @return mixed array
     */
    public function importLearningGoals()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);
        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Номер цели обучения', $i)) {
                continue;
            }

            if("нет" === $this->getCellValue($sheet, 'Оценивается в Релиз 1 (да/нет)', $i)){
                continue;
            }

            $learningAreaCode = $this->getCellValue($sheet, 'Номер области обучения', $i) ? : null;

            $learningGoalGroupText = $this->getCellValue($sheet, 'Learning_goal_group_text', $i) ? : null;
            $learningGoalGroupCode = $this->getCellValue($sheet, 'Learning_goal_group_id', $i) ? : null;

            if(!empty($learningGoalGroupCode) && !empty($learningGoalGroupText)){
                $learningGoalGroup = LearningGoalGroup::model()->findByAttributes([
                    'code'=>$learningGoalGroupCode,
                    'scenario_id' => $this->scenario->primaryKey
                ]);

                if(null === $learningGoalGroup) {
                    $learningGoalGroup = new LearningGoalGroup();
                    $learningGoalGroup->code = $learningGoalGroupCode;
                }
                $learningGoalGroup->learning_area_code = $learningAreaCode ? $this->scenario->getLearningArea(['code' => $learningAreaCode])->code : null;
                $learningGoalGroup->learning_area_id = $learningAreaCode ? $this->scenario->getLearningArea(['code' => $learningAreaCode])->id : null;
                $learningGoalGroup->title = $learningGoalGroupText;
                $learningGoalGroup->import_id = $this->import_id;
                $learningGoalGroup->scenario_id = $this->scenario->primaryKey;
                $learningGoalGroup->save(false);
            } else {
                $learningGoalGroup = null;
            }

            // try to find exists entity
            $learningGoal = LearningGoal::model()->findByAttributes([
                'code' => $this->getCellValue($sheet, 'Номер цели обучения', $i),
                'scenario_id' => $this->scenario->primaryKey
            ]);

            // create entity if not exists {
            if (null === $learningGoal) {
                $learningGoal = new LearningGoal();
                $learningGoal->code = $this->getCellValue($sheet, 'Номер цели обучения', $i);
            }
            // create entity if not exists }

            // update data {
            $learningGoal->title = $this->getCellValue($sheet, 'Наименование цели обучения', $i);
            $learningGoal->learning_goal_group_id = ($learningGoalGroup === null) ? 0 : $learningGoalGroup->id;
            $learningGoal->learning_area_code = $learningAreaCode ? $this->scenario->getLearningArea(['code' => $learningAreaCode])->getPrimaryKey() : null;
            $learningGoal->import_id = $this->import_id;
            $learningGoal->scenario_id = $this->scenario->primaryKey;

            // save
            $learningGoal->save();

            $importedRows++;

        }

        // delete old unused data {
        LearningGoal::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        LearningGoalGroup::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_learning_goals' => $importedRows,
            'errors'                  => false,
        );
    }

    public function importMailConstructor()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Constructor');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        $endCol = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());
        for ($col = 0; $col < $endCol; $col++) {
            $constructorCode = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if ($constructorCode === null) {
                continue;
            }
            $constructor = MailConstructor::model()->findByAttributes(['code' => $constructorCode, 'scenario_id' => $this->scenario->primaryKey]);
            if ($constructor === null) {
                $constructor = new MailConstructor();
            }

            $constructor->code = $constructorCode;
            $constructor->import_id = $this->import_id;
            $constructor->scenario_id = $this->scenario->primaryKey;
            $constructor->save();
            $column_number = 1;
            for ($row = 2; $row < $sheet->getHighestDataRow(); $row++) {
                $phraseValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                if (empty($phraseValue)) {
                    continue;
                }
                if($phraseValue === '****'){
                    $column_number++;
                    continue;
                }
                // ##### - constructor terminator
                if($phraseValue === '#####'){
                    break;
                }

                $phrase = $this->scenario->getMailPhrase([
                    'constructor_id' => $constructor->getPrimaryKey(),
                    'name' => $phraseValue,
                    'column_number' => $column_number
                ]);

                if ($phrase === null) {
                    $phrase = new MailPhrase();
                }
                $phrase->constructor_id = $constructor->getPrimaryKey();
                $phrase->name = $phraseValue;
                $phrase->import_id = $this->import_id;
                $phrase->scenario_id = $this->scenario->primaryKey;
                $phrase->column_number = $column_number;
                $phrase->save();
                $importedRows++;
            }
        }

        // Manual add punctuation signs
        $constructor = MailConstructor::model()
            ->findByAttributes(['code' => 'SYS',
            'scenario_id' => $this->scenario->primaryKey]);

        if ($constructor === null) {
            $constructor = new MailConstructor();
        }
        $constructor->code = 'SYS';
        $constructor->import_id = $this->import_id;
        $constructor->scenario_id = $this->scenario->primaryKey;
        $constructor->save();

        $signs = ['.', ',', ':', '"', '-', ';'];
        foreach ($signs as $sign) {

            $phrase = $this->scenario->getMailPhrase([
                'constructor_id' => $constructor->getPrimaryKey(),
                'name' => $sign]);

            if ($phrase === null) {
                $phrase = new MailPhrase();
            }
            //$phrase->code = 'SYS'; что это за код???? как это работало? *_*
            $phrase->name = $sign;
            $phrase->import_id = $this->import_id;
            $phrase->constructor_id = $constructor->getPrimaryKey();
            $phrase->scenario_id = $this->scenario->primaryKey;
            $phrase->save();
        }

        $constructor = MailConstructor::model()
            ->findByAttributes(['code' => 'TXT',
                'scenario_id' => $this->scenario->primaryKey]);

        if ($constructor === null) {
            $constructor = new MailConstructor();
            $constructor->code = 'TXT';
        }
        $constructor->import_id = $this->import_id;
        $constructor->scenario_id = $this->scenario->primaryKey;
        $constructor->save();

        // delete old unused data {
        MailPhrase::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        MailConstructor::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }


        $this->logEnd();
        return ['ok' => 1];
    }

    /**
     *
     */
    public function  importHeroBehaviours()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $assessment_group = $this->scenario->getAssessmentGroups([]);

        $groups = [];
        foreach($assessment_group as $group){
            $groups[$group->id] = $group->name;
        }

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Номер требуемого поведения', $i)) {
                continue;
            }
            if("нет" === $this->getCellValue($sheet, 'Оценивается в Релиз 1 (да/нет)', $i)){
                continue;
            }
            // try to find exists entity 
            $charactersPointsTitle = HeroBehaviour::model()
                ->findByAttributes([
                    'code' => $this->getCellValue($sheet, 'Номер требуемого поведения', $i),
                    'scenario_id' => $this->scenario->primaryKey
                ]);

            // create entity if not exists {
            if (null === $charactersPointsTitle) {
                $charactersPointsTitle = new HeroBehaviour();
                $charactersPointsTitle->code = $this->getCellValue($sheet, 'Номер требуемого поведения', $i);
            }
            // create entity if not exists }

            $group_id = array_search($this->getCellValue($sheet, 'Assessment group', $i), $groups);
            if(false !== $group_id){
                $charactersPointsTitle->group_id = $group_id;
            }


            // update data {
            $charactersPointsTitle->title = $this->getCellValue($sheet, 'Наименование требуемого поведения', $i);
            $charactersPointsTitle->learning_goal_id = $this->scenario->getLearningGoal(['code' => $this->getCellValue($sheet, 'Номер цели обучения', $i)])->getPrimaryKey();
            $charactersPointsTitle->scale = $this->getCellValue($sheet, 'Единая шкала', $i); // Makr
            $charactersPointsTitle->type_scale = HeroBehaviour::getScaleId($this->getCellValue($sheet, 'Тип шкалы', $i));
            $charactersPointsTitle->import_id = $this->import_id;
            $charactersPointsTitle->scenario_id = $this->scenario->primaryKey;

            // save
            $charactersPointsTitle->save();

            $importedRows++;

        }

        // delete old unused data {
        HeroBehaviour::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_character_point_titles' => $importedRows,
            'errors'                          => false,
        );
    }

    public function importAssessmentGroup() {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Forma_1');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Assessment group', $i)) {
                continue;
            }
            if("нет" === $this->getCellValue($sheet, 'Оценивается в Релиз 1 (да/нет)', $i)){
                continue;
            }
            // try to find exists entity
            $assessment_group = $this->scenario
                ->getAssessmentGroup(['name'=>$this->getCellValue($sheet, 'Assessment group', $i)]);

            // create entity if not exists {
            if (null === $assessment_group) {
                $group = $this->getCellValue($sheet, 'Assessment group', $i);
                if( strlen($group) <= 255 ){
                    $assessment_group = new AssessmentGroup();
                    $assessment_group->name = $group;
                }else{
                    throw new Exception("Mysql VARCHAR 255 !== ${group} ".strlen($group));
                }

            }
            // create entity if not exists }

            // update data {
            $assessment_group->import_id = $this->import_id;
            $assessment_group->scenario_id = $this->scenario->getPrimaryKey();

            // save
            $assessment_group->save();

            $importedRows++;

        }

        // delete old unused data {
        AssessmentGroup::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->getPrimaryKey())
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_assessment_group' => $importedRows,
            'errors' => false,
        );
    }

    public function importStressRules()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Stress');

        // load sheet }
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        $this->setColumnNumbersByNames($sheet);

        $types = [
            'id_записи' => 'replica_id',
            'outbox' => 'mail_id',
            'inbox' => 'mail_id'
        ];

        $rules = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $ruleId = $this->getCellValue($sheet, 'Stress_rule_id', $i);
            $type = $this->getCellValue($sheet, 'Stress_type', $i);
            $code = $this->getCellValue($sheet, 'Stress_code', $i);

            if (empty($ruleId)) {
                break;
            }

            if ($type == 'id_записи') {
                $entity = $this->scenario->getReplica(['excel_id' => $code]);
            } elseif ($type == 'outbox' || $type == 'inbox') {
                $entity = $this->scenario->getMailTemplate(['code' => $code]);
            } else {
                $entity = null;
            }

            if (isset($entity)) {
                $rule = $this->scenario->getStressRule(['code' => $ruleId]);
                if (empty($rule)) {
                    $rule = new StressRule();
                    $rule->code = $ruleId;
                }

                $rule->{$types[$type]} = $entity->id;
                $rule->value = $this->getCellValue($sheet, 'Stress_value', $i);
                $rule->scenario_id = $this->scenario->getPrimaryKey();
                $rule->import_id = $this->import_id;

                $rule->save();
                $rules++;
            }
        }

        // delete old unused data {
        StressRule::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->getPrimaryKey())
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'stress_rules' => $rules,
            'errors' => false,
        );
    }

    /**
     * @return array
     */
    public function importActivityParentAvailability()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Parent_params');

        // load sheet }
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        $this->setColumnNumbersByNames($sheet);

        $time_index = "Время доступности к запуску";

        $counter = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Parent', $i);

            $entity = $this->scenario
                ->getActivityParentAvailability(['code' => $code]);

            if (null === $entity) {
                $entity = new ActivityParentAvailability();
                $entity->code = $code;
            }
            $must_present_for_214d = $this->getCellValue($sheet, 'Must_present_for_214d', $i);
            $keep_last_category = $this->getCellValue($sheet, 'Keep last category', $i);
            $entity->category = $this->getCellValue($sheet, 'Категория', $i);
            $entity->is_keep_last_category = ($keep_last_category === 'yes')?LogActivityActionAggregated::KEEP_LAST_CATEGORY_YES:LogActivityActionAggregated::KEEP_LAST_CATEGORY_NO;
            $entity->must_present_for_214d = ($must_present_for_214d === 'must')?ActivityParentAvailability::MUST_PRESENT_FOR_214D_YES:ActivityParentAvailability::MUST_PRESENT_FOR_214D_NO;
            $entity->available_at = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, $time_index, $i), 'hh:mm:ss');
            $entity->import_id = $this->import_id;
            $entity->scenario_id = $this->scenario->getPrimaryKey();
            $entity->save();
            $counter++;
        }

        // delete old unused data {
        ActivityParentAvailability::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id', [
                'import_id' => $this->import_id,
                'scenario_id' => $this->scenario->getPrimaryKey()
        ]);
        // delete old unused data }

        $this->logEnd();

        return array(
            'parent_availability_lines' => $counter,
            'errors'                    => false,
        );
    }

    /**
     * @todo: make code simpler!
     * @return array
     * @throws Exception
     */
    public function importEmails()
    {
        $this->logStart();
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);

        $counter = array(
            'all'        => 0,
            'MY'         => 0,
            'M'          => 0,
            'MSY'        => 0,
            'MS'         => 0,
            'mark-codes' => 0,
            'mark-0'     => 0,
            'mark-1'     => 0,
        );

        $emailIds = array(); // to delete old letters after import
        $emailToCopyIds = array(); // to delete old letter-cope relations after import
        $emailToRecipientIds = array(); // to delete old letter-recipient relations after import
        $emailToPointIds = array(); // to delete old letter-point relations after import
        $emailSubjectsIds = array(); // to delete old letter-"theme" relations after import

        $characters = array();
        $charactersList = $this->scenario->getCharacters([]);

        foreach ($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }

        // загрузим информацию о поинтах
        $pointsTitles = $this->scenario->getHeroBehavours([]);
        $pointsInfo = array();
        foreach ($pointsTitles as $item) {
            $pointsInfo[$item->code] = $item->id;
        }

        // Get all exist system mail_templates to avoid SQL queries againts each request {
        $existsMailTemplate = array();
        foreach ($this->scenario->getMailTemplates([]) as $mailTemplate) {
            $existsMailTemplate[$mailTemplate->code] = $mailTemplate;
        }
        // Get all mail_templates }

        $exists = array();

        $index = 0;
        $pointsCodes = array();

        $START_COL = $this->columnNoByName['Задержка прихода письма'] + 1;
        $END_COL = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
        for ($columnIndex = $START_COL; $columnIndex <= $END_COL; $columnIndex++) {
            $code = $sheet->getCellByColumnAndRow($columnIndex, 2)->getValue();
            if ($code === null || preg_match('/^[^0-9]/', $code)) {
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
                    'text'   => "cant find character by code $fromCode <br/> Line $index",
                );
            }
            $fromId = $characters[$fromCode];

            // tmp
            $copiesArr = array();
            if (strpos($copies, ', ') !== false) {
                $copiesArr = explode(', ', $copies);
            }
            elseif($copies != "-") {
                $copiesArr = [$copies];
            }
            elseif($copies != "-") {
                $copiesArr = [$copies];
            }

            if (strpos($toCode, ', ') !== false) {
                $toCode = explode(', ', $toCode);
            }
            else {
                $toCode = [$toCode];
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

            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }
            $themePrefix = $this->getCellValue($sheet, 'Theme_prefix', $i);
            if ($themePrefix === '-') {
                $themePrefix = null;
            }

            $emailTemplateEntity = $this->scenario->getMailTemplate(['code' => $code]);
            if ($emailTemplateEntity === null) {
                $emailTemplateEntity = new MailTemplate();
                $emailTemplateEntity->code = $code;
            }

            $theme = $this->scenario->getTheme(['theme_code'=>$subject_id]);

            $emailTemplateEntity->group_id = $group;
            $emailTemplateEntity->sender_id = $fromId;
            $emailTemplateEntity->receiver_id = $toId;
            $emailTemplateEntity->theme_id = $theme->id;
            $emailTemplateEntity->mail_prefix = $themePrefix;
            $emailTemplateEntity->message = $message;
            $emailTemplateEntity->sent_at = $sendingDate . ' ' . $sendingTime;
            $emailTemplateEntity->type = $type;
            $emailTemplateEntity->type_of_importance = $typeOfImportance ? : 'none';
            $emailTemplateEntity->import_id = $this->import_id;
            $emailTemplateEntity->scenario_id = $this->scenario->primaryKey;
            $emailTemplateEntity->flag_to_switch = (empty($flag))?null:$flag;

            $emailTemplateEntity->save(false);
            $emailIds[] = $emailTemplateEntity->id;

            // учтем поинты (оценки, marks)
            $columnIndex = $START_COL;
            while ($columnIndex < $END_COL) {
                $value = $sheet->getCellByColumnAndRow($columnIndex, $i->key())->getValue();

                if ($value === null || $value === "") {
                    $columnIndex++;
                    continue;
                }
                $pointCode = $pointsCodes[$columnIndex];
                if (isset($pointsInfo[$pointCode])){
                    $pointId = $pointsInfo[$pointCode];
                    /** @var MailPoint $pointEntity */
                    $pointEntity = $this->scenario->getMailPoint(['mail_id' => $emailTemplateEntity->id, 'point_id'  => $pointId]);
                    if (null === $pointEntity) {
                        $pointEntity = new MailPoint();
                    }
                    $pointEntity->mail_id = $emailTemplateEntity->id;
                    $pointEntity->point_id = $pointId;
                    $pointEntity->add_value = $value;
                    $pointEntity->import_id = $this->import_id;
                    $pointEntity->scenario_id = $this->scenario->primaryKey;
                    $pointEntity->save();

                }else{
                    $columnIndex++;
                    continue;
                }

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
                // todo: use scenario_id!
                $mrt = MailTemplateRecipient::model()->findByAttributes([
                        'mail_id'     => $emailTemplateEntity->id,
                        'receiver_id' => $receiverId
                    ]);
                if (null === $mrt) {
                    $mrt = new MailTemplateRecipient();
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
                        'text'   => "cant find character by code $characterCode",
                    );
                }
                $characterId = $characters[$characterCode];

                // todo: use scenario_id!
                $mct = MailTemplateCopy::model()
                    ->findByAttributes([
                        'mail_id'     => $emailTemplateEntity->id,
                        'receiver_id' => $characterId,
                        'scenario_id' => $this->scenario->getPrimaryKey(),
                    ]);

                if (null === $mct) {
                    $mct = new MailTemplateCopy();
                    $mct->mail_id = $emailTemplateEntity->id;
                    $mct->receiver_id = $characterId;
                    $mct->scenario_id = $this->scenario->primaryKey;
                    $mct->insert();
                }

                $emailToCopyIds[] = $mct->id;
            }

            $counter['all']++;
        }

        // remove old entities {
        // copy relations {
        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('id', $emailToCopyIds);
        $criteria->compare('scenario_id', $this->scenario->getPrimaryKey());
        $emailCopyEntities = MailTemplateCopy::model()
            ->findAll($criteria);

        foreach ($emailCopyEntities as $entity) {
            $entity->delete();
        }

        unset($entity);
        // copy relations }

        // recipient relations {
        /** @var MailRecipient[] $emailRecipientEntities */
        // todo: use scenario_id!
        $emailRecipientEntities = MailRecipient::model()
            ->findAll(" id NOT IN (:ids) ", ['ids' => implode(',', $emailToRecipientIds)]);

        foreach ($emailRecipientEntities as $entity) {
            $entity->delete();
        }
        unset($entity);
        // recipient relations }

        // points relations {
        if (0 !== count($emailToPointIds)) {
            $criteria = new CDbCriteria();
            $criteria->addNotInCondition('id', $emailToPointIds);
            $emailPointsEntities = $this->scenario->getMailPoints($criteria);

            foreach ($emailPointsEntities as $entity) {
                $entity->delete();
            }
            unset($entity);
        }
        // points relations }



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
        MailTemplate::model()->deleteAll('import_id<>:import_id AND scenario_id = :scenario_id', array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey));

        $this->logEnd();

        return array(
            'status' => true,
            'text'   => $html,
        );
    }

    /**
     * Requires characters, emails (F-S-C)
     *
     * @throws CException
     * @throws Exception
     * @return array
     */
    public function importAllThemes()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL Themes');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }

        $characters = array();
        $charactersList = $this->scenario->getCharacters([]);
        foreach ($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }

        $html = '';
        $themes_unique = []; //Уникальные темы без повторения
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {

            $theme_usage = $this->getCellValue($sheet, 'Theme_usage', $i); //phone , mail_outbox, mail_inbox, mail_
            //Импортируем только темы для телефона и исходящых писем
            if (false === in_array($theme_usage, ['phone', 'mail_outbox', 'mail_outbox_old', 'mail_inbox'])) {
                continue;
            }
            $theme_code = $this->getCellValue($sheet, 'Original_Theme_id', $i); // A
            if (null === $theme_code) {
                continue;
            }

            //Если такая тема уже есть то не добавляем её ещё раз
            if (false === isset($themes_unique[$theme_code])) {

                // Theme {
                $theme = $this->scenario->getTheme(['theme_code'=>$theme_code]);
                if($theme === null) {
                    $theme = new Theme();
                    $theme->theme_code = $theme_code;
                }
                $theme->text = $this->getCellValue($sheet, 'Original_Theme_text', $i);
                $theme->import_id = $this->import_id;
                $theme->scenario_id = $this->scenario->id;
                $theme->save(false);
                $themes_unique[$theme_code] = $theme->id;
                // Theme }
            }

            $theme_id = $themes_unique[$theme_code];
            $character_code = $this->getCellValue($sheet, 'To_code', $i);

            // OutgoingPhoneTheme {
            if ('phone' === $theme_usage) {
                $character_id = $characters[$character_code];
                $phone_theme = $this->scenario->getOutgoingPhoneTheme(['theme_id' => $theme_id, 'character_to_id' => $character_id]);
                if(null === $phone_theme) {
                    $phone_theme = new OutgoingPhoneTheme();
                    $phone_theme->theme_id = $theme_id;
                    $phone_theme->character_to_id = $character_id;
                }
                // Phone dialogue number

                $dialog_code = $this->getCellValue($sheet, 'Phone dialogue number', $i);
                if(empty($dialog_code)){
                    $dialog_code = null;
                }
                // Phone W/R
                $phone_wr = $this->getCellValue($sheet, 'Phone W/R', $i);
                if (empty($phone_wr)) {
                    $phone_wr = null;
                }
                $phone_theme->wr = $phone_wr;
                $phone_theme->dialog_code = $dialog_code;
                $phone_theme->import_id = $this->import_id;
                $phone_theme->scenario_id = $this->scenario->id;
                $phone_theme->save(false);
            }
            // OutgoingPhoneTheme }

            // OutboxMailTheme {
            if ('mail_outbox' === $theme_usage) {

                $character_id = $characters[$character_code];

                $mail_prefix = $this->getCellValue($sheet, 'Theme_prefix', $i);
                if(empty($mail_prefix) || $mail_prefix === '-') {
                    $mail_prefix = null;
                }

                $mail_theme = $this->scenario->getOutboxMailTheme([
                    'theme_id'        => $theme_id,
                    'character_to_id' => $character_id,
                    'mail_prefix'     => $mail_prefix,
                ]);

                if(null === $mail_theme){
                    $mail_theme = new OutboxMailTheme();
                    $mail_theme->theme_id        = $theme_id;
                    $mail_theme->character_to_id = $character_id;
                    $mail_theme->mail_prefix     = $mail_prefix;
                }

                // Mail constructor number
                $mail_constructor_number = $this->getCellValue($sheet, 'Mail constructor number', $i);

                if(empty($mail_constructor_number)){

                    $mail_theme->mail_constructor_id = null;
                } else {
                    $mail_theme->mail_constructor_id = $this->scenario->getMailConstructor(['code'=>$mail_constructor_number])->id;
                }
                // Mail W/R
                $mail_wr = $this->getCellValue($sheet, 'Mail W/R', $i);
                if (empty($mail_wr)) {
                    $mail_wr = null;
                }

                $mail_code = $this->getCellValue($sheet, 'Mail letter number', $i);
                if(empty($mail_code) || $mail_code === 'MS не найдено') {
                    $mail_code = null;
                }
                $mail_theme->mail_code   = $mail_code;
                $mail_theme->wr          = $mail_wr;
                $mail_theme->import_id   = $this->import_id;
                $mail_theme->scenario_id = $this->scenario->id;
                $mail_theme->save(false);
            }
            // OutboxMailTheme }
        }
        // remove all old, unused characterMailThemes after import {
        Theme::model()->deleteAll('import_id<>:import_id AND scenario_id = :scenario_id', array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey));
        OutgoingPhoneTheme::model()->deleteAll('import_id<>:import_id AND scenario_id = :scenario_id', array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey));
        OutboxMailTheme::model()->deleteAll('import_id<>:import_id AND scenario_id = :scenario_id', array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey));

        // SKILIKS-5210 {
        // Сценарий будет обновлён через неделю - я правка тут на 10 минут
        $loshadkin = $this->scenario->getCharacter(['fio' => 'Лошадкин М.']);
        $themeNew = Theme::model()->findByAttributes(['text' => 'Новая тема']);
        $mail_theme = $this->scenario->getOutboxMailTheme([
            'theme_id'        => $themeNew->id,
            'character_to_id' => $loshadkin->id,
            'mail_prefix'     => null,
        ]);
        if (null == $mail_theme) {
            $mail_theme = new OutboxMailTheme();
            $mail_theme->theme_id        = $themeNew->id;
            $mail_theme->character_to_id = $loshadkin->id;
            $mail_theme->wr          = 'w';
            $mail_theme->import_id   = $this->import_id;
            $mail_theme->scenario_id = $this->scenario->id;
            $mail_theme->save(false);
        }
        // SKILIKS-5210 }

        $html .= "Email from characters import finished! <br/>";

        $this->logEnd();

        return array(
            'status' => true,
            'text'   => $html,
        );
    }

    public function importTasks()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('to-do-list');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

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
            $startTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Fixed time', $i), 'hh:mm:ss');

            // Категория
            $category = $this->getCellValue($sheet, 'Категория', $i);
            // Мин.
            $duration = $this->getCellValue($sheet, 'Мин.', $i);

            $task = $this->scenario->getTask(['code' => $code]);
            if (!$task) {
                $task = new Task();
                $task->code = $code;
            }
            $task->title = $name;
            // bug in the content. remove code after 29.04.2012
            $task->start_time = preg_replace('/;/', ':', $startTime) ? : null;
            $task->duration = $duration;
            if ($this->getCellValue($sheet, 'Task time limit type', $i) === 'can\'t be moved') {
                $task->is_cant_be_moved = 1;
            } else {
                $task->is_cant_be_moved = 0;
            }
            $task->start_type = $startType;
            $task->category = $category;
            $task->time_limit_type = $this->getCellValue($sheet, 'Task time limit type', $i);
            $task->fixed_day = $this->getCellValue($sheet, 'Fixed day', $i);

            $task->import_id = $this->import_id;
            $task->scenario_id = $this->scenario->primaryKey;
            $task->save();
        }
        Task::model()->deleteAll('import_id<>:import_id AND scenario_id = :scenario_id', array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey));

        $this->logEnd();

        return array(
            'status' => true,
            'text'   => sprintf('%s tasks have been imported.', Task::model()->count()),
        );
    }

    /**
     * Импорт задач для писем M-T
     */
    public function importMailTasks()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('M-T');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Mail code', $i)) {
                continue;
            }

            $mail = $this->scenario->getMailTemplate(['code' => $this->getCellValue($sheet, 'Mail code', $i)]);

            if (!$mail) {
                break;
            }

            // try to find exists entity
            // todo: use scenario_id!
            $mailTask = MailTask::model()->findByAttributes([
                'mail_id' => $mail->id,
                'name' => $this->getCellValue($sheet, 'Task', $i)
            ]);

            // create entity if not exists {
            if (null === $mailTask) {
                $mailTask = new MailTask();
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
            $mailTask->scenario_id = $this->scenario->primaryKey;

            // save
            $mailTask->save();

            $importedRows++;
        }

        // delete old unused data {
        MailTask::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_documents' => $importedRows,
            'errors'             => false,
        );
    }

    public function importFlags()
    {
        $this->logStart();
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Flags');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Flag_code', $i);
            if ($code === null) {
                continue;
            }
            $flag = $this->scenario->getFlag(['code' => $code]);
            if ($flag === null) {
                $flag = new Flag();
            }
            //$flag->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flag->code = $code;
            $flag->description = $this->getCellValue($sheet, 'Flag_name', $i);
            $flag->delay = $this->getCellValue($sheet, 'Flag_delay', $i);
            $flag->import_id = $this->import_id;
            $flag->scenario_id = $this->scenario->primaryKey;
            $flag->save();
            Flag::model()->deleteAll('import_id <> :import_id AND scenario_id=:scenario_id', ['import_id' => $this->import_id, 'scenario_id' => $this->scenario->getPrimaryKey()]);
        }
        $this->logEnd();
        return [
            'status' => true,
            'text'   => sprintf("imported %d flags", Flag::model()->count())
        ];
    }

    /**
     * Импорт событий из писем
     */
    public function importMailEvents()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

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
            $event = $this->scenario->getEventSample(['code' => $code]);
            if (!$event) {
                $event = new EventSample();
                $event->code = $code;
            }

            $event->on_ignore_result = 7;
            $event->on_hold_logic = 1;
            $event->trigger_time = $sendingTime ? : null;
            $event->import_id = $this->import_id;
            $event->scenario_id = $this->scenario->primaryKey;
            $event->title = '';
            $event->save();
        }

        $this->logEnd();

        return array(
            'status' => true,
            'text'   => sprintf('%s mail events have been imported.', EventSample::model()->count('code LIKE "M%"')),
        );
    }

    public function importMailAttaches()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        $documents = [];
        foreach ($this->scenario->getDocumentTemplates([]) as $document) {
            $documents[$document->code] = $document->id;
        }

        $index = 0;
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);
            $attache = $this->getCellValue($sheet, 'Attachment', $i);

            if ($attache == '' || $attache == '-') continue; // нет аттачей

            $mail = $this->scenario->getMailTemplate(['code' => $code]);
            $fileId = $documents[$attache];

            // todo: use scenario_id!
            $attacheModel = $this->scenario->getMailAttachmentTemplate([
                'mail_id' => $mail->id,
                'file_id' => $fileId
            ]);
            if ($attacheModel === null) {
                $attacheModel = new MailAttachmentTemplate();
            }

            $attacheModel->mail_id = $mail->id;
            $attacheModel->file_id = $fileId;
            $attacheModel->import_id = $this->import_id;
            $attacheModel->scenario_id = $this->scenario->primaryKey;
            $attacheModel->save();

        }

        // delete old unused data {
        MailAttachmentTemplate::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'status' => true,
            'text'   => sprintf('%s mail attaches have been imported.', $index),
        );
    }

    /**
     * Импорт документов
     */
    public function importMyDocuments()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Documents');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 1);

        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'Document_code', $i)) {
                continue;
            }

            // try to find exists entity 
            $document = $this->scenario->getDocumentTemplate(['code' => $this->getCellValue($sheet, 'Document_code', $i)]);

            // create entity if not exists {
            if (null === $document) {
                $document = new DocumentTemplate();
                $document->code = $this->getCellValue($sheet, 'Document_code', $i);
            }
            // create entity if not exists }

            // update data {
            $document->fileName = sprintf('%s.%s', $this->getCellValue($sheet, 'Document_name', $i), $this->getCellValue($sheet, 'Document_extension', $i));

            // may be this is hack, but let it be {
            $document->srcFile = StringTools::CyToEn($this->getCellValue($sheet, 'Document_filename', $i)); // cyrilic to latinitsa
            $document->srcFile = str_replace(' ', '_', $document->srcFile);
            $document->srcFile = str_replace('.docx', '.pdf', $document->srcFile);
            $document->srcFile = str_replace('.pptx', '.pdf', $document->srcFile);
            // may be this is hack, but let it be }

            $document->format = $this->getCellValue($sheet, 'Document_extension', $i);

            $document->type = $this->getCellValue($sheet, 'Document_type', $i);
            $document->hidden = 'start' === $document->type ? 0 : 1;
            $document->import_id = $this->import_id;
            $document->scenario_id = $this->scenario->primaryKey;

            // save
            $document->save();

            $importedRows++;
        }

        // delete old unused data {
        DocumentTemplate::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_documents' => $importedRows,
            'errors'             => false,
        );
    }

    /**
     *
     */
    public function importDialogReplicas()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
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
            $replica_excel_id = $this->getCellValue($sheet, 'id записи', $i);
            if (NULL == $replica_excel_id) {
                continue;
            }

            $replica = $this->scenario->getReplica(['excel_id' => $replica_excel_id]);
            if (NULL === $replica) {
                $replica = new Replica(); // Создаем событие
                $replica->excel_id = $replica_excel_id;
            }

            // a lot of dialog properties: {
            $replica->code = $this->getCellValue($sheet, 'Event_code', $i);
            $replica->event_result = 7; // ничего
            $from_character_code = $this->getCellValue($sheet, 'Персонаж-ОТ (код)', $i);
            $replica->ch_from = $this->scenario->getCharacter(['code' => $from_character_code])->primaryKey;
            $to_character_code = $this->getCellValue($sheet, 'Персонаж-КОМУ (код)', $i);
            $replica->ch_to = $this->scenario->getCharacter(['code' => $to_character_code])->primaryKey;

            $subtypeAlias = $this->getCellValue($sheet, 'Тип интерфейса диалога', $i);
            if (!isset($subtypes[$subtypeAlias])) {
                throw new Exception('Unknown dialog type: ' . $subtypeAlias);
            }
            $replica->dialog_subtype = (isset($subtypes[$subtypeAlias])) ? $subtypes[$subtypeAlias] : NULL; // 1 is "me"

            $code = $this->getCellValue($sheet, 'Event_result_code', $i);
            $nextEvent = $this->scenario->getEventSample(['code' => $code]);
            $replica->next_event = $nextEvent ? $nextEvent->getPrimaryKey() : null;

            $replica->next_event_code = ('-' == $code) ? NULL : $code;
            $text = $this->getCellValue($sheet, 'Реплика', $i);
            $text = preg_replace('/^\s*-[\s ]*/', ' — ', $text);
            $replica->text = $text;
            $replica->step_number = $this->getCellValue($sheet, '№ шага в диалоге', $i);
            $replica->replica_number = $this->getCellValue($sheet, '№ реплики в диалоге', $i);
            $replica->delay = $this->getCellValue($sheet, 'Задержка, мин', $i);
            $duration = $this->getCellValue($sheet, 'Длительность, сек', $i);
            $replica->duration = ($duration === '-')?null:$duration;

            $flagCode = $this->getCellValue($sheet, 'Переключение флагов 1', $i);
            if ($flagCode !== '') {
                $flag = Flag::model()->findByAttributes([
                    'code'        => $flagCode,
                    'scenario_id' => $this->scenario->primaryKey,
                ]);
                //assert($flag, 'Flag for ' . $flagCode);
                $replica->flag_to_switch = $flag->code;
            } else {
                $replica->flag_to_switch = null;
            }
            unset($flagCode);
            unset($flag);

            $flagCode = $this->getCellValue($sheet, 'Переключение флагов 2', $i);

            if ($flagCode !== '') {
                $flag = Flag::model()->findByAttributes([
                    'code'        => $flagCode,
                    'scenario_id' => $this->scenario->primaryKey,
                ]);
                //assert($flag, 'Flag for ' . $flagCode);
                $replica->flag_to_switch_2 = $flag->code;
            } else {
                $replica->flag_to_switch_2 = null;
            }

            @$isUseInDemo = ('да' == $this->getCellValue($sheet, 'Использовать в DEMO', $i)) ? 1 : 0;
            $replica->demo = $isUseInDemo;
            $replica->type_of_init = $this->getCellValue($sheet, 'Тип запуска', $i);
            $replica->fantastic_result =
                $this->getCellValue($sheet, 'Отправка письма фант образом', $i) ? :
                    $this->getCellValue($sheet, 'Открытие полученного письма фант образом', $i);

            $media_file = $this->getCellValue($sheet, 'Имя звук/видео файла', $i);
            if( $media_file == 'нет' || $media_file == '-' ){
                $replica->media_file_name = null;
                $replica->media_type = null;
            } else {
                $types = ['.webm', '.jpeg', '.wav'];
                foreach($types as $type){
                    $media_type = strstr($media_file, $type);
                    if(false !== $media_type) {
                        $replica->media_file_name = str_replace($media_type, '', $media_file);
                        $replica->media_type = ltrim($media_type, '.');
                    }
                }
            }

            $isFinal = $this->getCellValue($sheet, 'Конечная реплика (да/нет)', $i);
            $replica->is_final_replica = ('да' === $isFinal) ? true : false;

            $replica->import_id = $this->import_id;
            $replica->scenario_id = $this->scenario->primaryKey;
            // a lot of dialog properties: }

            $replica->save();

            $importedRows++;
        }

        // delete old unused data {
        Replica::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_dialog_replics' => $importedRows,
            'errors'                  => false,
        );
    }

    /**
     *
     */
    public function importDialogs()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
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
            if (in_array($code, $this->importedEvents)) {
                continue;
            }

            $this->importedEvents[] = $code;

            $dialog = $this->scenario->getDialog([
                'code' => $code
            ]);
            //$dialog->deleteByPk($code);

            if (null === $dialog) {
                $dialog = new Dialog(); // Создаем событие
                $dialog->code = $code;
            }

            $dialog->title = $this->getCellValue($sheet, 'Наименование события', $i);
            $dialog->setTypeFromExcel($this->getCellValue($sheet, 'Тип интерфейса диалога', $i));
            $dialog->start_by = $this->getCellValue($sheet, 'Тип запуска', $i);
            $dialog->delay = $this->getCellValue($sheet, 'Задержка, мин', $i);
            $dialog->category = $this->getCellValue($sheet, 'Категория события', $i);
            $dialog->start_time = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Начало, время', $i), 'hh:mm:ss') ? : null;
            @$dialog->is_use_in_demo = ('да' == $this->getCellValue($sheet, 'Использовать в DEMO', $i)) ? true : false;
            $dialog->import_id = $this->import_id;
            $dialog->scenario_id = $this->scenario->primaryKey;
            $dialog->save();
            $importedRows++;
        }
        // Events from dialogs }

        // Create crutch events (Hello, Sergey) {
        $dialogT = $dialog = $this->scenario->getDialog([
            'code' => "T"
        ]);

        if (null == $dialogT) {
            $dialogT = new Dialog(); // Создаем событие
            $dialogT->code = 'T';
        }

        $dialogT->title = 'Конечное событие';
        $dialogT->start_by = Dialog::START_BY_DIALOG;
        $dialogT->delay = 0;
        $dialogT->is_use_in_demo = true;
        $dialogT->category = 5;
        $dialogT->import_id = $this->import_id;
        $dialogT->scenario_id = $this->scenario->primaryKey;
        $dialogT->save();
        // }

        // delete old unused data {
        Dialog::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_dialogs' => $importedRows,
            'errors'           => false,
        );
    }

    /**
     *
     */
    public function importDialogPoints()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        // link points to excelColums: pint titles placed in row 2 {        
        $points = [];
        foreach ($this->scenario->getHeroBehavours([]) as $point) {
            if (isset($this->columnNoByName[$point->code])) {
                $points[] = $point;
            }
        }
        // link points to excelColums }

        $importedRows = 0;

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            // in the bottom of excel sheet we have a couple of check sum, that aren`t replics sure.
            $excelId = $this->getCellValue($sheet, 'id записи', $i);
            if (NULL == $excelId) {
                continue;
            }

            $dialog = $this->scenario->getReplica(['excel_id' => $excelId]);

            if (NULL === $dialog) {
                throw new Exception('Try to use unexisi in DB dialog, with ExcelId ' . $excelId);
            }

            foreach ($points as $point) {
                $score = $this->getCellValue($sheet, $point->code, $i);

                // ignore empty cells, but we must imort all "0" values!
                if (NULL === $score || '' === $score) {
                    continue;
                }

                $charactersPoints = ReplicaPoint::model()->findByAttributes([
                    'point_id'    => $point->id,
                    'dialog_id'   => $dialog->id,
                    'scenario_id' => $this->scenario->primaryKey,
                ]);
                if (NULL === $charactersPoints) {
                    $charactersPoints = new ReplicaPoint();
                    $charactersPoints->dialog_id = $dialog->id;
                    $charactersPoints->point_id = $point->id;
                }

                $charactersPoints->add_value = $score;
                $charactersPoints->import_id = $this->import_id;
                $charactersPoints->scenario_id = $this->scenario->primaryKey;

                $charactersPoints->save();

                $importedRows++;
            }
        }

        // delete old unused data {
        ReplicaPoint::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_characters_points' => $importedRows,
            'errors'                     => false,
        );
    }

    /**
     *
     */
    public function importEventSamples()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
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

            $event = EventSample::model()->findByAttributes([
                'code'        => $code,
                'scenario_id' => $this->scenario->primaryKey
            ]);

            if (!$event) {
                $event = new EventSample(); // Создаем событие
                $event->code = $code;
            }

            $event->title = $this->getCellValue($sheet, 'Наименование события', $i);
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->trigger_time = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Начало, время', $i), 'hh:mm:ss') ? : null;
            $event->import_id = $this->import_id;
            $event->scenario_id = $this->scenario->primaryKey;

            if ($event->validate()) {
                $event->save();
            } else {
                throw new CException($event->getErrors());
            }

            $importedRows++;
        }
        // Events from dialogs }

        // Create crutch events (Hello, Sergey) {
        $event = $this->scenario->getEventSample(['code' => 'T']);
        if (!$event) {
            $event = new EventSample(); // Создаем событие
            $event->code = 'T';
        }

        $event->title = 'Конечное событие';
        $event->on_ignore_result = 7; // ничего
        $event->on_hold_logic = 1; // ничего
        $event->trigger_time = 0;
        $event->import_id = $this->import_id;
        $event->scenario_id = $this->scenario->primaryKey;
        $event->save();
        // }

        $sheet = $excel->getSheetByName('to-do-list');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 1);
        // load sheet }

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $planCode = $this->getCellValue($sheet, 'Plan_code', $i);
            if ($planCode === null)
                continue;
            $event = $this->scenario->getEventSample(['code' => $planCode]);
            if (!$event) {
                $event = new EventSample(); // Создаем событие
            }
            $event->code = $planCode;
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->import_id = $this->import_id;
            $event->title = '';
            $event->scenario_id = $this->scenario->primaryKey;
            $event->save();
        }

        // delete old unused data {
        EventSample::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'imported_documents' => $importedRows,
            'errors'             => false,
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

    public function logging($message)
    {
        if ('console' == $this->environment) {
            echo "\n\r" . $message;
        }

        if ('db' == $this->environment) {
            $f = fopen(__DIR__.'/../../logs/'.$this->dbLogInstance->id.'-import.log', 'a+');
            fwrite($f, "\r\n" . $message);
            fclose($f);
            $this->dbLogInstance->text .= "\r\n" . $message;
        }
    }

    private function logStart($message = null)
    {
        $callers = debug_backtrace();

        if (null !== $message) { $this->logging($message); }
        $this->logging('START: ' . $callers[1]['function'] . " " . date('H:i:s'));
    }

    private function logEnd($message = null)
    {
        $callers = debug_backtrace();

        if (null !== $message) {
           $this->logging($message);
        } else {
            $this->logging('successfully!');
        }
        $this->logging('FINISH: ' . $callers[1]['function'] . " " . date('H:i:s') . "\n");
    }

    /**
     * @return PHPExcel_Reader_Excel2003XML
     */
    private function getReader()
    {
        if ($this->cache_method) {
            PHPExcel_Settings::setCacheStorageMethod($this->cache_method);
        }

        if (!isset($this->reader)) {
            $this->reader = PHPExcel_IOFactory::createReader('Excel2007');

            // prevent read string "11:00" like "0.45833333333333" even by getValue()
            $this->reader->setReadDataOnly(true);
        }

        return $this->reader;
    }

    /**
     * Returns cached excel file
     * @return PHPExcel
     */
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
        $this->logStart();

        $activity_types = array(
            'Documents_leg'   => 'document_id',
            'Manual_dial_leg' => 'dialog_id',
            'System_dial_leg' => 'dialog_id',
            'Inbox_leg'       => 'mail_id',
            'Outbox_leg'      => 'mail_id',
            'Window'          => 'window_id',
            'Meeting'         => 'meeting_id'
        );

        $sheet = $this->getExcel()->getSheetByName('Leg_actions');

        if (null === $sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }

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
            $activity = Activity::model()->findByAttributes([
                'code' => $activityCode,
                'scenario_id' => $this->scenario->primaryKey
            ]);

            // create Activity 
            if ($activity === null) {
                $activity = new Activity();
                $activity->code = $activityCode;
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
            $activity->scenario_id = $this->scenario->primaryKey;
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
                    $values = $this->scenario->getReplicas([]);
                } else if ($xls_act_value === 'phone talk') {
                    $values = [null];
                } else {
                    $dialogs = $this->scenario->getReplicas(array('code' => $xls_act_value));
                    if (count($dialogs) === 0) {
                        assert($dialogs, 'No such dialog: "' . $xls_act_value . '"');
                    }
                    $values = $dialogs;
                }
            } else if ($type === 'mail_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear yet
                    $values = $this->scenario->getMailTemplates([]);
                } else if ($xls_act_value === 'incorrect_sent' or $xls_act_value === 'not_sent') {
                    $values = [null];
                } else {
                    $mail = $this->scenario->getMailTemplate(array('code' => $xls_act_value));
                    if ($mail === null) {
                        throw new Exception('No such mail: ' . $xls_act_value);
                    }
                    $values = array($mail);
                }
            } else if ($type === 'document_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear yet
                    $values = $this->scenario->getDocumentTemplates([]);
                } else {
                    $document = $this->scenario->getDocumentTemplate(array('code' => $xls_act_value));
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
            } else if ($type === 'meeting_id') {
                if ($xls_act_value === 'all') {
                    $values = $this->scenario->getMeetings([]);
                } else {
                    $meeting = $this->scenario->getMeeting(array('code' => $xls_act_value));
                    assert($meeting);
                    $values = array($meeting);
                }
            } else {
                throw new Exception('Can not handle type:' . $type);
            }

            // update relation Activiti to Document, Replica replic ro Email {
            foreach ($values as $value) {
                /** @var ActivityAction $activityAction */
                $activityAction = ActivityAction::model()->findByAttributes(array(
                    'activity_id' => $activity->primaryKey,
                    'scenario_id' => $this->scenario->primaryKey,
                    $type         => ($value !== null ? $value->primaryKey : null)
                ));
                if ($activityAction === null) {
                    $activityAction = new ActivityAction();
                }
                $activityAction->import_id = $this->import_id;
                $activityAction->scenario_id = $this->scenario->primaryKey;
                $activityAction->activity_id = $activity->id;
                $activityAction->leg_type = $leg_type;
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
        ActivityAction::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array(
                'import_id' => $this->import_id,
                'scenario_id' => $this->scenario->primaryKey
            ));

        Activity::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array(
                'import_id' => $this->import_id,
                'scenario_id' => $this->scenario->primaryKey
            ));
        // delete old unused data }

        $this->logEnd();

        return array(
            'activity_actions' => $activity_actions,
            'errors'           => false,
            'activities'       => count($activities)
        );
    }

    public function importActivityParentEnding()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Parent_ending');
        if (null === $sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $types = [
            'id_записи' => 'dialog_id',
            'outbox'    => 'mail_id',
            'inbox'     => 'mail_id'
        ];

        $updatedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $parentCode = $this->getCellValue($sheet, 'Parent', $i);
            $endType = $this->getCellValue($sheet, 'Parent_end_type', $i);
            $endCode = $this->getCellValue($sheet, 'Parent_end_code', $i);

            if ($endType == 'id_записи') {
                $entity = $this->scenario->getReplica(['excel_id' => $endCode]);
            } elseif ($endType == 'outbox' || $endType == 'inbox') {
                $entity = $this->scenario->getMailTemplate(['code' => $endCode]);
            }

            if (isset($entity)) {
                $parentActivity = ActivityParent::model()->findByAttributes([
                    'parent_code'    => $parentCode,
                    'scenario_id'    => $this->scenario->primaryKey,
                    $types[$endType] => $entity->id
                ]);

                if (empty($parentActivity)) {
                    $parentActivity = new ActivityParent();
                }

                $parentActivity->parent_code = $parentCode;
                $parentActivity->import_id = $this->import_id;
                $parentActivity->scenario_id = $this->scenario->primaryKey;
                $parentActivity->{$types[$endType]} = $entity->id;
                $parentActivity->save();

                $updatedRows++;
            }
        }

        // delete old unused data {
        ActivityParent::model()->deleteAll(
            'import_id <> :import_id AND scenario_id=:scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'updated_activityActions' => $updatedRows,
            'errors'                  => false,
        );
    }

    public function importPerformanceRules()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Result_rules');
        if (null === $sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $types = [
            'id_записи' => 'replica_id',
            'outbox'    => 'mail_id',
            'inbox'     => 'mail_id',
            'excel'     => 'excel_formula_id'
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

            $rule = $this->scenario->getPerformanceRule(['code' => $ruleId]);
            if (empty($rule)) {
                $rule = new PerformanceRule();
                $rule->code = $ruleId;
            }

            $rule->activity_id = $this->getCellValue($sheet, 'Activity_code', $i);

            $rule->operation = $this->getCellValue($sheet, 'Result_operation', $i);
            $rule->value = $this->getCellValue($sheet, 'All_Result_value', $i);
            $rule->import_id = $this->import_id;
            $rule->scenario_id = $this->scenario->primaryKey;
            $rule->category_id = $this->getCellValue($sheet, 'Категория', $i);

            $rule->save();
            $rules++;

            if ($type == 'id_записи') {
                $entity = $this->scenario->getReplica(['excel_id' => $code]);
            } elseif ($type == 'outbox' || $type == 'inbox') {
                $entity = $this->scenario->getMailTemplate(['code' => $code]);
            } elseif ($type == 'excel') {
                $entity = ExcelPointFormula::model()->findByPk($code);
            } else {
                $entity = null;
            }

            if (isset($entity)) {
                $condition = PerformanceRuleCondition::model()->findByAttributes([
                    'performance_rule_id' => $rule->id,
                    'scenario_id'         => $this->scenario->primaryKey,
                    $types[$type]         => $entity->id
                ]);

                if (empty($condition)) {
                    $condition = new PerformanceRuleCondition();
                }

                $condition->performance_rule_id = $rule->id;
                $condition->{$types[$type]} = $entity->id;
                $condition->import_id = $this->import_id;
                $condition->scenario_id = $this->scenario->primaryKey;

                $condition->save();
                $conditions++;
            }
        }

        // delete old unused data {
        PerformanceRuleCondition::model()->deleteAll(
            'import_id <> :import_id AND scenario_id=:scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        PerformanceRule::model()->deleteAll(
            'import_id <> :import_id AND scenario_id=:scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'performance_rules'           => $rules,
            'performance_rule_conditions' => $conditions,
            'errors'                      => false,
        );
    }

    public function importMaxRate()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Max_rate');
        if (null === $sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $types = [
            'Цель обучения' => 'learning_goal_id',
            'Требуемое поведение' => 'hero_behaviour_id',
            'Результативность' => 'performance_rule_category_id'
        ];

        $rates = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $entityType = $this->getCellValue($sheet, 'Тип объекта, к которому max_rate применяется', $i);
            $entityCode = $this->getCellValue($sheet, 'Код объекта', $i);
            $fKey = $types[$entityType];

            if ($fKey == 'learning_goal_id') {
                $entity = $this->scenario->getLearningGoal(['code' => $entityCode]);
            } elseif ($fKey == 'hero_behaviour_id') {
                $entity = $this->scenario->getHeroBehaviour(['code' => $entityCode]);
            } elseif ($fKey == 'performance_rule_category_id') {
                $entity = ActivityCategory::model()->findByAttributes(['code' => $entityCode]);
            } else {
                $entity = null;
            }

            if (!empty($entity)) {
                $rateEntity = $this->scenario->getMaxRate([$types[$entityType] => $entityCode]);
                if (empty($rateEntity)) {
                    $rateEntity = new MaxRate();
                    $rateEntity->$fKey = $entityCode;
                }

                $rateEntity->$fKey = $entity->primaryKey;
                $rateEntity->type = $this->getCellValue($sheet, 'Rate_type', $i);
                $rateEntity->rate = $this->getCellValue($sheet, 'Max_rate', $i);
                $rateEntity->scenario_id = $this->scenario->primaryKey;
                $rateEntity->import_id = $this->import_id;

                $rateEntity->save();

                $rates++;
            }
        }

        // delete old unused data {
        MaxRate::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'max_rates' => $rates,
            'errors'    => false,
        );
    }

    public function importWeights()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Weights');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $managementMapping = [
            'Управленческие навыки (ИТОГО)' => AssessmentCategory::MANAGEMENT_SKILLS,
            'Результативность (ИТОГО)' => AssessmentCategory::PRODUCTIVITY,
            'Эффективность использования времени (ИТОГО)' => AssessmentCategory::TIME_EFFECTIVENESS,
        ];

        $performanceMapping = [
            'Результативность (K0)' => '0',
            'Результативность (K1)' => '1',
            'Результативность (K2)' => '2',
            'Результативность (2_min)' => '2_min'
        ];

        $types = [
            Weight::RULE_OVERALL_RATE => 'assessment_category_code',
            Weight::RULE_PERFORMANCE => 'performance_rule_category_id',
            Weight::RULE_DECISION_MAKING => 'hero_behaviour_id'
        ];

        $weights = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $ruleId = $this->getCellValue($sheet, 'Номер правила для расчета веса', $i);
            $code = $this->getCellValue($sheet, 'Оценка, к которой применяется вес', $i);

            if (empty($types[$ruleId])) {
                continue;
            }

            $fKey = $types[$ruleId];
            if ($fKey == 'assessment_category_code') {
                $entity = AssessmentCategory::model()->findByAttributes(['code' => $managementMapping[$code]]);
            } elseif ($fKey == 'performance_rule_category_id') {
                $entity = ActivityCategory::model()->findByAttributes(['code' => $performanceMapping[$code]]);
            } elseif ($fKey == 'hero_behaviour_id') {
                $entity = $this->scenario->getHeroBehaviour(['code' => $code]);
            } else {
                $entity = null;
            }

            if (!empty($entity)) {
                $weight = $this->scenario->getWeight([$fKey => $entity->primaryKey]);
                if (empty($weight)) {
                    $weight = new Weight();
                }

                $weight->$fKey = $entity->primaryKey;
                $weight->rule_id = $ruleId;
                $weight->value = round($this->getCellValue($sheet, 'Значение веса', $i), 10);
                $weight->scenario_id = $this->scenario->primaryKey;
                $weight->import_id = $this->import_id;

                $weight->save(false);
                $weights++;
            }
        }

        // delete old unused data {
        Weight::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'weights' => $weights,
            'errors'    => false,
        );
    }

    public function importMeetings()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Meetings');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $meetings = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Meeting_code', $i);
            $taskCode = $this->getCellValue($sheet, 'Plan_connection', $i);

            $meeting = $this->scenario->getMeeting(['code' => $code]);
            if (empty($meeting)) {
                $meeting = new Meeting();
                $meeting->code = $code;
            }

            $meeting->name = $this->getCellValue($sheet, 'Meeting_name', $i);
            $meeting->icon_text = $this->getCellValue($sheet, 'Meeting_icon_text', $i);
            $meeting->popup_text = $this->getCellValue($sheet, 'Meeting_popup_text', $i);
            $meeting->duration = $this->getCellValue($sheet, 'Duration', $i);

            if ($taskCode) {
                $task = $this->scenario->getTask(['code' => $taskCode]);
                if ($task) {
                    $meeting->task_id = $task->id;
                }
            }

            $meeting->scenario_id = $this->scenario->primaryKey;
            $meeting->import_id = $this->import_id;

            $meeting->save();
            $meetings++;
        }

        // delete old unused data {
        Meeting::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'meetings' => $meetings,
            'errors'    => false,
        );
    }

    public function importFlagTimeSwitch()
    {
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Set_flags');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $flagSwitches = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $id = $this->getCellValue($sheet, 'Set_flag_id', $i);

            $flagSwitch = $this->scenario->getFlagSwitchTime(['id' => $id]);
            if (empty($flagSwitch)) {
                $flagSwitch = new FlagSwitchTime();
                $flagSwitch->id = $id;
            }

            $flagSwitch->flag_code = $this->getCellValue($sheet, 'Flag_code_to_set', $i);
            $flagSwitch->value = $this->getCellValue($sheet, 'Flag_value_to_set', $i);
            $flagSwitch->time = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Set_flag_value', $i), 'hh:mm:ss') ?: null;

            $flagSwitch->scenario_id = $this->scenario->primaryKey;
            $flagSwitch->import_id = $this->import_id;

            $flagSwitch->save();
            $flagSwitches++;
        }

        // delete old unused data {
        FlagSwitchTime::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'flag_switches' => $flagSwitches,
            'errors'    => false,
        );
    }

    /**
     * Only must to use functions. Has correct import order
     */
    public function importAll()
    {
        ini_set('memory_limit', '900M');

        $transaction = Yii::app()->db->beginTransaction();
        try {
            $result = $this->importWithoutTransaction();

            $transaction->commit();

            $this->logging('All operation complete!');

        } catch (Exception $e) {
            $transaction->rollback();
            $this->logging('Exception: ' . $e->getMessage());
            throw $e;
        }

        return $result;
    }

    public function importScenarioConfig()
    {
        //return;
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Scenario_configs');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $items = 0;

        $scenarioConfig = ScenarioConfig::model()->findByAttributes(['scenario_id'=>$this->scenario->id]);
        if (null === $scenarioConfig) {
            $scenarioConfig = new ScenarioConfig();
        }

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $name = (string)trim($this->getCellValue($sheet, 'Name', $i));
            $value = trim($this->getCellValue($sheet, 'Value', $i));

            if(empty($name) || empty($value)) { continue; }

            if ('game_date_data' == $name && '4.10.2013' ==  $value) {
                $value = '04.10.2013';
            }

            $scenarioConfig->{$name} = $value;
            $items++;
        }

        $scenarioConfig->scenario_id = $this->scenario->primaryKey;
        $scenarioConfig->import_id = $this->import_id;
        $scenarioConfig->save(false);

//        // TODO: Hardcode. Time should be defined in scenario file
//        if ($this->scenario->slug == Scenario::TYPE_LITE) {
//            $this->scenario->start_time = '9:45:00'; $scenarioConfig->game_start_timestamp;
//            $this->scenario->end_time = '11:05:00'; $scenarioConfig->game_end_workday_timestamp;
//            $this->scenario->finish_time = '11:05:00'; $scenarioConfig->game_end_timestamp;
//            $this->scenario->duration_in_game_min = 80;
//        } elseif ($this->scenario->slug == Scenario::TYPE_FULL) {
//            $this->scenario->start_time = '9:45:00';
//            $this->scenario->end_time = '18:00:00';
//            $this->scenario->finish_time = '20:00:00';
//            $this->scenario->duration_in_game_min = 495;
//        } elseif ($this->scenario->slug == Scenario::TYPE_TUTORIAL) {
//            $this->scenario->start_time = '9:45:00';
//            $this->scenario->end_time = '12:45:00';
//            $this->scenario->finish_time = '12:45:00';
//            $this->scenario->duration_in_game_min = 180;
//        }

        // update scenario object TIME options {
        $this->scenario->start_time = $scenarioConfig->game_start_timestamp;
        $this->scenario->end_time = $scenarioConfig->game_end_workday_timestamp;
        $this->scenario->finish_time = $scenarioConfig->game_end_timestamp;

        $startTimeArray = explode(':', $scenarioConfig->game_start_timestamp);
        $endWorkdayTimeArray = explode(':', $scenarioConfig->game_end_workday_timestamp);
        $this->scenario->duration_in_game_min = $endWorkdayTimeArray[0]*60 + $endWorkdayTimeArray[1]
            - $startTimeArray[0]*60 - $startTimeArray[1];
        $this->scenario->save();
        // update scenario object TIME options }

        // delete old unused data {
        ScenarioConfig::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'items' => $items,
            'errors'    => false,
        );
    }

    public function importParagraphsAndPockets()
    {
        //return;
        $this->logStart();

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Paragraphs');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $items = 0;
        $order_number = 1;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $paragraph = $this->scenario->getParagraph(['alias'=>$this->getCellValue($sheet, 'alias', $i)]);
            if (null === $paragraph) {
                $paragraph = new Paragraph();
                $paragraph->alias = $this->getCellValue($sheet, 'alias', $i);
            }
            $paragraph->order_number = $order_number;
            $paragraph->label = trim($this->getCellValue($sheet, 'label', $i));
            $paragraph->value_1 = trim($this->getCellValue($sheet, 'value_1', $i));
            $paragraph->value_2 = trim($this->getCellValue($sheet, 'value_2', $i));
            $paragraph->value_3 = trim($this->getCellValue($sheet, 'value_3', $i));
            $paragraph->method = trim($this->getCellValue($sheet, 'method', $i));
            $paragraph->scenario_id = $this->scenario->primaryKey;
            $paragraph->import_id = $this->import_id;
            $paragraph->save(false);
            $items++;
            $order_number++;
        }

        $sheet = $excel->getSheetByName('Pockets');
        if (!$sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $paragraph = $this->scenario->getParagraphPocket([
                'paragraph_alias'=>$this->getCellValue($sheet, 'paragraph_alias', $i),
                'behaviour_alias'=>$this->getCellValue($sheet, 'behaviour_alias', $i),
                'left_direction'=>$this->getCellValue($sheet, 'left_direction', $i),
                'left'=>$this->getCellValue($sheet, 'left', $i),
                'right_direction'=>$this->getCellValue($sheet, 'right_direction', $i),
                'right'=>$this->getCellValue($sheet, 'right', $i),
            ]);
            if (null === $paragraph) {
                $paragraph = new ParagraphPocket();
                $paragraph->paragraph_alias = trim($this->getCellValue($sheet, 'paragraph_alias', $i));
                $paragraph->behaviour_alias = trim($this->getCellValue($sheet, 'behaviour_alias', $i));
                $paragraph->left_direction = trim($this->getCellValue($sheet, 'left_direction', $i));
                $paragraph->left = trim($this->getCellValue($sheet, 'left', $i));
                $paragraph->right_direction = trim($this->getCellValue($sheet, 'right_direction', $i));
                $paragraph->right = trim($this->getCellValue($sheet, 'right', $i));
            }
            $paragraph->text = trim($this->getCellValue($sheet, 'text', $i));
            $paragraph->scenario_id = $this->scenario->primaryKey;
            $paragraph->import_id = $this->import_id;
            $paragraph->save(false);
            $items++;
        }

        // update scenario object TIME options }

        // delete old unused data {
        Paragraph::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        ParagraphPocket::model()->deleteAll(
            'import_id <> :import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return array(
            'items' => $items,
            'errors'    => false,
        );
    }

    /**
     * @param $result
     * @return mixed
     */
    public function importWithoutTransaction()
    {
        $result = [];
        $result['scenario_config'] = $this->importScenarioConfig();
        $result['assessment_group'] = $this->importAssessmentGroup();
        $result['characters'] = $this->importCharacters();
        $result['learning_areas'] = $this->importLearningAreas();
        $result['learning_goals'] = $this->importLearningGoals();
        $result['characters_points_titles'] = $this->importHeroBehaviours();
        $result['flags'] = $this->importFlags();
        $result['replicas'] = $this->importDialogReplicas();
        $result['dialogs'] = $this->importDialogs();
        $result['my_documents'] = $this->importMyDocuments();
        $result['character_points'] = $this->importDialogPoints();
        $result['constructor'] = $this->importMailConstructor();
        $result['email_subjects'] = $this->importAllThemes();
        $result['emails'] = $this->importEmails();
        $result['mail_attaches'] = $this->importMailAttaches();
        $result['mail_events'] = $this->importMailEvents();
        $result['tasks'] = $this->importTasks();
        $result['mail_tasks'] = $this->importMailTasks();
        $result['event_samples'] = $this->importEventSamples();
        $result['meetings'] = $this->importMeetings();
        $result['activity'] = $this->importActivity();
        $result['activity_parent_ending'] = $this->importActivityParentEnding();
        $result['flag_rules'] = $this->importFlagsRules();
        $result['performance_rules'] = $this->importPerformanceRules();
        $result['stress_rules'] = $this->importStressRules();
        $result['max_rate'] = $this->importMaxRate();
        $result['weights'] = $this->importWeights();
        $result['activity_parent_availability'] = $this->importActivityParentAvailability();
        $result['flag_time_switch'] = $this->importFlagTimeSwitch();
        $result['paragraphs_and_pockets'] = $this->importParagraphsAndPockets();

        return $result;
    }


    public function importFlagsRules()
    {
        $this->logStart();

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Flags');
        if (null === $sheet) {
            $this->logEnd('WARNING: no sheet');
            return ['error' => 'no sheet'];
        }
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedFlagToRunMailRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('mail' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            /** @var $emailEvent EventSample */
            $emailEvent = $this->scenario->getEventSample([
                'code' => $this->getCellValue($sheet, 'Run_code', $i)
            ]);

            if (NULL === $emailEvent) {
                throw new Exception('Can`t find event sample for email ' . $this->getCellValue($sheet, 'Run_code', $i));
            }

            // we run, immediatly after flag was switched, email without trigger time only
            if ('00:00:00' == $emailEvent->trigger_time || null == $emailEvent->trigger_time) {

                // try to find exists entity {
                $mailFlag = $this->scenario->getFlagRunMail([
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
                $mailFlag->scenario_id = $this->scenario->primaryKey;
                $mailFlag->save();
                // create entity if not exists }

                $importedFlagToRunMailRows++;
            }

            // Flag blocks mail always {
            $mailTemplate = $this->scenario->getMailTemplate(['code' => $this->getCellValue($sheet, 'Run_code', $i)]);
            $mailFlag = $this->scenario->getFlagBlockMail([
                'mail_template_id' => $mailTemplate->primaryKey,
                'flag_code'        => $this->getCellValue($sheet, 'Flag_code', $i),
            ]);
            if (null === $mailFlag) {
                $mailFlag = new FlagBlockMail();
            }
            $mailFlag->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            $mailFlag->mail_template_id = $mailTemplate->primaryKey;
            $mailFlag->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $mailFlag->import_id = $this->import_id;
            $mailFlag->scenario_id = $this->scenario->primaryKey;
            $mailFlag->save();
            // Flag blocks mail always }
        }

        /* Создание зависимости ком. тем от флагов для тем в почте */
        $importedOutboxMailThemeDependence = 0;
        $importedFlagToRunOutboxMailRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('mail_outbox' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }
            /* Создание зависимости тем от флагов для реплик в почте */
            $outboxMailTheme = $this->scenario->getOutboxMailTheme(['mail_code'=>$this->getCellValue($sheet, 'Run_code', $i)]);
            if(null === $outboxMailTheme) {
                continue;
            }
            $flagOutboxMailThemeDependence = $this->scenario->getFlagOutboxMailThemeDependence(['outbox_mail_theme_id'=>$outboxMailTheme->id, 'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i)]);
            if(null === $flagOutboxMailThemeDependence) {
                $flagOutboxMailThemeDependence = new FlagOutboxMailThemeDependence();
                $flagOutboxMailThemeDependence->outbox_mail_theme_id = $outboxMailTheme->id;
                $flagOutboxMailThemeDependence->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            }
            $flagOutboxMailThemeDependence->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagOutboxMailThemeDependence->scenario_id = $this->scenario->primaryKey;
            $flagOutboxMailThemeDependence->import_id = $this->import_id;
            $flagOutboxMailThemeDependence->save();
            unset($outboxMailTheme);
            unset($flagOutboxMailThemeDependence);
            $importedFlagToRunOutboxMailRows++;
            $importedOutboxMailThemeDependence++;
        }


        $importedFlagBlockReplica = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('replica' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // try to find exists entity {
            $flagBlockReplica = $this->scenario->getFlagBlockReplica([
                'flag_code'  => $this->getCellValue($sheet, 'Flag_code', $i),
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
            $flagBlockReplica->scenario_id = $this->scenario->primaryKey;

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
            $flagBlockDialog = $this->scenario->getFlagBlockDialog([
                'flag_code'   => $this->getCellValue($sheet, 'Flag_code', $i),
                'dialog_code' => $this->getCellValue($sheet, 'Run_code', $i),
            ]);
            // try to find exists entity }

            // create entity if not exists {
            if (null === $flagBlockDialog) {
                $flagBlockDialog = new FlagBlockDialog();
                $flagBlockDialog->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $flagBlockDialog->dialog_code = $this->getCellValue($sheet, 'Run_code', $i);
            }
            // create entity if not exists }`

            $flagBlockDialog->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagBlockDialog->import_id = $this->import_id;
            $flagBlockDialog->scenario_id = $this->scenario->primaryKey;

            $flagBlockDialog->save();

            /* Создание зависимости тем от флагов для диалогов в телефоне */
            $outgoingPhoneTheme = $this->scenario->getOutgoingPhoneTheme(['dialog_code'=>$this->getCellValue($sheet, 'Run_code', $i)]);
            if(null === $outgoingPhoneTheme) {
                continue;
            }
            $flagOutgoingPhoneThemeDependence = $this->scenario->getFlagOutgoingPhoneThemeDependence(['outgoing_phone_theme_id'=>$outgoingPhoneTheme->id, 'flag_code' => $this->getCellValue($sheet, 'Flag_code', $i)]);
            if(null === $flagOutgoingPhoneThemeDependence) {
                $flagOutgoingPhoneThemeDependence = new FlagOutgoingPhoneThemeDependence();
                $flagOutgoingPhoneThemeDependence->outgoing_phone_theme_id = $outgoingPhoneTheme->id;
                $flagOutgoingPhoneThemeDependence->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            }
            $flagOutgoingPhoneThemeDependence->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagOutgoingPhoneThemeDependence->scenario_id = $this->scenario->primaryKey;
            $flagOutgoingPhoneThemeDependence->import_id = $this->import_id;
            $flagOutgoingPhoneThemeDependence->save();
            unset($flagOutgoingPhoneThemeDependence);

            $importedFlagBlockDialog++;

        }
        // for Dialogs }

        $importedFlagAllowMeeting = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('meeting' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            $meeting = $this->scenario->getMeeting(['code' => $this->getCellValue($sheet, 'Run_code', $i)]);

            // try to find exists entity {
            $flagAllowMeeting = $this->scenario->getFlagAllowMeeting([
                'flag_code'  => $this->getCellValue($sheet, 'Flag_code', $i),
                'meeting_id' => $meeting->id,
            ]);
            // try to find exists entity }

            // create entity if not exists {
            if (null === $flagAllowMeeting) {
                $flagAllowMeeting = new FlagAllowMeeting();
                $flagAllowMeeting->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $flagAllowMeeting->meeting_id = $meeting->id;
            }
            // create entity if not exists }

            $flagAllowMeeting->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagAllowMeeting->import_id = $this->import_id;
            $flagAllowMeeting->scenario_id = $this->scenario->primaryKey;

            $flagAllowMeeting->save();
            $importedFlagAllowMeeting++;
        }

        // delete old unused data {
        FlagRunMail::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        FlagBlockMail::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );

        FlagBlockReplica::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );

        FlagBlockDialog::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );

        FlagOutboxMailThemeDependence::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        FlagOutgoingPhoneThemeDependence::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );

        FlagAllowMeeting::model()->deleteAll(
            'import_id<>:import_id AND scenario_id = :scenario_id',
            array('import_id' => $this->import_id, 'scenario_id' => $this->scenario->primaryKey)
        );
        // delete old unused data }

        $this->logEnd();

        return [
            'imported_Flag_to_run_mail'          => $importedFlagToRunMailRows,
            'imported_Flag_block_replica'        => $importedFlagBlockReplica,
            'imported_Flag_block_dialog'         => $importedFlagBlockDialog,
            'imported_Flag_to_outbox_mail'       => $importedOutboxMailThemeDependence,
            'imported_Flag_allow_meeting'        => $importedFlagAllowMeeting,
            'errors'                             => false,
        ];
    }

    public function setScenario()
    {
        $this->logStart();

        $scenario = Scenario::model()->findByAttributes(['slug' => $this->scenario_slug]);
        if ($scenario === null) {
            $scenario = new Scenario();
        }

        $scenario->slug = $this->scenario_slug;

        // TODO: Hardcode. Time should be defined in scenario file
        if ($scenario->slug == Scenario::TYPE_LITE) {
            $scenario->start_time = '9:45:00';
            $scenario->end_time = '10:10:00';
            $scenario->finish_time = '10:10:00';
            $scenario->duration_in_game_min = 80;
        } elseif ($scenario->slug == Scenario::TYPE_FULL) {
            $scenario->start_time = '9:45:00';
            $scenario->end_time = '18:00:00';
            $scenario->finish_time = '20:00:00';
            $scenario->duration_in_game_min = 495;
        } elseif ($scenario->slug == Scenario::TYPE_TUTORIAL) {
            $scenario->start_time = '9:45:00';
            $scenario->end_time = '12:45:00';
            $scenario->finish_time = '12:45:00';
            $scenario->duration_in_game_min = 180;
        }

        $filename = substr($this->filename, strpos($this->filename, 'scenario_'), 200);
        $scenario->filename = $filename;
        $scenario->save();

        $this->scenario = $scenario;

        $this->logEnd();
    }
}

