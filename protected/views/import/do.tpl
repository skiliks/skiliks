<table border="1">
    <tr>
        <th>Import name</th>
        <th>Import data</th>
    </tr>
{foreach from=$result key=import_name item=import_val}
    <tr>
        <td>{$import_name}</td>
        <td>
            <table>
                {foreach from=$import_val key=k item=v}
                    <tr>
                        <td>{$k}</td>
                        <td>{$v|@var_export}</td>
                    </tr>
                {/foreach}
            </table>
        </td>
    </tr>
{/foreach}
</table>
