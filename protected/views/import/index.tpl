<script type="text/javascript">
    document.title = 'Imports';
    function setIframeSrc(src, title) {
        document.title = title;
        document.getElementById('importpage').src = src;
        console.log(src);
        return false;
    }
</script>    
<div style="float: right">
<p>
    <ul>
        {foreach from=$links item=link}
            <li><a href="{$link['href']}">{$link['title']}</a></li>
        {/foreach} 
    </ul>
</p>
</div>
    <div>
        <iframe height="700" width="700" id="importpage" src="">
    </div>    