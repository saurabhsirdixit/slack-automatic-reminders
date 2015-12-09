<?php
/**
 * Plugin Name: Slack Automatic Reminders
 * Plugin URI: https://github.com/saurabhsirdixit/slack-automatic-reminders
 * Description: This plugin allows you to send automatic reminders to Slack channels.
 * Version: 0.0.1
 * Author: Saurabh Dixit
 * Author URI: https://axelerant.com
 * Text Domain: slack
 * Domain Path: /languages
 * License: GPL v2 or later
 * Requires at least: 3.6
 * Tested up to: 4.3.1
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

/**
 * Adds new event that send notification to Slack channel
 *
 * @param  array $events
 * @return array
 *
 * @filter slack_get_events
 */

//add_action( 'init', 'create_slack_automatic_reminders_custom_post' );
function create_slack_automatic_reminders_custom_post() {
	register_post_type( 'automatic_reminders',
		array(
			'labels' => array(
				'name' => __( 'Slack Automatic Reminders' ),
				'singular_name' => __( 'Slack Automatic Reminder' )
			),
			'public' => true,
			'has_archive' => true,
			'taxonomies' => array( '' ),
			'supports' => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields' ),
			'rewrite' => array('slug' => 'slack-automatic-reminder'),
		)
	);
}

function axl_quarterly_cron() {
    do_action('axl-send-slack-api');
}
add_action( 'axl_slack_alert', 'axl_quarterly_cron' );

// Custom Cron Recurrences
function custom_cron_job_custom_recurrence( $schedules ) {
	$schedules['quarterly'] = array(
		'display' => __( 'Quarterly', 'textdomain' ),
		'interval' => 7905600,
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'custom_cron_job_custom_recurrence' );

// Schedule Cron Job Event
function custom_cron_job() {
	if ( ! wp_next_scheduled( 'axl_slack_alert' ) ) {
		wp_schedule_event( 1443675600, 'quarterly', 'axl_slack_alert' );
	}
}
add_action( 'wp', 'custom_cron_job' );

function wp_slack_automatic_reminders( $events ) {
	$events['axl-send-slack'] = array(
		'action'      => 'axl-send-slack-api',
		'description' => __( 'Slack Automatic Reminders', 'slack' ),
		'default'     => false,
		'message'     => function( $ID, $post ) {
			return sprintf(
				'We have had a number of requests for onsite opportunities in the last few months. Before pursuing, we would like to know who among you are interested in such. Please fill up the form to know your interest on the same [https://docs.google.com/a/axelerant.com/forms/d/1CfM0MKd1L99XnTeOmj4QLQVNN9J2aytAtjyuq27aobg/viewform]'
			);
		},
	);

	return $events;
}
add_filter( 'slack_get_events', 'wp_slack_automatic_reminders' );
