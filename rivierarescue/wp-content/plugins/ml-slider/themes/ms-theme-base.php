<?php

if (!defined('ABSPATH')) {
die('No direct access.');
}
/**
 * MS Theme base - Use to instanciate and store theme functions. 
 * Extend it if you need to change / add functionality to your theme.
 */
class MetaSlider_Theme_Base
{
    /**
     * Theme ID
     *
     * @var string
     */
    public $id;

    /**
     * Assets
     *
     * @var array
     */
    public $assets;

    /**
     * Registered Themes - used to give access to the themes options and settings
     *
     * @var array
     */
    public static $themes = array();

    /**
     * Parameters
     *
     * @var string
     */
    public $slider_parameters = array();

    /**
     * Theme Version
     *
     * @var string
     */
    public $version;

    /**
     * Construct - set private for singleton pattern.
     *
     * @param int   $id      ID
     * @param int   $version Version
     * @param array $assets  Assets array
     */
    public function __construct($id, $version, $assets = array())
    {
        $this->id = $id;
        $this->version = $version;
        $this->assets = apply_filters('metaslider_theme_assets', $assets, $this->id);
        
        // store the current instance, to give it global access via MetaSlider_Theme_Base::$themes['theme_id']
        self::$themes[$this->id] = $this;

        $this->init();

        // Customize theme design
        add_filter('metaslider_css', array($this, 'theme_customize'), 10, 3);
    }

    /**
     * Initialize hooks
     */
    public function init()
    {
        // Enqueue assets
        add_action('metaslider_register_public_styles', array($this, 'enqueue_assets'));

        // override the arrows markup
        add_filter('metaslider_flex_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_responsive_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_nivo_slider_parameters', array($this, 'update_parameters'), 99, 3);
        add_filter('metaslider_coin_slider_parameters', array($this, 'update_parameters'), 99, 3);

        // Pro - override the arrows markup for the filmstrip
        add_filter('metaslider_flex_slider_filmstrip_parameters', array($this, 'update_parameters'), 99, 3);

        // Adds classes for thumbnails and filmstrip navigation
        add_filter('metaslider_css_classes', array($this, 'slider_classes'), 20, 3);
    }

    /**
     * Slider Classes - Filter
     *
     * @param string $classes         Slider Classes
     * @param int    $slider_id       Slider ID
     * @param array  $slider_settings Slider Settings
     * @return string
     */
    public function slider_classes($classes, $slider_id, $slider_settings)
    {
        if (isset($slider_settings['carouselMode']) && 'true' === $slider_settings['carouselMode']) {
            $classes .= ' has-carousel-mode';
        }
        if ('true' == $slider_settings['navigation']) {
            $classes .= ' has-dots-nav';
        }
        if ('filmstrip' == $slider_settings['navigation']) {
            $classes .= ' has-filmstrip-nav';
        }
        return $classes;
    }

    /**
     * Enqueues theme specific styles and scripts
     */
    public function enqueue_assets()
    {
        foreach ($this->assets as $asset) {
            if ('css' == $asset['type']) {
                wp_enqueue_style('metaslider_' . $this->id . '_theme_styles', METASLIDER_THEMES_URL . $this->id . $asset['file'], isset($asset['dependencies']) ? $asset['dependencies'] : array(), $this->version);
            }

            if ('js' == $asset['type']) {
                wp_enqueue_script('metaslider_' . $this->id . '_theme_script', METASLIDER_THEMES_URL . $this->id . $asset['file'], isset($asset['dependencies']) ? $asset['dependencies'] : array(), $this->version, isset($asset['in_footer']) ? $asset['in_footer'] : true);
            }
        }
    }

    /**
     * Adds parameters for this theme. Used mainly for changing the Arrows text + icons
     *
     * @param array      $options      The slider plugin options
     * @param int|string $slideshow_id The slideshow options
     * @param array      $settings     The slideshow settings
     */
    public function update_parameters($options, $slideshow_id, $settings)
    {
        $theme_id = false;

        if (!$this->slider_parameters) {
return $options;
        }

        // if preview
        if (isset($_REQUEST['action']) && 'ms_get_preview' == $_REQUEST['action']) {
            if (isset($_REQUEST['theme_id'])) {
                $theme_id = sanitize_text_field($_REQUEST['theme_id']);
            }
        }
    
        // only fetch the saved theme if the preview theme isn't set
        if (!$theme_id) {
            $theme = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);
            if (isset($theme['folder'])) {
                $theme_id = $theme['folder'];
            }
        }

        if ($this->id == $theme_id) {
            return array_merge($options, apply_filters('metaslider_theme_' . $this->id . '_slider_parameters', $this->slider_parameters));
        }

