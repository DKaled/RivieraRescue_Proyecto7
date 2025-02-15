<?php
define( 'WP_CACHE', true );
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
define( 'DB_NAME', 'u856363392_P5WyA' );

/** Database username */
define( 'DB_USER', 'u856363392_j8ZsF' );

/** Database password */
define( 'DB_PASSWORD', 'EcriNKvsKt' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

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
define( 'AUTH_KEY',          'FPK~[l%b[VI60+*Q6wYj+^cRC@(XwwUH#dALK.fcc#y|%!~bK@y!XM=bi(z~YW6_' );
define( 'SECURE_AUTH_KEY',   'b75eW#X`ek&nw<JoWcuLXMf?!9v<!1Ll*@xr60a4`Eu[*f/9)^n/+9i4h[mb<8~c' );
define( 'LOGGED_IN_KEY',     'T^CtjP/dJ+QhUiyC|T1JG~}??vS]_1{;fIk4$DYt^Lh92JFlGJlC[+6/X|C;z95 ' );
define( 'NONCE_KEY',         'v/M#qWAScg3;@JhB0J+E!O:?;B,Z$loj<I/PS~|.NfWp=i>a6ZIR>A/ wc7PYF?{' );
define( 'AUTH_SALT',         'Gf]2Eu9 f-=vp-Dc6QaX=p+.Zj&)9QV+BJ!y)`!Nxo5y-R_e -.~8&YOlm)G;`{b' );
define( 'SECURE_AUTH_SALT',  '/[+JWRc=*&w`cjqH(^|YTVpdZ}IesWn`SeHh?q*zd0AqNU=4!Gk`lZiLvfsT?./`' );
define( 'LOGGED_IN_SALT',    'yW5|H?$1T}vOO}tf041j0RT8[.=tgVVW$[ zPcJXH4=@GD%hi?$LXY`4mt:0B`Rz' );
define( 'NONCE_SALT',        'E2W=_Th/^E2|[{@Uf0Nljh!#Kt!z,Rvn=+&]2=uHCF>;I%!&;BM6>pg([>]>z{ s' );
define( 'WP_CACHE_KEY_SALT', 'zG7M5(wl$H=hnkbA7]x(?(I$Bm)V7;MYa+`{tiZtTcqXu*|eL;v<WovDmn/!B-3E' );


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

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '62ce2ee5aaeaa75ddadde2ba92a100c5' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
