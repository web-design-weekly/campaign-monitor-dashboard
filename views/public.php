<?php
/**
 * Represents the view for the public-facing component of the plugin.
 *
 * This typically includes any information, if any, that is rendered to the
 * frontend of the theme when the plugin is activated.
 *
 * @package   PluginName
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Your Name or Company Name
 */
?>
<!-- This file is used to markup the public facing aspect of the plugin. -->

<!--

/* add sub to list */
 <?php
    $auth = array('api_key' => $cm_api);

    $wrap = new CS_REST_Subscribers($cm_list, $auth);
    $result = $wrap->add(array(
        'EmailAddress' => 'jakebresnehan+cmplug@gmail.com',
        'Name' => 'JakeyB',
        'Resubscribe' => true
    ));

    echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
    if($result->was_successful()) {
        echo "Subscribed with code ".$result->http_status_code;
    } else {
        echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        var_dump($result->response);
        echo '</pre>';
    }
?>
 -->

 <p>Boom</p>