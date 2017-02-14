<?php

class BiabSenseHAT_REST {

	public static function register_sensehat_routes( ) {
		register_rest_route( 'biab/v1', '/sensehat', array(
			'methods' => 'GET',
			'callback' => array( 'BiabSensehat_REST', 'get_reading' ),
		) );
		register_rest_route( 'biab/v1', '/sensehat', array(
			'methods' => 'POST',
			'callback' => array( 'BiabSensehat_REST', 'insert_reading' ),
		) );
	}

	public static function get_reading( WP_REST_Request $request ) {
		if( isset( $request[ 'before' ] ) && isset( $request[ 'after' ] ) ) {
			$sql_query = "
			SELECT temperature AS temperature,
				humidity AS humidity,
				air_pressure AS air_pressure,
				created_at AS timestamp
			FROM wp_sense_hat
			WHERE date(created_at) >= '".$request['after']."'
				AND date(created_at) <= '".$request['before']."'
			";
		} else {
			$sql_query = "
			SELECT temperature AS temperature,
				humidity AS humidity,
				air_pressure AS air_pressure,
				created_at AS timestamp
			FROM wp_sense_hat
			ORDER BY id DESC LIMIT 1;
			";
		}

		global $wpdb;
		$values = $wpdb->get_results( $sql_query );
		return $values ? $values : [];

	}

	public static function insert_reading( WP_REST_Request $request ) {
		global $wpdb;

		$rows = $wpdb->insert( 'wp_sense_hat',
			array(
				'temperature'  => $request->get_param( 'temperature' ),
				'humidity'     => $request->get_param( 'humidity' ),
				'air_pressure' => $request->get_param( 'air_pressure' ),
			),
			array(
				'%f',
				'%f',
				'%f',
			 )
		);

		return $request->get_params();
	}

}
