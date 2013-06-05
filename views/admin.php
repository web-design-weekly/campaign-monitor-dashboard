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

        <p>Extra Content</p>
        <input type="text" name="extra_content_option" size="40" value="<?php echo get_option('extra_content_option'); ?>" />

        <p>API</p>
        <input type="text" name="cm_api_option" size="40" value="<?php echo get_option('cm_api_option'); ?>" />

        <p>List ID</p>
        <input type="text" name="cm_list_id_option" size="40" value="<?php echo get_option('cm_list_id_option'); ?>" />

        <p>Client ID</p>
        <input type="text" name="cm_client_id_option" size="40" value="<?php echo get_option('cm_client_id_option'); ?>" />



        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

<?php

    $cm_api = get_option('cm_api_option');
    $cm_list = get_option('cm_list_id_option');
    $cm_client_id = get_option('cm_client_id_option');

    $auth = array('api_key' => $cm_api);
    $wrap = new CS_REST_Lists($cm_list, $auth);

    $result = $wrap->get();
    $stats_result = $wrap->get_stats();
    $segments_result = $wrap->get_segments();

    if($result->was_successful()) {
        echo "<div class=\"sub-stats\">";
        echo "<p>List - " .$result->response->Title. "</p>";
        echo "<p>Total Subscribers - " .$stats_result->response->TotalActiveSubscribers. "</p>";
        echo "<p>active - " .$result->response->Title. "</p>";
        echo "</div>";
    } else {
        echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        var_dump($result->response);
    }
    echo '</pre>';

?>



<div class="clear">


    <canvas id="canvas" width="400" height="400"></canvas>

<?php

//$lists = array();

$wrap = new CS_REST_Lists($cm_list, $auth);

$result = $wrap->get_active_subscribers(date('Y-m-d', strtotime('-30 days')), 1, 300, 'date', 'asc');

//$result = $wrap->get_active_subscribers(date('Y-m-d', strtotime('-30 days')),
//  page number, page size, order by, order direction);

echo "Result of GET /api/v3/lists/{ID}/active\n<br />";
if($result->was_successful()) {
    echo "Got subscribers\n<br /><pre>";

    $stack = array();
    $i = 1;
    $d = '';
    foreach($result->response->Results as $list) {

        $date = $list->Date;
        $pattern = '([^\s]+)';
        preg_match($pattern, $date, $matches);
       // echo "<p>Date - $matches[0]</p>";
        $d = $matches[0];
        array_push($stack, $d);

        $i++;
    }

    //$array = array("$stack");
    print_r(array_count_values($stack));

    //var_dump($stack);
   // var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';

?>



<?php


// $cm_api = get_option('cm_api_option');
$cm_client_id = get_option('cm_client_id_option');

$wrap = new CS_REST_Clients(
    $cm_client_id,
    $auth);

$result = $wrap->get_lists();

echo "Result of /api/v3/clients/{id}/lists\n<br />";
if($result->was_successful()) {
    echo "Got lists\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';
?>


<?php
    $extra_content = get_option('extra_content_option');
    echo ('<p>Extra Content: ');
    var_dump($extra_content);
    echo ('</p>');

    $cm_api = get_option('cm_api_option');
    echo ('<p>CM API: ');
    var_dump($cm_api);
    echo ('</p>');

    $cm_list = get_option('cm_list_id_option');
    echo ('<p> CM List ID: ');
    var_dump($cm_list);
    echo ('</p>');

    $cm_client_id = get_option('cm_client_id_option');
    echo ('<p> CM Client ID: ');
    var_dump($cm_client_id);
    echo ('</p>');
 ?>
 </div>