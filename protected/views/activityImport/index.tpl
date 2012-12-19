<h1>{$this->id}/{$this->action->id}</h1>

<pre>
    {if ! $errors === false }
	Errors: {$errors|@var_dump}
    {else}
    Time: {$time|string_format:"%.2f"} seconds
    Activity actions: {$activity_actions}
    Activities: {$activities}
    {/if}
</pre>
