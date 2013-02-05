<script type="text/template" id="window_template">
    <section class="sim-window-container">
        <header>
            <div class="header-inner">
                <h1><@= title @></h1>

                <ul class="btn-window">
                    <@ if(isDisplaySettingsButton){ @>
                    <li>
                        <button class="btn-set">&nbsp;</button>
                    </li>
                    <@ } @>
                    <@ if(isDisplayCloseWindowsButton){ @>
                    <li>
                        <button class="btn-cl win-close">&nbsp;</button>
                    </li>
                    <@ } @>
                </ul>
            </div>
        </header>

        <div class="sim-window-content">
        </div>
    </section>
</script>