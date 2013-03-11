<br/>
<br/>
<br/>
<br/>
<br/>

<?php echo sprintf(
    Yii::t('site','Something went wrong please try to %s register again %s.'),
    '<a href="/userAccount/registration">',
    '</a>'
) ?>

<br/>
<br/>
<?php if (isset($error)) { echo Yii::t('site', $error); } ?>