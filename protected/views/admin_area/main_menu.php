<?/* @var $user YumUser */
    $user = Yii::app()->user->data();
?>
<div class="nav-collapse collapse">
    <p class="navbar-text pull-right">
        <?=$user->profile->firstname?> <?=$user->profile->lastname?> <a href="/admin_area/logout" class="navbar-link">Выйти</a>
    </p>
    <ul class="nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
</div>