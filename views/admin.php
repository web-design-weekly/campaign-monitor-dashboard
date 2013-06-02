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

    <form method="post" action="options.php">
        <?php settings_fields( 'option-group' ); ?>

        <p>Extra Content</p>
        <input type="text" name="extra_content_option" size="80" value="<?php echo get_option('extra_content_option'); ?>" />

        <p>API</p>
        <input type="text" name="cm_api_option" size="80" value="<?php echo get_option('cm_api_option'); ?>" />

        <p>List ID</p>
        <input type="text" name="cm_list_id_option" size="80" value="<?php echo get_option('cm_list_id_option'); ?>" />

        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>

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
 ?>



 <?php

$auth = array('api_key' => $cm_api);
$wrap = new CS_REST_Lists($cm_list, $auth);

$result = $wrap->get();

echo "Result of GET /api/v3/lists/{ID}\n<br />";
if($result->was_successful()) {
    echo "Got list details\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';

?>

<?php
    $auth = array('api_key' => $cm_api);
    $wrap = new CS_REST_Lists($cm_list, $auth);

    $stats_result = $wrap->get_stats();
    $result = $wrap->get_segments();

    echo "Result of GET /api/v3/lists/{ID}/stats\n<br />";
    if($stats_result->was_successful()) {
        echo "Got list stats\n<br /><pre>";

        var_dump($stats_result->response->TotalActiveSubscribers);

        echo ('Total Active Subscriber - ' .$stats_result->response->TotalActiveSubscribers);

        var_dump($stats_result->response);

    } else {
        echo 'Failed with code '.$stats_result->http_status_code."\n<br /><pre>";
        var_dump($stats_result->response);
    }
    echo '</pre>';
?>

