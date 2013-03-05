<div class="tooltip" id="tooltip_<? echo $data->id; ?>"> 
	<? $this->renderPartial('_tooltip', array('data' =>  $data)); ?>
</div>

<div class="view_user" id="user_<? echo $data->id;?>"> 

<?
$online = '';
if(Yum::hasModule('profile') && Yum::module('profile')->enablePrivacySetting) {
	if($data->privacy && $data->privacy->show_online_status) {
		if($data->isOnline()) {
			$online .= CHtml::image(Yum::register('images/green_button.png'));
		}
	}
}

?>

<? printf('<h3>%s %s</h3>', $data->username, $online); ?>

<? echo CHtml::link($data->getAvatar(true),
		array(
			'//profile/profile/view', 'id' => $data->id)); ?>
</div>

<?
Yii::app()->clientScript->registerScript('tooltip_'.$data->id, "
$('#user_{$data->id}').tooltip({
'position': 'top',
'offset': [0, -50],
'tip': '#tooltip_{$data->id}',
'predelay': 100,
'fadeOutSpeed': 100,

}); 
");
?>

