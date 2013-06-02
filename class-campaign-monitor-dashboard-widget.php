<?php
/**
 * Campaign Monitor Dashboard.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */

/**
 * Plugin class.
 *
 * @package CampaignMonitorDashboard
 * @author  Jake Bresnehan <hello@jakebresnehan.com>
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
            'campaign_monitor_widget', // Base ID
            'Campaign Monitor', // Name
            array( 'description' => __( 'A signup form for your site.', 'text_domain' ), ) // Args
        );

        add_action ( 'init', array ( $this, 'ajax_receiver' ) );

    }

    public function process_add_transfer() {
        if ( empty($_POST) || !wp_verify_nonce('security-code-here','add_transfer') ) {
            echo 'You targeted the right function, but sorry, your nonce did not verify.';
            die();
        } else {
            // do your function here
            wp_redirect($redirect_url_for_non_ajax_request);
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;
        if ( ! empty( $title ) )
            echo $before_title . $title . $after_title;
        echo __( '<p>Name</p>', 'text_domain' );
        echo __( '<p>Email</p>', 'text_domain' );
        echo __( '<p>Submit</p>', 'text_domain' );

        $cm_api = get_option('cm_api_option');
        echo ('<p>CM API: ');
        var_dump($cm_api);
        echo ('</p>');

         $cm_list = get_option('cm_list_id_option');
        echo ('<p> CM List ID: ');
        var_dump($cm_list);
        echo ('</p>');

        // var_dump($_POST['cm_email']);
        // var_dump($_POST['cm_action']);

        if ( isset ( $instance['pretext'] ) ) {

            echo ('<p>' .$instance['pretext']. '</p>');

        }

        ?>

        <form method="POST" id="cm_ajax_form_<?php echo $this->number; ?>" action="">

            <input type="hidden" name="cm_action" value="subscribe">

            <p><label for="cm_email">Email: </label>
            <input type="email" name="cm_email" /></p>

            <input type="submit" name="cm_submit" value="Submit">

        </form>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery('form#cm_ajax_form_<?php echo $this->number; ?> input:submit').click(function() {

                    jQuery.ajax(
                        { type: 'POST',
                          data: jQuery('form#cm_ajax_form_<?php echo $this->number; ?>').serialize()+'&cm_ajax_response=ajax',
                          success: function(data) {

                                        console.log("DATA \n" + data);

                                        if (data == 'SUCCESS') {
                                            console.log('success');
                                        } else {
                                            console.log('fail');
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
        $instance['pretext'] = $new_instance['pretext'];
        // $instance['account_api_key'] = strip_tags($new_instance['account_api_key']);
        // $instance['list_api_key'] = strip_tags($new_instance['list_api_key']);

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
            $title = __( 'Signup to our Email', 'text_domain' );
        }

        if ( isset( $instance[ 'pretext' ] ) ) {
            $pretext = $instance[ 'pretext' ];
        }
        else {
            $pretext = __( 'pretext boom', 'text_domain' );
        }
        ?>

        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

         <p>
        <label for="<?php echo $this->get_field_id( 'pretext' ); ?>"><?php _e( 'Pretext:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'pretext' ); ?>" name="<?php echo $this->get_field_name( 'pretext' ); ?>" type="text" value="<?php echo esc_attr( $pretext ); ?>" />
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


        if ( ! isset ( $_POST['cm_action'] ) )
            return;

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

        //$settings = get_option ( $this->option_name );

        $wrap = new CS_REST_Subscribers($cm_list, $cm_api);

        $result = $wrap->add(array(
            'EmailAddress' => $_POST['cm_email'],
            //'Name' => 'JakeyB',
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

        // echo "Result of POST /api/v3/subscribers/{list id}.{format}\n<br />";
        // if($result->was_successful()) {
        //     echo "Subscribed with code ".$result->http_status_code;
        // } else {
        //     echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
        //     var_dump($result->response);
        //     echo '</pre>';
        // }

        return;

    }

}
