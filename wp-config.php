<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //

/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',          'x,A&Nq)(iaL<8DWZf;~OrvQ;Zsdh&<#U_=68Nt|;?4z>OD{=+@jm;@=2=46yrms@' );
define( 'SECURE_AUTH_KEY',   ']Tqz<w.C@mcPDCe/f&M[]~Iys?]DUWax@zqN%qiP3^X-^8R.)h1gm5D=>a4ksw,,' );
define( 'LOGGED_IN_KEY',     'vi|qRhCW*(IaZmr</a/c9&:_|q,5sZ)WRvGIB[b+Lbs;Nf8j8$^ye5*wVN85(Jp)' );
define( 'NONCE_KEY',         '<W#aj+_z<fV|h6z8@X x6$Td0SgTfW:%eL>,bXK26rR8S(-O2e1hdOSP,,>N,J0:' );
define( 'AUTH_SALT',         '|M1HT2Y|T{Pa9R.x]Cx->n`%PLnd4aQP=Wx+FMeQa7y|fdfd9o^itSFii{h$Qsaq' );
define( 'SECURE_AUTH_SALT',  '7Z^UiE}p9K=}A7tO7T149j/2W_4?$@59xln]M.Gx*xoSl{KKD +Cow]@AE0Z>QIm' );
define( 'LOGGED_IN_SALT',    'jJ$pb`=T%bTo}*-3ijBpis]&@<P[&P>(Htr1Jk=o,<!yZR|oQoR8tV?flEs) .u$' );
define( 'NONCE_SALT',        '5;]n=j`,X;2#^<.hV^g|3Po+}-{#2p_LJ`#vWC|U8W=vrg%7m y8-;N ZH7u?>4`' );
define( 'WP_CACHE_KEY_SALT', 'O])zYb{)T$ayb(]Nu[{#unW<C&ji/18ScSOv1Nm[fC.94nDstM=2xDQvd{4gnI`p' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
