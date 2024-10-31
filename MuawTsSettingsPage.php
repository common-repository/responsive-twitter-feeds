<?php
class MuawTsSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'muts_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'muaw_tws_page_init' ) );
    }

    /**
     * Add options page
     */
    public function muts_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Twitter Settings',
            'Twitter Settings',
            'manage_options',
            'muts_settings-admin',
            array( $this, 'muaw_tws_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function muaw_tws_admin_page()
    {
        // Set class property
        $this->options = get_option( 'muts_option' );
        ?>
        <div class="wrap">
            <h2>Twitter Feed Settings</h2>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'muts_option_group' );
                do_settings_sections( 'muts_settings-admin' );

                submit_button();
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function muaw_tws_page_init()
    {
        register_setting(
            'muts_option_group', // Option group
            'muts_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'muts_setting_section', // ID
            'Twitter Settings', // Title
            array( $this, 'print_muts_section' ), // Callback
            'muts_settings-admin' // Page
        );

        add_settings_field(
            'consumer_key', // ID
            'Consumer Key (API Key)', // Title
            array( $this, 'consumer_key_callback' ), // Callback
            'muts_settings-admin', // Page
            'muts_setting_section' // Section
        );

        add_settings_field(
            'consumer_secret',
            'Consumer Secret (API Secret)',
            array( $this, 'consumer_secret_callback' ),
            'muts_settings-admin',
            'muts_setting_section'
        );

        add_settings_field(
            'access_token',
            'Access Token',
            array( $this, 'access_token_callback' ),
            'muts_settings-admin',
            'muts_setting_section'
        );

        add_settings_field(
            'access_token_secret',
            'Access Token Secret',
            array( $this, 'access_token_secret_callback' ),
            'muts_settings-admin',
            'muts_setting_section'
        );

        add_settings_field(
            'twitter_username',
            'Twitter Username',
            array( $this, 'tw_username_callback' ),
            'muts_settings-admin',
            'muts_setting_section'
        );

        add_settings_field(
            'no_of_posts_to_show',
            'Number of Tweets to show',
            array( $this, 'no_of_posts_to_show_callback' ),
            'muts_settings-admin',
            'muts_setting_section'
        );

        add_settings_section(
            'muts_setting_section_last', // ID
            'Shortcode Options', // Title
            array( $this, 'print_muts_section_last' ), // Callback
            'muts_settings-admin' // Page
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['muts_consumer_key'] ) )
            /*$new_input['consumer_key'] = absint( $input['consumer_key'] );*/
            $new_input['muts_consumer_key'] = sanitize_text_field( $input['muts_consumer_key'] );

        if( isset( $input['muts_consumer_secret'] ) )
            $new_input['muts_consumer_secret'] = sanitize_text_field( $input['muts_consumer_secret'] );

        if( isset( $input['muts_access_token'] ) )
            $new_input['muts_access_token'] = sanitize_text_field( $input['muts_access_token'] );

        if( isset( $input['muts_access_token_secret'] ) )
            $new_input['muts_access_token_secret'] = sanitize_text_field( $input['muts_access_token_secret'] );

        if( isset( $input['twitter_username'] ) )
            $new_input['twitter_username'] = sanitize_text_field( $input['twitter_username'] );

        if( isset( $input['no_of_posts_to_show'] ) )
            $new_input['no_of_posts_to_show'] = absint( $input['no_of_posts_to_show'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_muts_section()
    {
        print 'Following details are needed in order to show twitter feeds on your website. Please fill those in.';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function consumer_key_callback()
    {
        printf(
            '<input type="text" id="muts_consumer_key" name="muts_option[muts_consumer_key]" value="%s" />',
            isset( $this->options['muts_consumer_key'] ) ? esc_attr( $this->options['muts_consumer_key']) : ''
        );
    }

    /**
     * Get the settings option array and print consumer_secret value
     */
    public function consumer_secret_callback()
    {
        printf(
            '<input type="text" id="muts_consumer_secret" name="muts_option[muts_consumer_secret]" value="%s" />',
            isset( $this->options['muts_consumer_secret'] ) ? esc_attr( $this->options['muts_consumer_secret']) : ''
        );
    }

    /**
     * Get the settings option array and print access_token value
     */
    public function access_token_callback()
    {
        printf(
            '<input type="text" id="muts_access_token" name="muts_option[muts_access_token]" value="%s" />',
            isset( $this->options['muts_access_token'] ) ? esc_attr( $this->options['muts_access_token']) : ''
        );
    }

    /**
     * Get the settings option array and print access_token value
     */
    public function access_token_secret_callback()
    {
        printf(
            '<input type="text" id="muts_access_token_secret" name="muts_option[muts_access_token_secret]" value="%s" />',
            isset( $this->options['muts_access_token_secret'] ) ? esc_attr( $this->options['muts_access_token_secret']) : ''
        );
    }

    public function tw_username_callback(){
        printf(
            '<input type="text" id="twitter_username" name="muts_option[twitter_username]" value="%s" />',
            isset( $this->options['twitter_username'] ) ? esc_attr( $this->options['twitter_username']) : ''
        );
    }

    public function no_of_posts_to_show_callback(){
        printf(
            '<input type="text" id="no_of_posts_to_show" name="muts_option[no_of_posts_to_show]" value="%s" />',
            isset( $this->options['no_of_posts_to_show'] ) ? esc_attr( $this->options['no_of_posts_to_show']) : ''
        );
    }

    public function print_muts_section_last()
    {
        printf('Copy the shortcode bellow and past it anywhere you need to show twitter feeds..<br/>Shortcode to embed is : [muaw_twitter_tweets]');
    }
}