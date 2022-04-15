## monPotager - Back

Une démo du site est disponible à cette 
[adresse](https://mon-potagerv2.surge.sh/)

## Installation sur serveur:

Placer le repo dans var/www/html sur le serveur, puis suivre le readme dans le dossier bin

## Installation en local:

Pour installer le backoffice en local:
- Télécharger le repo, 
- Se placer dans le dossier public, et lancer la commande 'composer install' pour télécharger les fichiers de Wordpress.
Si erreur 'no lock file', lancer la commande 'composer update'.

- Toujours dans le dossier public, créer et configurer le fichier wp-config.php

```php
<?php
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
define( 'DB_NAME', 'A modifier' );

/** MySQL database username */
define( 'DB_USER', 'A modifier' );

/** MySQL database password */
define( 'DB_PASSWORD', 'A modifier' );

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
define('AUTH_KEY',         'e0f$~OvFrk55Ry/a1u*8njS2|GSbTEx}BW+Gk2;ckOm] 1]!F* ~5Q h1(.>#d5L');
define('SECURE_AUTH_KEY',  '>$[`7.NI|Y[opvZG$U&|Q^F>Vt$Hm1`+(@V^a2RhJvb<RCIA+7Y-Q##hpnB}Zihz');
define('LOGGED_IN_KEY',    'B|Q{vJG~J<ayyb/K>+`uu8L+v  (/+Nz+BR<c~`ky`9k>@pp*%yEJEXzZlo$njuV');
define('NONCE_KEY',        'y0bBbrW>{-U<y#tC*;,37?FI>s+b)c;S G%?UrjafQ3-Rg/hPw-<q&y-[T@TM&?`');
define('AUTH_SALT',        '[_|1qo6`+7(+ble15t}P2-kOIZ(;+Y)GSv)-bt|c5lZeuQ`#4[n?g_]IqK+Iz.x0');
define('SECURE_AUTH_SALT', '{I4U44by2*-:7W<1QV`q][8[pDo5v&+pn-lRXL%]Uc!}7!|$:s-bzN/.u+KT,)q1');
define('LOGGED_IN_SALT',   '6WM4Ayx!8fqgjH_+Z9oV~c&W3S)Rn0nh#5+uVn]ghN!+4jx:t<+A?HqH4!b? >,q');
define('NONCE_SALT',       'LP2+D$ZVbiM0/|(jgL7+:!%1hdBw49VghMXU@Z8_L-fFJ<Ol?SX.~$7 +?q ^d;{');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
// AJOUT :
//----------------------------
//! url vers la home (la racine) du site wordpress
define('WP_HOME', rtrim ( 'A modifier', '/' ));


// nous spécifions dans quel dossier sont installés les fichiers de wordpress
define('WP_SITEURL', WP_HOME . '/wp');

define('WP_CONTENT_URL', WP_HOME . '/wp-content');
define('WP_CONTENT_DIR', __DIR__ . '/wp-content');


// on peut installer des plugins/theme directement depuis le backoffice
define('FS_METHOD','direct');
//--------------------------------------------

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

```

- Après avoir configurer le fichier (attention à bien modifier la racine de l'url du site), créer une base donnée et son utilisateur avec les mêmes accès rentrés dans le fichier.

- Puis lancer la commande d'installation,
```sh
wp core install --url="SITE_URL" --title="WORDPRESS_SITE_NAME" --admin_user="WORDPRESS_ADMIN_NAME" --admin_password="WORDPRESS_ADMIN_PASSWORD" --admin_email="WORDPRESS_ADMIN_EMAIL" --skip-email;
```

Exemple commande :
```sh
wp core install --url="http://localhost/PROMO/TRINITY/speWP/S01/wp-oProfile/public" --title="Démo install wp" --admin_user="admin" --admin_password="allo?" --admin_email="paul@super.beam" --skip-email;
```
