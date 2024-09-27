<?php
/*
 * Plugin Name:  Gravity Forms Dummy Entry Generator
 * Plugin URI:   http://gravitywiz.com
 * Description:  A plugin to generate a large number of entries for testing purposes.
 * Author:       Gravity Wiz
 * Version:      1.0-beta-1
 * Author URI:   http://gravitywiz.com
 */

define( 'GWIZ_DUMMY_ENTRY_GENERATOR', '1.0-beta-1' );

add_action( 'init', function() {
	if ( ! is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	require_once( plugin_dir_path( __FILE__ ) . '/vendor/autoload_packages.php' );

	$gwiz_faker = new GWiz_Faker();

	new GWiz_Batcher\Batcher( array(
		'title'        => 'Dummy Entry Generator',
		'id'           => 'gwiz-dummy-entry-generator',
		'size'         => 25,
		'show_form_selector' => true,
		'require_form_selection' => true,
		'additional_inputs' => '<p><label>Number of Entries</label><input type="number" placeholder="Number of entries to generate" value="50" name="number_of_entries" /></p>',
		'create_admin_page' => true,
		'get_items'    => function ( $size, $offset ) use ( $gwiz_faker ) {
			$total = $_POST['number_of_entries'];

			$paging  = array(
				'offset'    => $offset,
				'page_size' => $size,
			);

			// Generate the dummy entries here.
			$entries = [];

			$form = GFAPI::get_form( $_POST['form_id'] );

			for ( $i = 0; $i < $size; $i++ ) {
				$entries[] = $gwiz_faker->generate_entry( $form );
			}

			return array(
				'items' => $entries,
				'total' => $total,
			);
		},
		'process_item' => function ( $entry ) {
			// Insert the entry here.
			GFAPI::add_entry( $entry );

			// Add entry meta here so we can delete the entries later.
			gform_add_meta( $entry['id'], 'gwiz_dummy_entry', true );
		},
	) );
} );

function gwiz_create_dummy_entry( $form_id ) {
	$form = GFAPI::get_form( $form_id );
}