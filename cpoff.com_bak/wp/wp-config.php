<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'curtyhtf_wp896');

/** MySQL database username */
define('DB_USER', 'curtyhtf_wp896');

/** MySQL database password */
define('DB_PASSWORD', '5lS4[Qxp!5');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'yoonitkyhbbyx1qhjwdifxpairykq7cwfkivlizcueeioaour6f8n8d7c5kohnat');
define('SECURE_AUTH_KEY',  'flnytq75jbww7yikisal6dmsobrcb3eeqig3w4fzeh3ylcrvt6ecksbojsxokvwv');
define('LOGGED_IN_KEY',    'emz7tbul1w3nrj765c53atpftsr60c2c7x3xmlem7tkzhpf2aqgqsq9juybcg6kv');
define('NONCE_KEY',        'bztnjtlmjgrvstiya9mnb7gk78a13ajdckzkotzre6l1be8soqyxh2ifh5btqesk');
define('AUTH_SALT',        'oi4kadlyanswee3pqllwm9jvyqizeccldjebqdjglnwurpcnk0nhjpsi9bxx7oeh');
define('SECURE_AUTH_SALT', 'lxjqgqxc4ybkywsgrtykyjy9texkea8q2kpyspslfaq3vicofj1gn3qzcyujjrxa');
define('LOGGED_IN_SALT',   'bijxb8fx6tlgwj90psy6qztqbaanzrtj63iremwdta1z12oc9dlt1r1zcpudtmpk');
define('NONCE_SALT',       'axediktgypfqdotxkb8q8ytcugz8tyxt9gi6vll2he14vtuwwvwvch9qabdorcuu');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpf1_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
