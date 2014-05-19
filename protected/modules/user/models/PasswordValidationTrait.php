<?php

trait PasswordValidationTrait {
    /**
     * Дублируется в YumUser!
     *
     * @param string $attribute, attribute name
     * @param mixed array $params
     */
    public function passwordCyrillicStringMin($attribute, $params)
    {
        $strLength = iconv_strlen($this->$attribute, 'UTF-8');

        if($strLength < $params['limit']) {
            $this->addError(
                $attribute,
                sprintf(
                    Yii::t('site', 'Type %s symbols at least.'),
                    $params['limit']
                )
            );
        }
    }

    /**
     *  Дублируется в YumUser!
     *
     * Проверяет на простоту пароль
     * @param $attribute
     * @return bool
     */
    public function isJustPassword($attribute) {
        if(ctype_digit($this->$attribute)){
            $this->addError($attribute, Yii::t('site', 'Пароль слишком простой'));
            return false;
        }
        if(count(array_unique(str_split($this->$attribute))) === 1) {
            $this->addError($attribute, Yii::t('site', 'Пароль слишком простой'));
            return false;
        }
        return true;
    }
}