<?php
/**
 * Payment Meta DB class
 *
 * @package     Give
 * @subpackage  Classes/DB Payment Meta
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Give_DB_Payment_Meta
 *
 * This class is for interacting with the payment meta database table.
 *
 * @since 2.0
 */
class Give_DB_Payment_Meta extends Give_DB {
	/**
	 * Give_DB_Payment_Meta constructor.
	 *
	 * @access  public
	 * @since   2.0
	 */
	public function __construct() {
		/* @var WPDB $wpdb */
		global $wpdb;

		$wpdb->paymentmeta = $this->table_name = $wpdb->prefix . 'give_paymentmeta';
		$this->primary_key = 'meta_id';
		$this->version     = '1.0';

		$this->register_table();

		add_filter( 'add_post_metadata', array( $this, '__add_meta' ), 0, 4 );
		add_filter( 'get_post_metadata', array( $this, '__get_meta' ), 0, 4 );
		add_filter( 'update_post_metadata', array( $this, '__update_meta' ), 0, 4 );
		add_filter( 'delete_post_metadata', array( $this, '__delete_meta' ), 0, 4 );
	}

	/**
	 * Get table columns and data types.
	 *
	 * @access  public
	 * @since   2.0
	 *
	 * @return  array  Columns and formats.
	 */
	public function get_columns() {
		return array(
			'meta_id'    => '%d',
			'payment_id' => '%d',
			'meta_key'   => '%s',
			'meta_value' => '%s',
		);
	}

	/**
	 * Retrieve payment meta field for a payment.
	 *
	 * For internal use only. Use Give_Payment->get_meta() for public usage.
	 *
	 * @access  public
	 * @since   2.0
	 *
	 * @param   int    $payment_id Payment ID.
	 * @param   string $meta_key   The meta key to retrieve.
	 * @param   bool   $single     Whether to return a single value.
	 *
	 * @return  mixed                 Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public function get_meta( $payment_id = 0, $meta_key = '', $single = false ) {
		$payment_id = $this->sanitize_id( $payment_id );

		// Bailout.
		if ( ! $payment_id || ! Give()->logs->log_db->is_log( $payment_id ) ) {
			return null;
		}

		return get_metadata( 'payment', $payment_id, $meta_key, $single );
	}

	/**
	 * Add meta data field to a payment.
	 *
	 * For internal use only. Use Give_Payment->add_meta() for public usage.
	 *
	 * @access  private
	 * @since   2.0
	 *
	 * @param   int    $payment_id Payment ID.
	 * @param   string $meta_key   Metadata name.
	 * @param   mixed  $meta_value Metadata value.
	 * @param   bool   $unique     Optional, default is false. Whether the same key should not be added.
	 *
	 * @return  bool                  False for failure. True for success.
	 */
	public function add_meta( $payment_id = 0, $meta_key = '', $meta_value, $unique = false ) {
		$payment_id = $this->sanitize_id( $payment_id );

		// Bailout.
		if ( ! $payment_id || ! Give()->logs->log_db->is_log( $payment_id ) ) {
			return null;
		}

		return add_metadata( 'payment', $payment_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Update payment meta field based on Payment ID.
	 *
	 * For internal use only. Use Give_Payment->update_meta() for public usage.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and Payment ID.
	 *
	 * If the meta field for the payment does not exist, it will be added.
	 *
	 * @access  public
	 * @since   2.0
	 *
	 * @param   int    $payment_id Payment ID.
	 * @param   string $meta_key   Metadata key.
	 * @param   mixed  $meta_value Metadata value.
	 * @param   mixed  $prev_value Optional. Previous value to check before removing.
	 *
	 * @return  bool                  False on failure, true if success.
	 */
	public function update_meta( $payment_id = 0, $meta_key = '', $meta_value, $prev_value = '' ) {
		$payment_id = $this->sanitize_id( $payment_id );

		// Bailout.
		if ( ! $payment_id || ! Give()->logs->log_db->is_log( $payment_id ) ) {
			return null;
		}

		return update_metadata( 'payment', $payment_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from a payment.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @access  public
	 * @since   2.0
	 *
	 * @param   int    $payment_id Payment ID.
	 * @param   string $meta_key   Metadata name.
	 * @param   mixed  $meta_value Optional. Metadata value.
	 *
	 * @return  bool                  False for failure. True for success.
	 */
	public function delete_meta( $payment_id = 0, $meta_key = '', $meta_value = '' ) {
		$payment_id = $this->sanitize_id( $payment_id );

		// Bailout.
		if ( ! $payment_id || ! Give()->logs->log_db->is_log( $payment_id ) ) {
			return null;
		}

		return delete_metadata( 'payment', $payment_id, $meta_key, $meta_value );
	}

	/**
	 * Create the table
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return void
	 */
	public function create_table() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$this->table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			payment_id bigint(20) NOT NULL,
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY payment_id (payment_id),
			KEY meta_key (meta_key)
			) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

	/**
	 * Add support for hidden functions.
	 *
	 * @since  2.0
	 * @access public
	 *
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		switch ( $name ) {
			case '__add_meta':
				$check    = $arguments[0];
				$log_id   = $arguments[1];
				$meta_key = $arguments[2];
				$single   = $arguments[3];

				return $this->add_meta( $log_id, $meta_key, $single );

			case '__get_meta':
				$check    = $arguments[0];
				$log_id   = $arguments[1];
				$meta_key = $arguments[2];
				$single   = $arguments[3];

				return $this->get_meta( $log_id, $meta_key, $single );

			case '__update_meta':
				$check    = $arguments[0];
				$log_id   = $arguments[1];
				$meta_key = $arguments[2];
				$single   = $arguments[3];

				return $this->update_meta( $log_id, $meta_key, $single );

			case '__delete_meta':
				$check    = $arguments[0];
				$log_id   = $arguments[1];
				$meta_key = $arguments[2];
				$single   = $arguments[3];

				return $this->delete_meta( $log_id, $meta_key, $single );
		}
	}
}
