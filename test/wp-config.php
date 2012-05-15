<?php
/**
  * Базовая конфигурация WordPress.
 *
 * ВНИМАНИЕ: используйте для редактирования этого файла только правильные редакторы!!! Прочитайте README.HTML !!!
 *
 * Этот файл содержит следующие конфигурации: Настройки MySQL, Префикс таблиц,
 * Секретные ключи, Язык WordPress, и ABSPATH. Вы можете найти больше информации
 * посетив страницу {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} в Кодексе. Вы можете получить настройки MySQL у вашего хостера.
 *
 * Этот файл используется мастером создания wp-config.php во время инсталляции
 * Вам не нужно использовать сайт - просто скопируйте этот файл под именем
 *  "wp-config.php" и заполните значнения.
 *
 * @package WordPress
 */

// ** Настройки MySQL - Вы можете получить их у вашего хостера ** //
/** Имя базы данных для WordPress */
define('DB_NAME', 'wp_euro');

/** MySQL имя пользователя */
define('DB_USER', 'root');

/** MySQL пароль базы данных */
define('DB_PASSWORD', '');

/** MySQL сервер - иногда требуется изменять это значение. */
define('DB_HOST', 'localhost');

/** Кодировка базы данных, используемая при создании таблиц. */
define('DB_CHARSET', 'utf8');

/** Сопоставление базы данных. НЕ ИЗМЕНЯЙТЕ ЭТО ЗНАЧЕНИЕ. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи аутентификации.
 *
 * Измените ключи "put your unique phrase here" на уникальные фразы! Каждая фраза должна быть разной. Желательно с использованием латинских строчных и прописных букв, цифр, спецсимоволов.
 * Или просто сгенерируйте, открыв вот эту ссылку в браузере {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}  Затем скопируйте полученные строки ниже, заменив те что были до этого
 * Вы можете изменить их в любое время, чтобы аннулировать все существующие cookies. Это заставит всех пользователей повторно авторизоваться в системе.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'put your unique phrase here');
define('SECURE_AUTH_KEY',  'put your unique phrase here');
define('LOGGED_IN_KEY',    'put your unique phrase here');
define('NONCE_KEY',        'put your unique phrase here');
define('AUTH_SALT',        'put your unique phrase here');
define('SECURE_AUTH_SALT', 'put your unique phrase here');
define('LOGGED_IN_SALT',   'put your unique phrase here');
define('NONCE_SALT',       'put your unique phrase here');

/**#@-*/

/**
 * Префикс таблиц WordPress в базе данных.
 *
 * Вы можете иметь несколько инсталляций WordPress в одной базе, но поставив в каждой различные префиксы
 * Только цифры, латинские буквы и символ подчеркивания!
 */
$table_prefix  = 'wp_';

/**
 * Язык WordPress. Если не указан никакой, то будет английский!
 *
 * По умолчанию для русской локализации предлагается такой вариант: define ('WPLANG', 'ru_RU');
 */
define ('WPLANG', 'ru_RU');

/* Раскомментируйте строку ниже, чтобы влючить возможность создания сети сайтов */
/* Подробно про режим Сети и процедуру настройки, вы можете прочитать по ссылке http://codex.wordpress.org/Создание_сети */
/* define('WP_ALLOW_MULTISITE', true); */

/**
 * Для разработчиков: режим отладки WordPress.
 *
 * Измените значение на true чтобы включить вывод уведомлений.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своих средах разработки.
 */
define('WP_DEBUG', false);

/* Это все! Дальше не редактируйте. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
