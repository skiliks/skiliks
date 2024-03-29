
<html>
<head>
    <?php Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . "/css/statistics/ci.css?dl=1"); ?>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="1200">
    <style>
        #lefttorun {
            position: fixed;
            padding: .7em 1em 1em;
            font-size: 60px;
            right: 0;
            bottom: 0;
            color: black;
            background: white;
            letter-spacing: -1px;
            border-top-left-radius: 20px;
        }
        #lefttorun > big {
            font-size: 2em;
        }

        #status {
            padding-top: 0;
        }
    </style>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script>
        function updateValue() {
            $.ajax({
                url: 'http://test.skiliks.com/cheat/zoho/getUsageValue',
                success: function(data) {
                    $('#status').text(data);
                }
            });
        }
        updateValue();

        setInterval(updateValue, 30000);

    </script>
</head>
<body>
<div id="status"></div>
<div id="author"></div>
</body>
