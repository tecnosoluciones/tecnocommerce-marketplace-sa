<?php

// BEGIN iThemes Security - No modifiques ni borres esta línea
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Desactivar editor de archivos - Seguridad > Ajustes > Ajustes TecnoCommerce > Editor de archivos
// END iThemes Security - No modifiques ni borres esta línea

define( 'WP_CACHE', false ); // Added by WP Rocket

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
* @link https://wordpress.org/support/article/editing-wp-config-php/
*
* @package WordPress
*/
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'marketpl_tcommmkt-sa' );
/** MySQL database username */
define( 'DB_USER', 'marketpl_tecnoadmin' );
/** MySQL database password */
define( 'DB_PASSWORD', 'lZDw$#7qR,M(' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
* Authentication Unique Keys and Salts.
*
* Change these to different unique phrases!
* You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
* You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
*
* @since 2.6.0
*/
define('AUTH_KEY',         '{b[@35nUyO?Vy;^_)]xx *zU0BGa#B7*mNTj52f4?wCEtzq9,50==~T5F(xRZ`pW');
define('SECURE_AUTH_KEY',  '!zi,hNmMi+t$4+q`e!Hf.+#/`c^<kNfVmgR5^nI/s/kCn@NpsnE,5|Bmj!,Rw]#>');
define('LOGGED_IN_KEY',    '3^nJB#XB+Q}5Tmn1{7Z|eH$QB?=<c.F?BJ{4CIZie$T3-$sF6C2FA!?W+Pj5cNXf');
define('NONCE_KEY',        '~|*]L7&gUez>/0:H&1eqs,1jCJ*%HAnX1^X5_Nr7ZR|9M=K9hGcj{F^,us+z%d`u');
define('AUTH_SALT',        'A9auJEEm+gYKV^A+-Cu)b8+L)R~f$8DZ~r .Jy6ToC,>8]yGbnd%,|&ZxnP?D`Z4');
define('SECURE_AUTH_SALT', '`~ypI- $ll-mClImr|sBPDWOj#/z]&xL_UhFp?5+Gib$f-*T`+tOY]-y:a#%bD>l');
define('LOGGED_IN_SALT',   '_BZctc<< ^fH}>qj:t7.#C+d%`A. `-fa,;Es3RU*~KQXN:W+n.D{HB5^S!Q,.R+');
define('NONCE_SALT',       '9kvr_?qbUOL+|/2:S Id,o6l3T^mm.;?g{N:*[R@1EZw$FZKlz::HuHinIP-TS=7');
/**#@-*/
/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each
* a unique prefix. Only numbers, letters, and underscores please!
*/
$table_prefix = 'wptsc_';
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
define( 'WP_DEBUG', false );
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
