<?php
if (!defined('ABSPATH')) {
    return;
}

global $woocommerce, $post;

$post_id = get_the_ID();
?>

<div id="bbPress_product_data_tab" class="panel woocommerce_options_panel">

    <?php
    /*
      P O P U L A T I N G - GETTING AND CHECKING THE DATA VALUES FOR EACH TYPE
     */

      $input_global_get = (int) get_post_meta($post_id, '_eh_bb_tab_global', true);
    $input_global_set = !empty($input_global_get) ? $input_global_get : true; // set default value

    $my_selector_get = (int) get_post_meta($post_id, '_eh_bb_tab_forum', true);
    $my_selector_data = !empty($my_selector_get) ? $my_selector_get : '0'; // set default value

    $global_reply_link_get = get_option('wc_bb_settings_tab_show_reply_link');
    $global_reply_link_set = !empty($global_reply_link_get) ? $global_reply_link_get : '0';

    $global_topic_get = get_option('wc_bb_settings_tab_view_topic');
    $global_topic_set = !empty($global_topic_get) ? $global_topic_get : '1';

    $global_view_images_get = get_option('wc_bb_settings_tab_view_post_images');
    $global_view_images_set = !empty($global_view_images_get) ? $global_view_images_get : '0';





    if ($input_global_get === 1) {

        $input_post_count = (int) get_option('wc_bb_settings_tab_post_count');
        $in_post_count = !empty($input_post_count) ? $input_post_count : '10'; // set default value
    } else {

        $input_post_count = (int) get_post_meta($post_id, '_eh_bb_post_count', true);
        $in_post_count = !empty($input_post_count) ? $input_post_count : '5'; // set default value
    }




    $actual_link = site_url() . "/?post_type=forum&#038;p=" . $my_selector_data;
    ?>

</div>
<style type="text/css">

    .panel-body {
        padding: 0px;
    }
    .media-list {
        padding-left: 0;
        list-style: none;
    }
    .media,
    .media-body {
        overflow: hidden;
        zoom: 1;
    }
    .media,
    .media .media {
        margin-top: 3px;
    }
    .media:first-child {
        margin-top: 0;
    }
    .media-object {
        display: block;
    }
    .media-heading {
        margin: 0 0 3px;
    }
    .media > .pull-left {
        margin-right: 5px;
    }
    .media > .pull-right {
        margin-left: 5px;
    }
    .media-list {
        padding-left: 0;
        list-style: none;
        margin: 0 0 0 0;
    }
    .media-object {
        display: block;
        width:50px;
        height:50px;
    }
    .img-circle {
        border-radius: 30%;
    }
    .eh-bb-pro-tab-img-circle{
        border-radius: 30%;
    }
</style>

<div style="width:100%;line-height:9px;">
    <center>
        <a href="<?php echo $actual_link . "#bbp_topic_title"; ?>" target="_blank" class=" single_add_to_cart_button button alt bbpt-comments" style="width:100%;"><?php _e('New Comment', 'eh_bb_import_export'); ?></a>
    </center>
    <br/>
    <hr />
</div>

<div>

    <div class="panel-body">
        <ul class="media-list">

            <?php
