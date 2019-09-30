<?php
/**
 * Plugin Name: ActivityPub
 * Plugin URI: https://github.com/pfefferle/wordpress-activitypub/
 * Description: The ActivityPub protocol is a decentralized social networking protocol based upon the ActivityStreams 2.0 data format.
 * Version: 0.8.3
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Requires PHP: 5.6
 * Text Domain: activitypub
 * Domain Path: /languages
 */

namespace Activitypub;

/**
 * Initialize plugin
 */
function init() {
	\defined( 'ACTIVITYPUB_HASHTAGS_REGEXP' ) || \define( 'ACTIVITYPUB_HASHTAGS_REGEXP', '(?:(?<=\s)|^)#(\w*[A-Za-z_]+\w*)' );

	require_once dirname( __FILE__ ) . '/includes/table/followers-list.php';
	require_once dirname( __FILE__ ) . '/includes/class-signature.php';
	require_once dirname( __FILE__ ) . '/includes/class-activity.php';
	require_once dirname( __FILE__ ) . '/includes/db/class-followers.php';
	require_once dirname( __FILE__ ) . '/includes/functions.php';

	require_once dirname( __FILE__ ) . '/includes/class-activity-dispatcher.php';
	\Activitypub\Activity_Dispatcher::init();

	require_once dirname( __FILE__ ) . '/includes/class-post.php';
	\Activitypub\Post::init();

	require_once dirname( __FILE__ ) . '/includes/class-activitypub.php';
	\Activitypub\Activitypub::init();

	// Configure the REST API route
	require_once dirname( __FILE__ ) . '/includes/rest/class-outbox.php';
	\Activitypub\Rest\Outbox::init();

	require_once dirname( __FILE__ ) . '/includes/rest/class-inbox.php';
	\Activitypub\Rest\Inbox::init();

	require_once dirname( __FILE__ ) . '/includes/rest/class-followers.php';
	\Activitypub\Rest\Followers::init();

	require_once dirname( __FILE__ ) . '/includes/rest/class-following.php';
	\Activitypub\Rest\Following::init();

	require_once dirname( __FILE__ ) . '/includes/rest/class-webfinger.php';
	\Activitypub\Rest\Webfinger::init();

	require_once dirname( __FILE__ ) . '/includes/rest/class-nodeinfo.php';
	\Activitypub\Rest\NodeInfo::init();

	require_once dirname( __FILE__ ) . '/includes/class-admin.php';
	\Activitypub\Admin::init();

	require_once dirname( __FILE__ ) . '/includes/class-hashtag.php';
	\Activitypub\Hashtag::init();

	require_once dirname( __FILE__ ) . '/includes/class-debug.php';
	\Activitypub\Debug::init();

	require_once dirname( __FILE__ ) . '/includes/class-health-check.php';
	\Activitypub\Health_Check::init();
}
add_action( 'plugins_loaded', '\Activitypub\init' );

/**
 * Add rewrite rules
 */
function add_rewrite_rules() {
	if ( ! \class_exists( 'Webfinger' ) ) {
		\add_rewrite_rule( '^.well-known/webfinger', 'index.php?rest_route=/activitypub/1.0/webfinger', 'top' );
	}

	if ( ! \class_exists( 'Nodeinfo' ) ) {
		\add_rewrite_rule( '^.well-known/nodeinfo', 'index.php?rest_route=/activitypub/1.0/nodeinfo/discovery', 'top' );
		\add_rewrite_rule( '^.well-known/x-nodeinfo2', 'index.php?rest_route=/activitypub/1.0/nodeinfo2', 'top' );
	}
}
\add_action( 'init', '\Activitypub\add_rewrite_rules', 1 );

/**
 * Flush rewrite rules;
 */
function flush_rewrite_rules() {
	\Activitypub\add_rewrite_rules();
	\flush_rewrite_rules();
}
\register_activation_hook( __FILE__, '\Activitypub\flush_rewrite_rules' );
\register_deactivation_hook( __FILE__, '\flush_rewrite_rules' );
