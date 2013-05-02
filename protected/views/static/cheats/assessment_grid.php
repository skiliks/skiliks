<h1>Таблица оценок</h1>
<pre>
<?php
$init_typeLabel = null;
$init_subtypeLabel = null;
$init_areaLabel = null;
//print_r($data);
?>
</pre>
<table>
    <?php foreach ($data as $typeLabel => $ratingType): ?>
        <?php foreach ($ratingType as $subtypeLabel => $ratingSubType): ?>
            <?php foreach ($ratingSubType as $areaLabel => $areas): ?>
                <tr>
                    <td style="border: 1px solid #ddd; padding: 7px">
                        <?php if (null == $init_typeLabel) { $init_typeLabel = $typeLabel; echo $typeLabel; } ?>
                        <?php if ($typeLabel != $init_typeLabel) { $init_typeLabel = $typeLabel; echo $typeLabel; } ?>
                    </td>
                    <td style="border: 1px solid #ddd; padding: 7px">
                        <?php if (null == $init_subtypeLabel) { $init_subtypeLabel = $subtypeLabel; echo $subtypeLabel; } ?>
                        <?php if ($subtypeLabel != $init_subtypeLabel) { $init_subtypeLabel = $subtypeLabel; echo $subtypeLabel; } ?>
                    </td>
                    <td style="border: 1px solid #ddd; padding: 7px">
                        <?php if (null == $init_areaLabel) { $init_subtypeLabel = $areaLabel; echo $areaLabel; } ?>
                        <?php if ($areaLabel != $init_areaLabel) { $init_areaLabel = $areaLabel; echo $areaLabel; } ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endforeach ?>
    <?php endforeach ?>
</table>