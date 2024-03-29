<html>
<head>
    <meta charset="utf-8">

    <?php Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . "/css/statistics/ci.css?dl=1"); ?>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>
<style>
    #lefttorun {
        position: fixed;
        padding: .7em 1em 1em;
        font-size: 0px;
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
</style>

<script>
    var prefix = '/statistics/SeleniumTestsAuth?params=';
    var xml = '/httpAuth/app/rest/buildTypes/id:bt3/builds/';
    function ci_call(url, result) {
        $.ajax({
            url:prefix + url,
            success: result
        });

    }
    function update_tc(){
        ci_call(xml, function (data) {
            build = $(data).find('build:eq(0)')
            if ((build.attr('status') == 'FAILURE') || (build.attr('status') == 'ERROR')) {
                $('body').removeClass('success');
                $('body').addClass('failure');
                var first_failure = $(data).find('build[status=SUCCESS]:eq(0)').prev().attr('href');
                ci_call(first_failure, function(data) {
                    var changes_url = $(data).find('changes').attr('href');
                    ci_call(changes_url, function(data) {
                        var usernames = {};
                        $(data).find('change').each(function() {
                            ci_call($(this).attr('href'), function(data) {
                                usernames[$(data).find('change').attr('username')] = true;
                                $('#author').html(Object.keys(usernames).join(' or '));
                            })
                        })
                    })
                })
            } else {
                $('body').removeClass('failure');
                $('body').addClass('success');
                var first_failure = $(data).find('build[status!=SUCCESS]:eq(0)').prev().attr('href');
                ci_call(first_failure, function(data) {
                    var changes_url = $(data).find('changes').attr('href');
                    ci_call(changes_url, function(data) {
                        $(data).find('change').each(function() {
                            ci_call($(this).attr('href'), function(data) {
                                if ($(data).find('change').attr('username') !== undefined) {
                                    $('#author').html('Fixed by ' + $(data).find('change').attr('username'));
                                } else {
                                    $('#author').html('');
                                }
                            })
                        })
                    })
                })
            };
            ci_call(build.attr('href'), function(data) {
                $('#status').html($(data).find('statusText').text());
            })
        })

    }

    update_tc();
    setInterval(update_tc, 30000);

</script>

<div id="status"></div>
<div id="author"></div>
</body>
