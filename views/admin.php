<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */
?>

<div class="wrap">

    <?php screen_icon(); ?>
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <p>Your Campaign Monitor settings.</p>

    <form class="settings-form" method="post" action="options.php">
        <?php settings_fields( 'option-group' ); ?>

        <p>API</p>
        <input type="text" name="cm_api_option" size="40" value="<?php echo get_option('cm_api_option'); ?>" />

        <p>List ID</p>
        <input type="text" name="cm_list_id_option" size="40" value="<?php echo get_option('cm_list_id_option'); ?>" />

        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

<?php

    $cm_api = get_option('cm_api_option');
    $cm_list = get_option('cm_list_id_option');

    $auth = array('api_key' => $cm_api);
    $wrap = new CS_REST_Lists($cm_list, $auth);

    $result = $wrap->get();
    $stats_result = $wrap->get_stats();
    $segments_result = $wrap->get_segments();

    if($result->was_successful()) {
        echo "<div class=\"sub-stats\">";
        echo "<p>List - " .$result->response->Title. "</p>";
        echo "<p>Total Subscribers - " .$stats_result->response->TotalActiveSubscribers. "</p>";
        echo "</div>";
        //var_dump($result->response);
    } else {
        echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        var_dump($result->response);
    }
    echo '</pre>';

?>



<div class="clear">

<?php

$wrap = new CS_REST_Lists($cm_list, $auth);

function get_actives($auth, $page_number)
{
    // last 365 days
    $date_from = date('Y-m-d', strtotime('-365 days'));

    // params: start date, page number, page size, order by, order direction
    $result = $auth->get_active_subscribers($date_from, $page_number, 1000, 'date', 'asc');
    return $result;
}


// get the first page of results
// currently assuming if this succeeds, then all calls for subsequent pages will also succeed
$result = get_actives($wrap, 1);

echo "Result of GET /api/v3/lists/{ID}/active\n<br />";
if($result->was_successful()) {
    echo "Got subscribers\n<br /><pre>";

    echo("ResultsOrderedBy ".$result->response->ResultsOrderedBy."\n");
    echo("OrderDirection ".$result->response->OrderDirection."\n");
    echo("PageNumber ".$result->response->PageNumber."\n");
    echo("PageSize ".$result->response->PageSize."\n");
    echo("RecordsOnThisPage ".$result->response->RecordsOnThisPage."\n");
    echo("TotalNumberOfRecords ".$result->response->TotalNumberOfRecords."\n");
    echo("NumberOfPages ".$result->response->NumberOfPages."\n");


    $num_pages = $result->response->NumberOfPages;
    $stack = array();

    // we've already got page 1; loop from 1 until < num_pages
    for($i = 1; $i < $num_pages; $i++) {
        foreach($result->response->Results as $list) {

            $date = $list->Date;
            // echo $date."\n";

            // // just get the date part of the $list->Date?
            // $pattern = '([^\s]+)';
            // preg_match($pattern, $date, $matches);
            // $d = $matches[0];

            // just get the month part of the $list->Date
            $d = substr($date, 0, 7);

            array_push($stack, $d);
        }

        $next_page = $i+1;
        $result = get_actives($wrap, $next_page);
    }

    // $stack now contains the results for all active subscribers

    // print the data
    print_r(array_count_values($stack));
    $graph_data = array_count_values($stack);
    
    $php_keys = array_keys($graph_data);
     $js_keys = json_encode($php_keys);

    $php_vals = array_values($graph_data);
     $js_vals = json_encode($php_vals);

} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';

?>

<script type="text/javascript">

jQuery(function () {
  var lineChartData = {
            labels : <?php echo $js_keys; ?>,
            datasets : [
                {
                    fillColor : "rgba(151,187,205,0.5)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    data : <?php echo $js_vals; ?>
                }
            ]

        }

    var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Line(lineChartData);
});

</script>

 <canvas id="canvas" width="700" height="400"></canvas>
