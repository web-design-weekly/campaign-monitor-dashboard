<?php

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

		echo '<div class="dashboard-widget">';
		echo '<h4>' . $result->response->Title . '</h4>';

		echo '<p class="total-subs">' . __('Total Subscribers: ','campaign-monitor-dashboard') . $total_active_subscribers . '</p>';

		echo '<div class="stats">';
		echo '<div class="sub-stats">';
		echo '<h5>' . __('Subscribers','campaign-monitor-dashboard') . '</h5>';
		echo '<p><span>' . __('Today','campaign-monitor-dashboard') . '</span>' . $new_sub_today . '</p>';
		echo '<p><span>' . __('Yesterday','campaign-monitor-dashboard') . '</span>' . $new_sub_yesterday . '</p>';
		echo '<p><span>' . __('This Week','campaign-monitor-dashboard') .'</span>' . $new_sub_this_week . '</p>';
		echo '<p><span>' . __('This Month','campaign-monitor-dashboard') . '</span>' . $new_sub_this_month . '</p>';
		echo '</div>';

		echo '<div class="sub-stats">';
		echo '<h5>' . __('Unsubscribers','campaign-monitor-dashboard') . '</h5>';
		echo '<p><span>' . __('Today','campaign-monitor-dashboard') . '</span>' . $un_sub_today . '</p>';
		echo '<p><span>' . __('Yesterday','campaign-monitor-dashboard') . '</span>' . $un_sub_yesterday . '</p>';
		echo '<p><span>' . __('This Week', 'campaign-monitor-dashboard') . '</span>' . $un_sub_this_week . '</p>';
		echo '<p><span>' . __('This Month','campaign-monitor-dashboard') . '</span>' . $un_sub_this_month . '</p>';
		echo '</div>';

		echo '</div>';
		echo '</div>';


	} else {
		echo '<p class="cm-error">' . __('Please add your correct credentials ', 'campaign-monitor-dashboard') . '<span>' . __('to get the ball rolling.', 'campaign-monitor-dashboard') . '</span></p>';
	}