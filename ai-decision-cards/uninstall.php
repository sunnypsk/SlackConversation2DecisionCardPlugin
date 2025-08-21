<?php
// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'aidc_api_type' );
delete_option( 'aidc_openai_api_key' );
delete_option( 'aidc_openai_api_base' );
delete_option( 'aidc_openai_model' );