// $my_selector_data is forum id

            global $wpdb;
            $count = 0;
            $author_count = 0;
            $author_count1 = 0;
            $query = "SELECT count(*) FROM $wpdb->posts WHERE post_type ='topic' and post_status='publish' and post_parent= '" . $my_selector_data . "'";
            $author_count = $wpdb->get_var($query);
            if ($author_count != '0') {
                $query = "SELECT ID FROM $wpdb->posts  WHERE post_type ='topic' and post_status='publish' and post_parent= '" . $my_selector_data . "' ORDER BY post_date DESC";
                $author = $wpdb->get_col($query);
                foreach ($author as $key => $value) {
                    if ($key < $in_post_count) {
                        if ($count < $in_post_count) {
                            $usr_query = "SELECT count(*) FROM $wpdb->users u INNER JOIN $wpdb->posts p ON u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author[$key];
                            $usr_count = $wpdb->get_var($usr_query);
                            if ($usr_count != 0) {
                                $usr_email_query = "SELECT user_email FROM $wpdb->users u INNER JOIN $wpdb->posts p ON  u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author[$key];
                                $usr_email = $wpdb->get_var($usr_email_query);
                                $usr_name_query = "SELECT display_name FROM $wpdb->users u INNER JOIN $wpdb->posts p ON u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author[$key];
                                $usr_name = $wpdb->get_var($usr_name_query);
                            } else {
                                $usr_email = get_post_meta($author[$key], '_bbp_anonymous_email', true);
                                $usr_name = get_post_meta($author[$key], '_bbp_anonymous_name', true);
                            }
                            $post_content_query = "SELECT post_content FROM $wpdb->posts WHERE ID =" . $author[$key];
                            $post_content = $wpdb->get_var($post_content_query);
                            $post_date_query = "SELECT post_date FROM $wpdb->posts WHERE ID =" . $author[$key];
                            $post_date = $wpdb->get_var($post_date_query);
                            $post_date = time_elapsed_string($post_date);
                            ?>
                            <li class="media">

                                <div class="media-body">

                                    <div class="media">
                                        <a class="pull-left" href="#">
                                            <?php echo get_avatar($usr_email, 50); ?>

                                        </a>

                                        <div class="media-body" >
                                            <p ><b style="color:#1e8cbe;padding-top: 5px;padding-bottom: 3px;"><?php echo $usr_name; ?></b>  <small class="text-muted" ><?php echo ' - ' . $post_date; ?></small>
                                            </p>

                                            <?php
                                            $text = $post_content;
                                            if ( $global_view_images_set === 'yes')
                                            {

                                                $reg_exUrl = '/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i';

                                                if (preg_match($reg_exUrl, $text, $image)) {

                                                    echo preg_replace($reg_exUrl, '<a href="' . $image['src'] . '" rel="nofollow" target="_blank">' . $image['src'] . '</a>', $text);
                                                } else {

                                                                // if no urls in the text just return the text
                                                    echo $text;
                                                }

                                            }
                                            else
                                            {
                                                echo $text;
                                            }


                                            ?>

                                            <br />
                                            <div style="height:25px">
                                                <?php if ($global_topic_set === 'yes') { ?>
                                                <small class="text-muted" ><a href="<?php echo site_url() . '/?post_type=topic&#038;p=' . $author[$key]; ?>" target="_blank">View Topic</a> 
                                                    <?php } else { ?>
                                                    <small class="text-muted" > 
                                                        <?php } ?>
                                                        <?php if ($global_reply_link_set === '1') { ?>
                                                        | <a href="<?php echo site_url() . '/?post_type=topic&#038;p=' . $author[$key] . '#bbp_reply_content'; ?>" target="_blank">Reply</a>
                                                        <?php } if ($global_reply_link_set === '2' && get_current_user_id() > 0) { ?>
                                                        | <a href="<?php echo site_url() . '/?post_type=topic&#038;p=' . $author[$key] . '#bbp_reply_content'; ?>" target="_blank">Reply</a>
                                                        <?php } ?>
                                                    </small>
                                                </div>
                                                <hr />
                                            </div>
                                        </div>

                                    </li>

                                    <?php
                                    $count++;

                                    $query = "SELECT count(*) FROM $wpdb->posts WHERE post_type ='reply' and post_status='publish' and post_parent= '" . $author[$key] . "'";
                                    $author_count1 = $wpdb->get_var($query);
                                    if ($author_count1 != '0') {
                                        $query = "SELECT ID FROM $wpdb->posts WHERE post_type ='reply' and post_status='publish' and post_parent= '" . $author[$key] . "' ORDER BY post_date ASC";
                                        $author1 = $wpdb->get_col($query);
                                        foreach ($author1 as $key1 => $value1) {
                                            if ($key1 < $in_post_count - 1) {
                                                if ($count < $in_post_count) {
                                                    $usr_query = "SELECT count(*) FROM $wpdb->users u INNER JOIN $wpdb->posts p ON u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author1[$key1];
                                                    $usr_count = $wpdb->get_var($usr_query);
                                                    if ($usr_count != 0) {
                                                        $usr_email_query = "SELECT user_email FROM $wpdb->users u INNER JOIN $wpdb->posts p ON u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author1[$key1];
                                                        $usr_email = $wpdb->get_var($usr_email_query);
                                                        $usr_name_query = "SELECT display_name FROM $wpdb->users u INNER JOIN $wpdb->posts p ON u.id = p.post_author WHERE p.post_status='publish' and p.ID =" . $author1[$key1];
                                                        $usr_name = $wpdb->get_var($usr_name_query);
                                                    } else {
                                                        $usr_email = get_post_meta($author1[$key1], '_bbp_anonymous_email', true);
                                                        $usr_name = get_post_meta($author1[$key1], '_bbp_anonymous_name', true);
                                                    }
                                                    $post_content_query = "SELECT post_content FROM $wpdb->posts WHERE ID =" . $author1[$key1];
                                                    $post_content = $wpdb->get_var($post_content_query);
                                                    $post_date_query = "SELECT post_date FROM $wpdb->posts WHERE ID =" . $author1[$key1];
                                                    $post_date = $wpdb->get_var($post_date_query);
                                                    $post_date = time_elapsed_string($post_date);
                                                    ?>
                                                    <li class="media" style="padding-left:60px;">

                                                        <div class="media-body">

                                                            <div class="media">
                                                                <a class="pull-left" href="#">
                                                                    <?php echo get_avatar($usr_email, 50); ?>
                                                                </a>
                                                                <div class="media-body" >
                                                                    <p ><b style="color:#1e8cbe;padding-top: 5px;padding-bottom: 3px;"><?php echo $usr_name; ?></b>  <small class="text-muted" ><?php echo ' - ' . $post_date; ?></small>
                                                                    </p>
                                                                    <?php
                                                                    $text = $post_content;

                                                                    if ( $global_view_images_set === 'yes')
                                                                    {

                                                                        $reg_exUrl = '/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i';

                                                                        if (preg_match($reg_exUrl, $text, $image)) {

                                                                            echo preg_replace($reg_exUrl, '<a href="' . $image['src'] . '" rel="nofollow" target="_blank">' . $image['src'] . '</a>', $text);
                                                                        } else {

                                                                // if no urls in the text just return the text
                                                                            echo $text;
                                                                        }

                                                                    }
                                                                    else
                                                                    {
                                                                        echo $text;
                                                                    }


                                                            /*
                                                            // The Regular Expression filter
                                                            $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

                                                            // Check if there is a url in the text
                                                            if (preg_match($reg_exUrl, $text, $url)) {

                                                                // make the urls hyper links
                                                                echo preg_replace($reg_exUrl, '<a href="' . $url[0] . '" rel="nofollow">' . $url[0] . '</a>', $text);
                                                            } else {

                                                                // if no urls in the text just return the text
                                                                echo $text;
                                                            }
                                                            */
                                                            ?>
                                                            <br />

                                                            <hr />
                                                        </div>
                                                    </div>

                                                </div>
                                            </li>

                                            <?php
                                        }
                                        $count++;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {

            }

            function time_elapsed_string($datetime, $full = false) {
                $now = new DateTime;
                $ago = new DateTime($datetime);
                $diff = $now->diff($ago);

                $diff->w = floor($diff->d / 7);
                $diff->d -= $diff->w * 7;

                $string = array(
                    'y' => 'year',
                    'm' => 'month',
                    'w' => 'week',
                    'd' => 'day',
                    'h' => 'hour',
                    'i' => 'minute',
                    's' => 'second',
                    );
                foreach ($string as $k => &$v) {
                    if ($diff->$k) {
                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                    } else {
                        unset($string[$k]);
                    }
                }

                if (!$full)
                    $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . ' ago' : 'just now';
            }
            ?> 



        </ul>
    </div>


</div>



<?php
$query = "SELECT count(*) FROM $wpdb->postmeta WHERE meta_key='_bbp_forum_id' and meta_value = '" . $my_selector_data . "'";
$total_count = $wpdb->get_var($query);

if ($in_post_count < $total_count) {
    ?>
    <br/>
    <div style="width:100%;line-height:9px;">
        <center>
            <a href="<?php echo $actual_link; ?>" target="_blank" class="single_add_to_cart_button button alt bbpt-comments" style="width:100%;"><?php _e('Load More Comments', 'eh_bb_import_export'); ?></a>
        </center>
    </div>        
    <?php } ?>