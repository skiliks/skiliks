<?php $cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->registerLessFile($assetsUrl . "/less/feedback.less", $assetsUrl . '/compiled_css/feedback.css');
?>
<div id="feedback-dialog" style="display: none;">
    <form class="feedback jt-feedback jotform-form" action="http://submit.jotformeu.com/submit.php" method="post"
          name="form_30835043655352" id="30835043655352" accept-charset="utf-8">
        <input type="hidden" name="formID" value="30835043655352"/>

        <div class="form-all">
            <ul class="form-section">
                <li class="form-line" id="id_7">
                    <label class="form-label-left" id="label_7" for="input_7">
                        Тема<span class="form-required">*</span>
                    </label>

                    <div id="cid_7" class="form-input">
                        <select class="form-dropdown validate[required]" style="width:150px" id="input_7"
                                name="q7_input7">
                            <option value="">Тема сообщения</option>
                            <option value="А-А-А-А-А-А У МЕНЯ ВСЕ НЕ РАБОТАЕТ"> А-А-А-А-А-А У МЕНЯ ВСЕ НЕ РАБОТАЕТ
                            </option>
                            <option value="Другое"> Другое</option>
                        </select>
                    </div>
                </li>
                <li class="form-line" id="id_5">
                    <label class="form-label-left" id="label_5" for="input_5">
                        Сообщение:<span class="form-required">*</span>
                    </label>

                    <div id="cid_5" class="form-input">
                        <textarea id="input_5" class="form-textarea validate[required]" name="q5_input5" cols="40"
                                  rows="6"></textarea>
                    </div>
                </li>
                <li class="form-line" id="id_6">
                    <label class="form-label-left" id="label_6" for="input_6">
                        E-mail:<span class="form-required">*</span>
                    </label>

                    <div id="cid_6" class="form-input">
                        <input placeholder="Введите ваш Email" type="email" class=" form-textbox validate[required, Email]" id="input_6" name="q6_email"
                               size="30"/>
                    </div>
                </li>
                <li class="form-line" id="id_2">
                    <div id="cid_2" class="form-input-wide">
                        <div style="margin-left:156px" class="form-buttons-wrapper">
                            <button id="input_2" type="submit" class="form-submit-button">
                                Отправить
                            </button>
                        </div>
                    </div>
                </li>
                <li style="display:none">
                    Should be Empty:
                    <input type="text" name="website" value=""/>
                </li>
            </ul>
        </div>
        <input type="hidden" id="simple_spc" name="simple_spc" value="30835043655352"/>
        <script type="text/javascript">
            document.getElementById("si" + "mple" + "_spc").value = "30835043655352-30835043655352";
        </script>
    </form>
</div>