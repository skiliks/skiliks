<br/>
<br/>
<br/>
<br/>
<h1>Results</h1>

<br/>
<br/>
<br/>
<br/>
<br/>

<table >
    <?php foreach ($results as $result): ?>
        <tr>
            <td style="border: solid #888  1px; padding: 4px;">
                <?php echo $result->point->title?>
            </td>
            <td style="border: solid #888  1px; padding: 4px;">
                <?php echo $result->value ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>