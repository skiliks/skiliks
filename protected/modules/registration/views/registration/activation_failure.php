<h2 class="thetitle"> <?php echo Yii::t('site','Activation did not work'); ?> </h2>
<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registerpads">
                <h3><?php if($error == -1) echo Yii::t('site','The user is already activated'); ?></h3>
                    <h3><?php if($error == -2) echo Yii::t('site','Wrong activation Key'); ?></h3>
                        <h3><?php if($error == -3) echo Yii::t('site','Profile found, but no associated user. Possible database inconsistency. Please contact the System administrator with this error message, thank you'); ?>
                        </h3>
                </div>
            </div>
        </div>
    </div>
