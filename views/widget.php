<?php

//echo 'BOOM!';

    $cm_api = get_option('cm_api_option');
    $cm_list = get_option('cm_list_id_option');

    $auth = array('api_key' => $cm_api);
    $wrap = new CS_REST_Lists($cm_list, $auth);

    $result = $wrap->get();
    $stats_result = $wrap->get_stats();

    if($result->was_successful()) {

        $total_active_subscribers = $stats_result->response->TotalActiveSubscribers;
        $new_sub_today = $stats_result->response->NewActiveSubscribersToday;
        $new_sub_yesterday = $stats_result->response->NewActiveSubscribersYesterday;
        $new_sub_this_week = $stats_result->response->NewActiveSubscribersThisWeek;
        $new_sub_this_month = $stats_result->response->NewActiveSubscribersThisMonth;
        $new_sub_this_year = $stats_result->response->NewActiveSubscribersThisYear;

        $total_unsubscribers = $stats_result->response->TotalUnsubscribes;
        $un_sub_today = $stats_result->response->UnsubscribesToday;
        $un_sub_yesterday = $stats_result->response->UnsubscribesYesterday;
        $un_sub_this_week = $stats_result->response->UnsubscribesThisWeek;
        $un_sub_this_month = $stats_result->response->UnsubscribesThisMonth;
        $un_sub_this_year = $stats_result->response->UnsubscribesThisYear;

        echo "<div class=\"\">";
        echo "<p>" .$result->response->Title. "</p>";
        echo "<p>Total Subscribers: " .$total_active_subscribers. "</p>";
        echo "</div>";

        echo "<div class=\"stats\">";
        echo "<div class=\"sub-stats\">";
        echo "<h4>Subscribers</h4>";
        echo "<p><span>Today</span> " .$new_sub_today. "</p>";
        echo "<p><span>Yesterday</span> " .$new_sub_yesterday. "</p>";
        echo "<p><span>This Week</span> " .$new_sub_this_week. "</p>";
        echo "<p><span>This Month</span> " .$new_sub_this_month. "</p>";
        echo "<p><span>This Year</span> " .$new_sub_this_year. "</p>";
        echo "</div>";

        echo "<div class=\"sub-stats\">";
        echo "<h4>Unsubscribers</h4>";
        echo "<p><span>Today</span> " .$un_sub_today. "</p>";
        echo "<p><span>Yesterday</span> " .$un_sub_yesterday. "</p>";
        echo "<p><span>This Week</span> " .$un_sub_this_week. "</p>";
        echo "<p><span>This Month</span> " .$un_sub_this_month. "</p>";
        echo "<p><span>This Year</span> " .$un_sub_this_year. "</p>";
        echo "</div>";
        echo "</div>";


    } else {
        echo '<p class="cm-error">';
        echo 'Please add your correct credentials <span>to get the ball rolling.</span>';
        echo '</p>';

    }
