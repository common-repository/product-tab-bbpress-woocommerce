<?php
if (!defined('ABSPATH')) {
    return;
}

add_filter('woocommerce_product_data_tabs', 'add_bbPress_product_data_tab_setting', 99, 1);

function add_bbPress_product_data_tab_setting($product_data_tabs) {
    $product_data_tabs['bbPress'] = array(
        'label' => __('bbPress Tab', 'eh_bb_product_tab'), // translatable
        'target' => 'eh_bbPress_product_data', // translatable
    );
    return $product_data_tabs;
}

// Creates the panel for selecting product options
add_action('woocommerce_product_write_panels', 'product_write_panel');

function product_write_panel() {
    global $woocommerce, $post;

    $post_id = get_the_ID();
    ?>

    <div id="eh_bbPress_product_data" class=" panel panel woocommerce_options_panel wc-metaboxes-wrapper">

        <div class="options_group wdm_custom_product">
            <?php
            /*
              P O P U L A T I N G - GETTING AND CHECKING THE DATA VALUES FOR EACH TYPE
             */
            $input_global_get = get_post_meta($post_id, '_eh_bb_tab_global', true);
            $input_global_set = !empty($input_global_get) ? $input_global_get : '1'; // set default value


            $input_text_get = get_post_meta($post_id, '_eh_bb_tab_title', true);
            $input_text_data = !empty($input_text_get) ? $input_text_get : 'Comments'; // set default value

            $input_priority_get = get_post_meta($post_id, '_eh_bb_tab_priority', true);
            $input_priority = !empty($input_priority_get) ? $input_priority_get : '50'; // set default value

            $input_post_count = get_post_meta($post_id, '_eh_bb_post_count', true);
            $input_post_count = !empty($input_post_count) ? $input_post_count : '5'; // set default value



            $my_selector_get = (int) get_post_meta($post_id, '_eh_bb_tab_forum', true);
            $my_selector_data = !empty($my_selector_get) ? $my_selector_get : '0'; // set default value

            /*
              THE 6 DIFFERENT FIELD TYPES
             */
            ?>



            <table class="form-table">

                <tr>

                    <td>
                        Select Forum
                    </td>
                    <td >

                        <select id="_my_selector" style="width:60%;"  name="_my_selector" class="wc-enhanced-select"   >
                            <option title="Enable/Disable bbPress Tab" value="0">--- Choose Forum ---</option>
    <?php
    $args = array(
        'posts_per_page' => -1,
        'post_type' => 'forum',
        'post_status' => 'publish',
        'suppress_filters' => true
    );
    $products = get_posts($args);
    foreach ($products as $product) {
        $data_value = strlen($product->post_title) > 50 ? substr($product->post_title, 0, 50) . "..." : $product->post_title;
        $data_value1 = $product->post_title;
        if ($product->ID === $my_selector_data) {
            echo '<option selected="true" title="' . $data_value1 . '" value="' . $product->ID . '">#' . $product->ID . ' - ' . $data_value . '</option>';
        } else {
            echo '<option title="' . $data_value1 . '" value="' . $product->ID . '">#' . $product->ID . ' - ' . $data_value . '</option>';
        }
    }
    ?>
                        </select><span class="woocommerce-help-tip" data-tip="Selected forum will be displayed on the product page"></span>               
                    </td>

                </tr>
                <tr>
                    <td>
                        Use Global Settings
                    </td>

                    <td>
                            <?php if ($input_global_set === '1') { ?>
                            <input type="checkbox"  checked='true' id='_input_global' name="_input_global"  />
    <?php } else { ?>
                            <input type="checkbox"  id='_input_global' name="_input_global"  />

    <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td  id="td1">
                        Tab Title
                    </td>

                    <td id="td2">
                        <input type="text" id="_input_text" value='<?php echo $input_text_data; ?>' name="_input_text"  placeholder="<?php _e('Comments', 'eh_bb_product_tab'); ?>" class="input-text" style="width:60%;" />
                    </td>
                </tr>
                <tr>
                    <td id="td3">
                        Tab Priority
                    </td>

                    <td id="td4">
                        <input type="number" id="_input_text_priority" value='<?php echo $input_priority; ?>' min="1" name="_input_text_priority"  placeholder="<?php _e('Tab Priority', 'eh_bb_product_tab'); ?>" class="input-text" style="width:60%;" />
                    </td>
                </tr>
                <tr>
                    <td id="td5">
                        Post Count
                    </td>

                    <td id="td6">
                        <input type="number" id="_input_postcount" value='<?php echo $input_post_count; ?>' min="1" name="_input_postcount"  placeholder="<?php _e('Post Count', 'eh_bb_product_tab'); ?>" class="input-text" style="width:60%;" />
                        <span class="woocommerce-help-tip" data-tip="Display count of the Posts/Comments"></span> 

                    </td>

                </tr>      

            </table>

        </div> <!-- options group -->

        <script type="text/javascript">
            jQuery( function( $ ) {

            $( '#_input_global' ).on('change',function(){
            if ( $( this ).is( ':checked' ) ) {
            $("#td1").hide(); 
            $("#td2").hide(); 
            $("#td3").hide(); 
            $("#td4").hide();
            $("#td5").hide(); 
            $("#td6").hide();
            //$("#_input_postcount").attr("disabled", "disabled"); 

            } 
            else
            {
            $("#td1").show(); 
            $("#td2").show();
            $("#td3").show(); 
            $("#td4").show();
            $("#td5").show(); 
            $("#td6").show();								
            }

            }).change();

            });
        </script>
    </div>

    <?php
}

// Step 3 - Saving custom fields data of custom products tab metabox
add_action('woocommerce_process_product_meta', 'eh_bb_data_save_product_tabs');

function eh_bb_data_save_product_tabs($post_id) {

    // save the text field data
    $wc_textglobal = isset($_POST['_input_global']) ? true : false;
    update_post_meta($post_id, '_eh_bb_tab_global', $wc_textglobal);

    $wc_textimput = isset($_POST['_input_text']) ? $_POST['_input_text'] : 'Comments';
    update_post_meta($post_id, '_eh_bb_tab_title', $wc_textimput);

    $wc_textinput = isset($_POST['_input_text_priority']) ? $_POST['_input_text_priority'] : '50';
    update_post_meta($post_id, '_eh_bb_tab_priority', $wc_textinput);
    $wc_postcount = isset($_POST['_input_postcount']) ? $_POST['_input_postcount'] : '5';
    update_post_meta($post_id, '_eh_bb_post_count', $wc_postcount);




    // save the selector field data
    $wc_selector = (int) isset($_POST['_my_selector']) ? $_POST['_my_selector'] : '0';
    update_post_meta($post_id, '_eh_bb_tab_forum', $wc_selector);
}
