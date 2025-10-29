<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cldelriolaborato_adm_con' );

/** Database username */
define( 'DB_USER', 'cldelriolaborato_webmalgarini' );

/** Database password */
define( 'DB_PASSWORD', '44o5.3{eo8Ch8[9t' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'EL{.|o)Uzs,QIET8i3^J5B*ZCUZv `XO|DF1Y`/`I0U#6dl-,0B6&_b7dN($r!c|');
define('SECURE_AUTH_KEY',  'IvGkK{|A!F 7wA4*(`>%C p ;-|+<gf|_^FqEDlEiys i Xlnh-}fmK!90^U(ci(');
define('LOGGED_IN_KEY',    '_`XaQ#5(c![b),~]s5@fj8M:1ovz,JXHd<SGt#}-i}7q{L(b50G~.q V>@[4!@R|');
define('NONCE_KEY',        'jI:jVrr=L;ZA?1s?iAI68GuG,jKE=5q}iaLN+7F:}}8lyo/=X2|wFuY57>m]2Z)9');
define('AUTH_SALT',        '$YV-y7Wv=5tc_R|1ILdjoB$[K-1R_d*,d@}P#~7wVT-7TQ|j*xL<08:?ft 1ZUN+');
define('SECURE_AUTH_SALT', 'knv#Im}2*E;nFu&qDFGDZ2C$8]~N)QpBoKYUL4Ls[7H&E8Wj!|0`AiSGC+Fd}^Ez');
define('LOGGED_IN_SALT',   'dpc1ha{AL)w8br!F{pGTl+r(`9}h7n+vkK)nmXJqzvN w$q)@&l=Dr$1kh+[Agy-');
define('NONCE_SALT',       '|m3_8:THmH,xz/9`A=qe>I,*R@_I3.[Gz!IZe=gMZ&S26U3LHgSn5FE7d&D!W!SS');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
