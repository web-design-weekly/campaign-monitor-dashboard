<?php
/**
 * Campaign Monitor Dashboard.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <jakebresnehan@gmail.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */

/**
 * Plugin class.
 *
 * @package CampaignMonitorDashboard
 * @author  Jake Bresnehan <jakebresnehan@gmail.com>
 */

/**
 * Adds Campaign_Monitor_Widget widget.
 */
class Campaign_Monitor_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'campaign_monitor_widget',
			'Campaign Monitor',
			array( 'description' => __( 'A sign up form for your site.', 'campaign-monitor-dashboard' ), )
		);

		add_action ( 'init', array ( $this, 'ajax_receiver' ) );

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		if ( isset ( $instance['subtitle'] ) ) {
			echo ('<p>' .$instance['subtitle']. '</p>');
		}

		?>

		<form method="POST" id="cm_ajax_form_<?php echo $this->number; ?>" action="">

			<input type="hidden" name="cm_action" value="subscribe">

			<p>
				<input type="email" name="cm_email" placeholder="Your email..."/>
			</p>

			<input type="submit" name="cm_submit" value="Submit">

		</form>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('form#cm_ajax_form_<?php echo $this->number; ?> input:submit').click(function() {

					jQuery.ajax({

						type: 'POST',
						data: jQuery('form#cm_ajax_form_<?php echo $this->number; ?>').serialize()+'&cm_ajax_response=ajax',

							success: function(data) {

								//console.log("DATA \n" + data);

								if (data == 'SUCCESS') {

									jQuery('.widget_campaign_monitor_widget .cm_fail_error').remove();
									jQuery('.widget_campaign_monitor_widget form').before('Thanks for subscribing!');
									jQuery('.widget_campaign_monitor_widget form').remove();

								} else {

									jQuery('.widget_campaign_monitor_widget .cm_fail_error').remove();
									jQuery('.widget_campaign_monitor_widget form').before('<p class="cm_fail_error" style="font-weight:bold; font-size: 12px;">Please provide a valid email address.</p>');

								}
							}
						}
					);
					return false;
					});
				});
		</script>

		<?php

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['subtitle'] = $new_instance['subtitle'];

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Sign up to our newsletter', 'campaign-monitor-dashboard' );
		}

		if ( isset( $instance[ 'subtitle' ] ) ) {
			$subtitle = $instance[ 'subtitle' ];
		}
		else {
			$subtitle = __( 'Receive updates on a regular basis.', 'campaign-monitor-dashboard' );
		}
		?>

			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','campaign-monitor-dashboard' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			 <p>
			<label for="<?php echo $this->get_field_id( 'subtitle' ); ?>"><?php _e( 'Subtitle:','campaign-monitor-dashboard' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'subtitle' ); ?>" name="<?php echo $this->get_field_name( 'subtitle' ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" />
			</p>

		<?php
	}


	/**
	 * Handle Ajax requests
	 *
	 */
	function ajax_receiver() {

?>
<?php

		if ( ! isset ( $_POST['cm_action'] ) ) {
			return;
		}

		switch ( $_POST['cm_action'] ) {

			case 'subscribe':
				$this->subscribe();
				break;

			default:
				break;
		}

		if( isset ( $_POST['cm_ajax_response'] ) && $_POST['cm_ajax_response'] == 'ajax' ) {
			 die();
		}

	}

	 /**
	 * Subscribe someone to a list
	 *
	 */
	public function subscribe() {

		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');

		$auth = array('api_key' => $cm_api);
		$wrap = new CS_REST_Subscribers($cm_list, $auth);


		$wrap = new CS_REST_Subscribers($cm_list, $cm_api);

		$result = $wrap->add(array(
			'EmailAddress' => $_POST['cm_email'],
			'Resubscribe' => true
		));

			if( isset ( $_POST['cm_ajax_response'] ) && $_POST['cm_ajax_response'] == 'ajax' ) {
				if ($result->was_successful()) {
					echo 'SUCCESS';
				} else {
					echo ('FAILED');
					echo ($result->response->Code).': ';
					echo ($result->response->Message);
				}
			} else {
				$this->result = $result->was_successful();
			}

		return;

	}

}