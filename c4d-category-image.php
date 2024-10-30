<?php
/*
Plugin Name: C4D Category Image
Plugin URI: http://coffee4dev.com/
Description: Set image for category.
Author: Coffee4dev.com
Author URI: http://coffee4dev.com/
Text Domain: c4d-category-image
Version: 2.0.0
*/

define('C4DCATEIMAGE_PLUGIN_URI', plugins_url('', __FILE__));

class C4D_category_image
{
    public $optionName = 'c4d_category_image';

	public function __construct()
	{
        if (is_admin()) {
            add_action( 'category_add_form_fields', array($this, 'metaFields'));
            add_action ( 'edit_category_form_fields', array($this,'metaFieldsTable'));
            add_action( 'created_category', array($this, 'save' ));
            add_action ( 'edited_category', array($this,'save'));
            add_action('admin_enqueue_scripts', array($this,'scripts'));    
            add_filter( 'plugin_row_meta', array($this,'plugin_row_meta'), 10, 2 );
        } else {
            add_action('wp_head', array($this,'background'), 100);    
        }
	}

    public function plugin_row_meta( $links, $file ) {

        if ( strpos( $file, basename(__FILE__) ) !== false ) {
            $new_links = array(
                'visit' => '<a href="http://coffee4dev.com">Visit Plugin Site</<a>',
                'forum' => '<a href="http://coffee4dev.com/forums/">Forum</<a>',
                'premium' => '<a href="http://coffee4dev.com">Premium Support</<a>'
            );
            
            $links = array_merge( $links, $new_links );
        }
        
        return $links;
    }

    public function scripts() {
        wp_enqueue_media();
        wp_enqueue_style( 'c4d-team-member-admin-style', C4DCATEIMAGE_PLUGIN_URI.'/assets/default.css' );
        wp_enqueue_script( 'c4d-team-member-admin-plugin-js', C4DCATEIMAGE_PLUGIN_URI.'/assets/default.js', array( 'jquery' ), false, true ); 

        wp_localize_script( 'c4d-team-member-admin-plugin-js', 'c4d_category_image',
                array( 'site_url' => site_url() ) );
    }

    public function metaFields($tag) {
	    $catMeta = get_option($this->optionName);
        $template = isset($catMeta[$tag]) ? $catMeta[$tag] : false;
        ?>
		<div class="form-field">
			<label ><?php _e('Feature Image', 'c4d-category-image'); ?></label>
			<div id="<?php echo esc_attr($this->optionName); ?>" style="background-image: url(<?php echo esc_url(site_url().$template); ?>)"><?php echo __('Select Image', 'c4d-category-image'); ?></div>
            <input type="hidden" id="<?php echo esc_attr($this->optionName.'_input'); ?>" name="<?php echo esc_attr($this->optionName); ?>" value="<?php echo esc_attr($template); ?>"/>
            <div>Select the image for category. Then you can use class c4d-category-image for element you want or function php: c4d_category_image();</div>
		</div>
		<?php
	}

    public function metaFieldsTable($tag) {
        $tID = $tag->term_id;
        $catMeta = get_option($this->optionName);
        $template = isset($catMeta[$tID]) ? $catMeta[$tID] : false;
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label ><?php _e('Feature Image', 'c4d-category-image'); ?></label></th>
            <td>
                <div id="<?php echo esc_attr($this->optionName); ?>" style="background-image: url(<?php echo esc_url(site_url().$template); ?>)"><?php echo __('Select Image', 'c4d-category-image'); ?></div>
                <input type="hidden" id="<?php echo esc_attr($this->optionName.'_input'); ?>" name="<?php echo esc_attr($this->optionName); ?>" value="<?php echo esc_attr($template); ?>"/>
                <div>Select the image for category. Then you can use class c4d-category-image for element you want or function php: c4d_category_image();</div>
            </td>
        </tr>
        <?php
    }

	public function save( $term_id ) {
        if ( isset( $_POST[$this->optionName] )) {
	        $catMeta = get_option($this->optionName);
	        $catMeta[$term_id] = sanitize_text_field($_POST[$this->optionName]);
            update_option($this->optionName, $catMeta );
        }
	}

    public function background() {
        if (is_category() || is_single()) {
            $catId = -1;
            if (is_category()) {
                $catId = get_query_var('cat');    
            }
            if (is_single()) {
                global $post;
                $cate = wp_get_post_categories(get_the_id());
                if (count($cate) > 0) {
                    $cate = get_category($cate[0]);
                    $catId = $cate->cat_ID;    
                }
            }
            $catMeta = get_option($this->optionName);
            $template = isset($catMeta[$catId]) ? $catMeta[$catId] : false;
            if ($template) {
                echo '<style class="c4d-category-image">.c4d-category-image { background-image: url('.esc_url(site_url().$template).'); }</style>';
            }
        }
    }
}

function c4d_category_image($catid = false){
    if (is_category()) {
        $catId = get_query_var('cat');
    } 
    
    $catMeta = get_option('c4d_category_image');
    $template = isset($catMeta[$catId]) ? $catMeta[$catId] : false;

    if ($template) {
        return site_url().$template;
    }
    
    return '';
}

new C4D_category_image();