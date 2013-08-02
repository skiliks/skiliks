<script>
    update_tc('.statistic-php-unit', '/httpAuth/app/rest/buildTypes/id:bt3/builds/');
    update_tc('.statistic-selenium-site', '/httpAuth/app/rest/buildTypes/id:bt6/builds/');
    update_tc('.statistic-selenium-assessment', '/httpAuth/app/rest/buildTypes/id:bt4/builds/');
    update_stat('.statistic-free-disk-space', '/admin_area/statistics/free-disk-space');
</script>

<div class="row-fluid">
<table class="table-statistics">
   <tr>
       <td class="statistic-php-unit">
           <div>Develop</div>
           <div class="status"></div>
           <div class="author"></div>
       </td>
       <td class="statistic-selenium-site">
           <div>Selenium Engine & Site</div>
           <div class="status"></div>
           <div class="author"></div>
       </td>
   </tr>
   <tr>
       <td class="statistic-selenium-assessment">
           <div>Selenium Assessment</div>
           <div class="status"></div>
           <div class="author"></div>
       </td>
       <td class="statistic-free-disk-space">
           <div>Free disk space</div>
           <div class="status"></div>
           <div class="author"></div>
       </td>
   </tr>
   <tr>
       <td>5</td>
       <td>6</td>
   </tr>
    <tr>
        <td>7</td>
        <td>8</td>
    </tr>
</table>
</div>