        return $options;
    }

    /**
     * Add manual controls to this theme
     *
     * @param array  $html         - The flexslider options
     * @param string $slideshow_id - the id of the slideshow
     * @param array  $settings     - the id of the slideshow
     *
     * @return array
     */
    public function add_title_to_replace_dots($html, $slideshow_id, $settings)
    {
        // We want to insert this after the closing ul but before the container div
        $nav = "</ul>";

        // Only enable this for dots nav
        if ('true' === $settings['navigation'] && 'false' === $settings['carouselMode']) {
            $nav .= "<ol class='flex-control-nav titleNav-{$slideshow_id}'>";
            foreach ($this->get_slides($slideshow_id) as $count => $slide) {
                // Check if the title is inherited or manually set
                if ((bool) get_post_meta($slide->ID, 'ml-slider_inherit_image_title', true)) {
                    $attachment = get_post(get_post_thumbnail_id($slide->ID));
                    $title = $attachment->post_title;
                } else {
                    $title = get_post_meta($slide->ID, 'ml-slider_title', true);
                }

                // Check if it's a string and not '' and use the count + 1
                if (!is_string($title) || empty($title)) {
                    $title = $count;
                }
                $nav .= "<li><a href='#'>{$title}</a></li>";
            }
            $nav .= "</ol>";
        }
        return str_replace('</ul>', $nav, $html);
    }

    /**
     * Copy the query from ml-slider
     *
     * @param int $slideshow_id - the id of the slideshow
     * @return WP_Query
     */
    private function get_slides($slideshow_id)
    {
        $settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
        $args = array(
            'force_no_custom_order' => true,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => array('attachment', 'ml-slide'),
            'post_status' => array('inherit', 'publish'),
            'lang' => '', // polylang, ingore language filter
            'suppress_filters' => 1, // wpml, ignore language filter
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => $slideshow_id
                )
            )
        );

        $args = apply_filters('metaslider_populate_slides_args', $args, $slideshow_id, $settings);
        $slides = get_posts($args);
        
        $available_slides = array();
        foreach ($slides as $slide) {
            $type = get_post_meta($slide->ID, 'ml-slider_type', true);
            $type = $type ? $type : 'image'; // Default ot image

            $is_hidden = get_post_meta($slide->ID, '_meta_slider_slide_is_hidden', true);
            if($is_hidden != true){
                // If this filter exists, that means the slide type is available (i.e. pro slides)
                if (has_filter("metaslider_get_{$type}_slide")) {
                    array_push($available_slides, $slide);
                }
            }
            
        }
        return $available_slides;
    }

    /**
     * Build CSS to customize theme colors
     * 
     * @since 3.93
     * 
     * @param string $theme                 Theme name in lowercase. e.g. 'bitono'
     * @param array|string $settings        Slideshow settings
     * @param array|string $slideshow_id    Slideshow id 
     * 
     * @return string
     */
    public function theme_customize_css($theme, $settings, $slideshow_id)
    {
        $theme_settings = get_post_meta($slideshow_id, 'metaslider_slideshow_theme', true);

        // @since 3.94 - Are we using this core theme through a custom theme v2?
        if (isset($theme_settings['folder']) && '_theme_v2' === substr($theme_settings['folder'], 0, 9)) {
            $custom_themes  = get_option('metaslider-themes');
            $stored_data    = isset($custom_themes[$theme_settings['folder']]) && isset($custom_themes[$theme_settings['folder']]['customize']) 
                            ? $custom_themes[$theme_settings['folder']]['customize']
                            : array();
        } else {
            // Is a core theme - customization settings are stored in ml-slider_settings postmeta db
            $stored_data = $settings['theme_customize'];
        }

        $manifest   = array();
        $type       = isset($theme_settings['type']) ? $theme_settings['type'] : 'free';

        $themes_class = MetaSlider_Themes::get_instance();

        // Is a premium, external or custom theme (v2), override $manifest path
        if ($type !== 'free') {
            // Check if is a custom v2 based on a free theme
            $manifest = $themes_class->add_base_customize_settings_single($theme);

            /**
             * Check if is a premium or custom theme (v2) based on a premium theme
             * by looping extra themes/ folders added from external sources,
             * including MetaSlider Pro.
             * We may also support external themes (custom coded themes added by users).
             * 
             * e.g. 
             * array(
             *  '/path/to/wp-content/plugins/ml-slider-pro/themes/',
             *  '/path/to/wp-content/themes/my-theme/ms-themes/'
             * )
             */
            if (! count($manifest)) {
                $extra_themes = apply_filters('metaslider_extra_themes', array());

                foreach ($extra_themes as $location) {
                    // Check if customize.php file that belongs to $theme as theme name (lowercase) exists
                    if (file_exists($customize_file = trailingslashit($location) . trailingslashit($theme) . 'customize.php')) {
                        // Get the data from customize.php files
                        $manifest = $themes_class->add_base_customize_settings_single(
                            $theme, $customize_file
                        );
                        break;
                    }
                }
            }
        } else {
            // Is a free theme - Get data from themes/$theme/customize.php
            $manifest = $themes_class->add_base_customize_settings_single($theme);
        }

        $output = $themes_class->build_customize_css($manifest, $stored_data, $slideshow_id);

        return $output;
    }

    /**
     * Add inline CSS to customize theme design
     * 
     * @since 3.93.0 - Moved from each theme.php file
     */
    public function theme_customize($css, $settings, $slideshow_id)
    {
        // /wp-admin/admin.php?page=metaslider-theme-editor&theme_slug=<slug>1&version=v2
        $is_theme_editor_screen = is_admin() 
            && function_exists('get_current_screen') 
            && ($screen = get_current_screen())
            && 'metaslider-pro_page_metaslider-theme-editor' === $screen->id 
            && isset($_GET['version']) 
            && $_GET['version'] == 'v2';

        /* This CSS only works with Flexslider
         * Important: even if is empty, 'theme_customize' is required in 'ml-slider_settings' postmeta db 
         * for themes created with v2 theme editor */
        $doesnt_use_customize = $settings['type'] !== 'flex' || ! isset($settings['theme_customize']);

        
        if ($is_theme_editor_screen || $doesnt_use_customize) {
            return $css;
        }

        $css .= $this->theme_customize_css(
            $this->id,
            $settings,
            $slideshow_id
        );

        // Commented to solve an issue:
        // can't have more than one slideshow based on the same theme on the same page
        //remove_filter('metaslider_css', array($this, 'theme_customize'), 10, 3);

        return $css;
    }
}
