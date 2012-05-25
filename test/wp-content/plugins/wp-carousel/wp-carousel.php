<?php
	/*
		Plugin Name: WP Carousel
		Plugin URI: http://sumolari.com/?p=1759
		Description: A great carousel manager for WordPress
		Version: 1.1
		Author: Sumolari
		Author URI: http://sumolari.com
	*/
	
	define('WP_CAROUSEL_VERSION', 1.1); // 0.6 = 0.60 < 0.6X < 0.70 == 0.7

	/*
		Copyright 2011 Lluís Ulzurrun de Asanza Sàez  (email : info@sumolari.com, sumolari@gmail.com)
	
		This program is free software; you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation; either version 2 of the License, or
		(at your option) any later version.
	
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
	
		You should have received a copy of the GNU General Public License
		along with this program; if not, write to the Free Software
		Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/
	
	@include('wp-carousel-config.php');
	
	/* If there is no config file, then load the sample config file */
	if (!isset($wp_carousel_config_file)) { @include('wp-carousel-config-sample.php'); }
	
	if (!isset($wp_carousel_config_file))
	{
		/*
			Change this value to TRUE to enable the WP Carousel's External Integration option
		*/
		
		define('WP_CAROUSEL_EI', false);
		$wp_carousel_external_integration_line = __LINE__ - 1;
	
		/*
			Change this value to FALSE to show images instead of videos
		*/
		
		define('WP_CAROUSEL_SHOW_VIDEOS_FIRST', true);
		$wp_carousel_videos_first_line = __LINE__ - 1;
		
		/*
			Change this value to FALSE to disable auto-backups
		*/
		
		define('WP_CAROUSEL_AUTOSAVE_BACKUPS', true);
		$wp_carousel_autosave_backups_line = __LINE__ - 1;
		
		/*
			Definimos los nombres de las opciones de WordPress que usa WP Carousel
		*/
		
		define('WP_CAROUSEL_ITEMS_TABLE', 'wp_carousel');			// Tabla para los elementos del carrusel
		define('WP_CAROUSEL_CONFIG_TABLE', 'wp_carousel_config');	// Tabla para las configuraciones del carrusel
		define('WP_CAROUSEL_BACKUP_TABLE', 'wp_carousel_backup');	// Tabla para almacenar las copias de seguridad
		
		/*
			Definimos los nombres de los campos personalizados de WP Carousel - Custom fields used by WP Carousel
		*/
		
		define('WP_CAROUSEL_CAROUSEL_TEXT', 'wp_carousel_carousel_text');	// Texto que usará el carrusel como descripción del contenido
		define('WP_CAROUSEL_IMAGE_URL', 'wp_carousel_image_url');			// URL de la imagen
		define('WP_CAROUSEL_LINK_URL', 'wp_carousel_link_url');				// URL del enlace
		define('WP_CAROUSEL_VIDEO_URL', 'wp_carousel_video_url');			// URL del vídeo
		
		/*
			Definimos los elementos que se cargarán y estarán disponibles en la página de opciones del carrusel
			
			Si por algún motivo no termina de cargar la página de opciones del carrusel, es posible que haya demasiado contenido para iterar sobre él en el tiempo de ejecución límite establecido en la configuración de PHP. Para evitar estos errores he añadido la posibilidad de eliminar las cajas con contenido demasiado grande como para mostrarse.
			
			- - - - -
			
			Are you having issues like page doesn't loading? Try disabling some of the "addable items boxes" of the Carousel's Edit Page (change 1 to 0)
			
		*/
		
		define('WP_CAROUSEL_SHOW_CATEGORIES_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir categorías al carrusel
		define('WP_CAROUSEL_SHOW_TAGS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir tags al carrusel
		define('WP_CAROUSEL_SHOW_POSTS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir artículos al carrusel
		define('WP_CAROUSEL_SHOW_PAGES_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir páginas al carrusel
		define('WP_CAROUSEL_SHOW_AUTHORS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir autores al carrusel
		
		/*
			Definimos el máximo de artículos a partir del cual dejará de mostrarse un menú desplegable en la lista de elementos individuales para solicitarse la ID.
			
			We define the maximum number of posts to load for the list of posts. If you are having issues like dissapearing part of the carousel's option page, try to use a lower value.
		*/
		
		define('WP_CAROUSEL_ITEMS_COUNT_LIMIT', 200); // 1000? 10000? I need more stats to set a better limit!
		
		/*
			Definimos el tipo de miniatura que se cargará por defecto: Se aceptan los siguientes valores:
				
				- thumbnail
				- medium
				- large
				
		*/
		
		define('WP_CAROUSEL_DEFAULT_THUMBNAIL_SIZE', 'thumbnail');	// Tamaño de la miniatura del carrusel
		$wp_carousel_thumbnail_size_line = __LINE__ - 1;
		
		/*
			Si hay más artículos que el límite, ¿se debe mostrar los últimos N (N = WP_CAROUSEL_ITEMS_COUNT_LIMIT) en lugar del cuadro de texto para indicar la ID?
			
			- - - - - - - -
			
			Where there are more posts than WP_CAROUSEL_ITEMS_COUNT_LIMIT, it can be shown the last WP_CAROUSEL_ITEMS_COUNT_LIMIT posts (value true) or the text input for IDs (value false)
		*/
		
		define('WP_CAROUSEL_SHOW_LAST_POSTS_INSTEAD_OF_ID_INPUT', false);
		$wp_carousel_force_last_posts_instead_of_id_input_line = __LINE__ - 1;
		
		$wp_carousel_config_file = __FILE__;
	}
		
	/*
		Definimos las funciones soportables de los themes: las que se soportan por defecto y las que no
	*/
		
	$wp_carousel_default_supported_supportable_features = array('arrows', 'panel_size', 'image_size', 'pagination');
	$wp_carousel_default_unsupported_supportable_features = array('jcarousel', 'carousel_size', 'vertical_mode', 'nivo');
	$wp_carousel_supportable_features = array_merge($wp_carousel_default_supported_supportable_features, $wp_carousel_default_unsupported_supportable_features);
	
	/*
		Definimos la URL de la encuesta para mejorar WP Carousel
	*/
		
	if (!defined('WP_CAROUSEL_SURVEY')) define('WP_CAROUSEL_SURVEY', 'http://polldaddy.com/s/003E291E95464CC7');
	
	/*
		Definimos WP_CONTENT_URL y WP_PLUGIN_URL, si es que no están definidos ya
	*/
	
	if (!defined( 'WP_CONTENT_URL' ) ) define( 'WP_CONTENT_URL', get_bloginfo('wpurl').'/wp-content');
	if (!defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	
	/*
		Definimos el nombre de la variable de sesión que indicará si actualmente se está en el bucle (loop) de WordPress o no
	*/
	
	define('WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_IS_IN_LOOP_INDICATOR', 'wp_carousel_is_in_loop');
	$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_IS_IN_LOOP_INDICATOR] = false;
	
	/*
		Definimos el nombre de la variable de sesión que indicará los carruseles que ya han sido mostrados para evitar mostrar varias veces el mismo carrusel
	*/
	
	define('WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN', 'wp_carousel_carousels_that_have_been_shown');
	$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN] = array();
	
	/*
		Creamos la matriz $wp_carousel_path, que almacenará:
			[1] -> Ruta a este archivo, partiendo de: wp-content/plugins .
			[2] -> Nombre de la carpeta que contiene a este archivo, por defecto es wp-carousel, pero puede ser modificado por el usuario.
			[3] -> Ruta a este archivo, partiendo de la carpeta que contiene los archivos de WordPress
			[4] -> Equivalente a WP_CONTENT_URL, sólo que con una barra al final
			[5] -> Equivalente a WP_PLUGIN_URL, sólo que con una barra al final
			[6] -> Ruta a la carpeta que contiene a este archivo (con barra), partiendo de la carpeta que contiene los archivos de WordPress
			[7] -> Nombre de la carpeta generalmente conocida como wp-admin
			[8] -> Nombre de la carpeta generalmente conocida como wp-content
	*/
	
	$wp_carousel_path[1] = plugin_basename(__FILE__);
	$wp_carousel_path[2] = ereg_replace('/wp-carousel.php', '', $wp_carousel_path[1]);
	$wp_carousel_path[3] = WP_PLUGIN_URL.'/'.$wp_carousel_path[1];
	$wp_carousel_path[4] = WP_CONTENT_URL.'/';
	$wp_carousel_path[5] = WP_PLUGIN_URL.'/';
	$wp_carousel_path[6] = ereg_replace('wp-carousel.php', '', $wp_carousel_path[3]);
	$wp_carousel_path[7] = ereg_replace('/wp-carousel.php', '', $wp_carousel_path[1]);
	$wp_carousel_path[8] = ereg_replace('/', '', ereg_replace(get_bloginfo('wpurl'), '', $wp_carousel_path[4]));
			
	/*
		Cargamos los archivos del idioma correspondiente
	*/
		
	$currentLocale = get_locale();
	if(!empty($currentLocale)) 
	{
		$moFile = dirname(__FILE__) . "/language/" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('wp_carousel', $moFile);
	}
	
	/*
		Cargamos aquí todas las frases de los themes para ser traducidas y evitar problemas con Gettext
	*/
	
	$wp_carousel_translate_temp = array();
	$wp_carousel_translate_temp[] = __('There was an error, please, report it in the forum and attach this error message:', 'wp_carousel');
	$wp_carousel_translate_temp[] = __('Changes saved', 'wp_carousel');
	$wp_carousel_translate_temp[] = __('There was an error!', 'wp_carousel');
	
	unset($wp_carousel_translate_temp);
	
	/*
		Habilitamos las miniaturas de los artículos
	*/
	
	if (function_exists('add_theme_support')) add_theme_support('post-thumbnails');
	
	/*
		Registremos todos los archivos JS que cargaremos en un momento o en otro, así los tenemos todos juntitos y no perdemos tiempo buscando
	*/
	
	function wp_carousel_register_scripts()
	{	
		global $wp_carousel_path;
		
		wp_register_script('wp_carousel_edit_carousel_init_js', $wp_carousel_path[6].'js/edit.carousel.init.js.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-tabs')); // Código Javascript necesario para las páginas de opciones de cada carrusel
		wp_register_script('wp_carousel_iphone_style_checkboxes_js', $wp_carousel_path[6].'js/iphone.style.checkboxes.js'); // Código encargado de darle un mejor aspecto a ciertos checkboxes
		wp_register_script('wp_carousel_jcarousel', $wp_carousel_path[6].'js/jcarousel.min.js', array('jquery'), false, true); // jCarusel (tomado de http://sorgalla.com/jcarousel/)
		wp_register_script('wp_carousel_nivoslider', $wp_carousel_path[6].'js/jquery.nivo.slider.js', array('jquery'), false, true); // NivoSlider, el script printipal para crear los carruseles (tomado de http://nivo.dev7studios.com/)
		wp_register_script('wp_carousel_init_all_stepcarousel', $wp_carousel_path[6].'js/init.all.stepcarousel.php', array('wp_carousel_stepcarousel')); // Configuraciones de todos los carruseles
		
		// Creamos los iniciadores de cada carrusel individual
		$wp_carousel_config_temp = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
		if (is_array($wp_carousel_config_temp))
		{
			foreach ($wp_carousel_config_temp as $key => $value)
			{
				wp_register_script('wp_carousel_init_stepcarousel_carousel_'.$key, $wp_carousel_path[6].'js/init.stepcarousel.php?id='.$key, array('wp_carousel_stepcarousel'), false, true); // Configuraciones del carrusel con ID $key
			}
		}
		unset($wp_carousel_config_temp);
	}
	add_action('init', 'wp_carousel_register_scripts');
	
	/*
		Registramos el CSS de Nivo Slider
	*/
	
	function wp_carousel_register_nivoslider_css()
	{
		global $wp_carousel_path;
		wp_register_style('wp_carousel_nivo_slider_css', $wp_carousel_path[6].'css/nivo_slider.css');
		wp_enqueue_style('wp_carousel_nivo_slider_css');
	}
	add_action('init', 'wp_carousel_register_nivoslider_css');
	
	/*
		Cargamos todas las acciones que se añaden
	*/
	
	add_action('admin_menu', 'wp_carousel_adminmenu_links'); // Menú de WP Carousel
	add_action('widgets_init', create_function('', 'return register_widget("WP_Carousel_Widget");')); // Widget de WP Carousel
	
	/*
		Comprobamos si tenemos que borrar algún carrusel o  si tenemos que hacer un guardado NO-AJAX
	*/
	
	if (strpos(wp_carousel_create_internal_urls('SELF_URL'), 'admin.php') !== false)
	{
		
		if (isset($_GET['action']))
		{
			$action = explode(':', $_GET['action']);
			switch ($action[0])
			{
				case 'DELETE_CAROUSEL':
					if (isset($_GET['sure']))
					{
						if ($_GET['sure'] == 'yes')
						{
							$items = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE));
							unset($items[$action[1]]);
							$items_db = serialize($items);
							update_option(WP_CAROUSEL_ITEMS_TABLE, $items_db);
							unset($items);
							
							$items = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
							unset($items[$action[1]]);
							$items_db = serialize($items);
							update_option(WP_CAROUSEL_CONFIG_TABLE, $items_db);
							unset($items);
						}
					}
					break;
				case 'DELETEBACKUPS':
					delete_option(WP_CAROUSEL_BACKUP_TABLE);
					break;
				case 'UNINSTALL':
					delete_option(WP_CAROUSEL_ITEMS_TABLE);
					delete_option(WP_CAROUSEL_CONFIG_TABLE);
					delete_option(WP_CAROUSEL_BACKUP_TABLE);
				default:
					break;
			}
		}
	
	}
	
	function wp_carousel_enqueue_admin_js()
	{
		wp_enqueue_script('wp_carousel_edit_carousel_init_js'); // Para crear el sistema AJAX
		wp_enqueue_script('wp_carousel_iphone_style_checkboxes_js'); // Código encargado de darle un mejor aspecto a ciertos checkboxes
		wp_enqueue_script('wp_carousel_jcarousel'); // Código JS para el carrusel de themes
	}
	
	function wp_carousel_enqueue_public_scripts()
	{
		wp_enqueue_script('wp_carousel_jcarousel');
		wp_enqueue_script('wp_carousel_nivoslider');
	}
	
	/*
		Comprobamos si estamos en una página de opciones de algún carrusel
	*/
	
	if (isset($_GET['page']))
	{
		if ($_GET['page'] == 'wp-carousel-add-theme')
		{
			$items = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE));
			$items[] = array();			
			$items_db = serialize($items);
			update_option(WP_CAROUSEL_ITEMS_TABLE, $items_db);
			$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
			$config[] = array(
				'THEME' => 'default',
				'SHOW_ARROWS' => '1',
				'SLIDE_POSTS' => '1',
				'ENABLE_PAGINATION' => '1',
				'AUTOSLIDE_TIME' => '3000',
				'AUTOSLIDE_POSTS' => '1',
				'IMG_WIDTH' => '',
				'IMG_HEIGHT' => '',
				'PANEL_WIDTH' => '',
				'LOOP_MODE' => '1'
			);					
			$config_db = serialize($config);
			update_option(WP_CAROUSEL_CONFIG_TABLE, $config_db);
		}
			
		if (strrpos($_GET['page'], 'edit-carousel-') === false && strrpos($_GET['page'], 'wp-carousel') === false)
		{
			// No estamos editando carruseles
			if ($_GET['page'] == 'wp-carousel-backup')
			{				
				
				add_action('init', 'wp_carousel_enqueue_admin_js');
				
				// Función para imprimir estilos CSS y el inicializador del estilo iPhone de los checkboxes
				function wp_carousel_print_edit_carousel_page_css()
				{
					global $wp_carousel_path;
					echo '<link rel="stylesheet" href="'.$wp_carousel_path[6].'css/wp_carousel_admin_panel.css'.'" type="text/css" media="all" />';
				}
				add_action('admin_head', 'wp_carousel_print_edit_carousel_page_css');
				
			}
		}
		else
		{
			// ¡Bingo! Tenemos cosas que hacer, como cargar el código Javascript :)
			add_action('init', 'wp_carousel_enqueue_admin_js');
			
			// Función para imprimir estilos CSS y el inicializador del estilo iPhone de los checkboxes
			function wp_carousel_print_edit_carousel_page_css()
			{
				global $wp_carousel_path;
				echo '<link rel="stylesheet" href="'.$wp_carousel_path[6].'css/wp_carousel_admin_panel.css'.'" type="text/css" media="all" />';
				?>
				<script type="text/javascript" language="javascript">
				jQuery(document).ready(function($) {
					$("#items_in_carousel .wp_carousel_ajax_form :checkbox").iphoneStyle({
						checkedLabel: "<?php _e('Yes', 'wp_carousel'); ?> ", 
						uncheckedLabel: "<?php _e('No', 'wp_carousel'); ?>",
						resizeContainer: false,
						resizeHandle: true
					});
					$("#standard_options :checkbox").iphoneStyle({
						checkedLabel: "<?php _e('Yes', 'wp_carousel'); ?> ", 
						uncheckedLabel: "<?php _e('No', 'wp_carousel'); ?>",
						resizeContainer: false,
						resizeHandle: true
					});
				});
				</script>
				<?php
			}
			add_action('admin_head', 'wp_carousel_print_edit_carousel_page_css');
			
		}
	}
	else
	{
		/*
			Cargamos el código JS y CSS del carrusel
		*/
		if (!is_admin())
		{
			unset($wp_carousel_config_temp);
			
			add_action('init', 'wp_carousel_enqueue_public_scripts');
			
			add_action('wp_head', 'wp_carousel_load_theme_css'); // Cargamos el código CSS de los themes
			add_action('wp_head', 'wp_carousel_load_theme_js'); // Cargamos el código Javascript de los themes
		}
	}
	
	/*
		@Función: wp_carousel_load_extras()
		@Versión: 1.1
		@Parámetros:
								$debug (bool): Sólo para mantener compatibilidad con código antiguo
								$dir: Si se carga desde una carpeta distinta, puede ser necesario establecer un valor inicial para $dir
		@Descripción: Carga los plugins instalados en WP Carousel para poder ser usados
		@Añadida en la versión: 0.5	
		@Actualizada en la versión: 1.0
	*/
	
	function wp_carousel_load_extras($debug=false, $dir = '')
	{
		
		global $wp_carousel_path;
						
		if (is_admin())
		{
			$dir .= '../';
		}
						
		$dir .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/extras';
			
		if (is_dir($dir))
		{
			if ($handle = opendir($dir))
			{
				$extras = array();
				while (($file = readdir($handle)) !== false)
				{
					if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn')
					{
						$extras[] = $file;
					}
				}
			closedir($handle);
			} 
		}
		
		if (isset($extras[0]))
		{
		
			foreach ($extras as $temp_key => $temp_value)
			{
				$to_check_information_file = '';
				$to_check_functions_file = '';
				
				if (is_admin())
				{
					$to_check_information_file .= '../';
					$to_check_functions_file .= '../';
				}
				
				$to_check_information_file .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/extras/'.$temp_value.'/index.php';
				$to_check_functions_file .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/extras/'.$temp_value.'/extra.php';
							
				if (is_file($to_check_information_file))
				{
					
					include($to_check_information_file);
					
					if (is_file($to_check_functions_file))
					{
						$extras[$temp_value] = $extra;
						require_once($to_check_functions_file);
						unset($extras[$temp_key]);
					}
										
					if (isset($extra['js']))
					{
						if (is_array($extra['js']))
						{
							foreach ($extra['js'] as $js_key => $js_file)
							{
																
								eval ("
								
								function load_wp_carousel_extra_".$temp_value."_js_file_".$js_key."()
								{
									echo '<script src=\'".get_option('siteurl')."/".$wp_carousel_path[8]."/plugins/".$wp_carousel_path[2]."/extras/".$temp_value."/".$js_file."\' type=\'text/javascript\'></script>';
								}
								
								add_action('wp_footer', 'load_wp_carousel_extra_".$temp_value."_js_file_".$js_key."');
								
								");
								
							}
						}
					}
					
					unset($extra);
					
				}
				
			}
			
		}
		
		if (!isset($extras))
		{
			$extras = false;
		}
				
		if (isset($_SESSION['WP_CAROUSEL_CUSTOM_EXTRAS']))
		{
			if (is_array($_SESSION['WP_CAROUSEL_CUSTOM_EXTRAS']))
			{
				foreach ($_SESSION['WP_CAROUSEL_CUSTOM_EXTRAS'] as $extra_key => $extra_item)
				{
					$extras[$extra_item['name']] = $extra_item;
				}
			}
		}
		
		$_SESSION['WP_CAROUSEL_EXTRAS'] = $extras;
		
	}
	
	/*
		@Función: wp_carousel_get_theme_information()
		@Versión: 1.0
		@Parámetros:
								$theme_name (string): Nombre del theme del que se quiere la información, ALL devuelve de todos los themes
								$debug (bool): Sólo para mantener compatibilidad con código antiguo
								$dir: Si se carga desde una carpeta distinta, puede ser necesario establecer un valor inicial para $dir
		@Descripción: Devuelve una matriz (array) con información acerca del theme
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_get_theme_information($theme_name='ALL', $debug=false, $dir='')
	{	
		global $wp_carousel_path, $wp_carousel_default_supported_supportable_features, $wp_carousel_default_unsupported_supportable_features;
		
		// Antes de nada, veamos si estamos ejecutando la función en modo debug
						
		if (is_admin())
		{
			$dir .= '../';
		}
						
		$dir .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/themes';
		
		$themes = array();
			
		if (is_dir($dir))
		{
			if ($handle = opendir($dir))
			{
				while (($file = readdir($handle)) !== false)
				{
					if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn')
					{
						$themes[] = $file;
					}
				}
			closedir($handle);
			} 
		}
		
		foreach ($themes as $temp_key => $temp_value)
		{
			$to_check_information_file = '';
			$to_check_theme_file = '';
			
			if (is_admin())
			{
				$to_check_information_file .= '../';
				$to_check_theme_file .= '../';
			}
			
			$to_check_information_file .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/themes/'.$temp_value.'/index.php';
			$to_check_theme_file .= $wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/themes/'.$temp_value.'/theme.php';
						
			if (is_file($to_check_information_file))
			{
				if (is_file($to_check_theme_file))
				{
					include($to_check_information_file);
					
					if (!isset($theme['supports']))
					{
						$theme['supports'] = array();
					}
					
					foreach ($wp_carousel_default_supported_supportable_features as $key => $value)
					{
						if (!isset($theme['supports'][$value]))
						{
							$theme['supports'][$value] = true;
						}
					}
					
					foreach ($wp_carousel_default_unsupported_supportable_features as $key => $value)
					{
						if (!isset($theme['supports'][$value]))
						{
							$theme['supports'][$value] = false;
						}
					}
					
					$themes[$temp_value] = $theme;
					unset($theme);
					unset($themes[$temp_key]);
				}
			}

			
		}
	
		if ($theme_name == 'ALL')
		{
			return $themes;
		}
		else
		{
			return (isset($themes[$theme_name])) ? $themes[$theme_name] : false;
		}
		
	}
	
	/*
		@Función: wp_carousel_custom_help_tab()
		@Versión: 2.0
		@Descripción: Añade la función wp_carousel_custom_help_tab_filter al filtro de la pestaña de ayuda
		@Actualizada en la versión: 1.1
		@Añadida en la versión: 0.5	
	*/
	
	function wp_carousel_custom_help_tab()
	{
	   add_filter('contextual_help', 'wp_carousel_custom_help_tab_filter');
	}
	
	/*
		@Función: wp_carousel_custom_help_tab_for_wordpress_three_dot_three()
		@Versión: 1.0
		@Descripción: Añade la ayuda de WP Carousel a la ventana de ayuda de WordPress 3.3
		@Añadida en la versión: 1.1
	*/
	
	add_action('admin_print_scripts', 'wp_carousel_custom_help_tab_for_wordpress_three_dot_three');
	function wp_carousel_custom_help_tab_for_wordpress_three_dot_three()
	{
		global $current_screen;
		
		if (!class_exists('WP_Screen'))
		{
			return;
		}
	
		if (!method_exists($current_screen, 'add_help_tab'))
		{
			return;
		}
	
		$total = wp_carousel_custom_help_tab_filter('', false, '-1', 'count');
		for ($i = 0; $i < $total; $i++)
		{
			$current_screen->add_help_tab( array(
				'id'      => 'wp-carousel-section-'.$i,
				'title'   => wp_carousel_custom_help_tab_filter('', false, $i, 'title'),
				'content' => wp_carousel_custom_help_tab_filter('', false, $i, 'content'),
			));
		}

	}
	
	/*
		@Función: wp_carousel_custom_help_tab_filter()
		@Versión: 1.1
		@Parámetros:
								$help: El contenido inicial de la pestaña
								$echo (BOOL): false devuelve el contenido, true lo imprime
								$section (int): numerode la seccion a mostrar/devolver, comenzando por 0. -1 devuelve todas las secciones
								$item ('title' | 'content' 'count'): Por defecto 'content'. 'content' es el unico valor posible cuando $section == -1 o echo == true.  Si se establece el valor 'title' devuelve la cabecera (titulo) de la seccion especificada, en caso de ser 'content' se devuelve el contenido, en caso de ser 'count' se devuelve el total de secciones
		@Descripción: Modifica el contenido de la pestaña de ayuda de WordPress
		@Añadida en la versión: 0.5	
		@Actualizada en la versión: 1.0	
	*/
	
	function wp_carousel_custom_help_tab_filter($help, $echo = true, $section = -1, $item = 'content')
	{
		global $wp_carousel_thumbnail_size_line, $wp_carousel_external_integration_line, $wp_carousel_videos_first_line, $wp_carousel_autosave_backups_line, $wp_carousel_config_file, $wp_carousel_posts_limit_line, $wp_carousel_force_last_posts_instead_of_id_input_line;
		
	 	if (!in_array($item, array('count', 'title', 'content')))
		{
			$item = 'content';
		}
		
		$section_titles = array(
			__('General', 'wp_carousel'), 
			__('Common problems &amp; solutions', 'wp_carousel'), 
			__('Baisc carousel management', 'wp_carousel'), 
			__('Showing carousels', 'wp_carousel'), 
			__('Carousel content\'s priority', 'wp_carousel'), 
			__('Other', 'wp_carousel')
		);
		
		$return = array();
		
		$return[0] = $return[0] = sprintf ("<p>".__('Please, fill up <a href="%s">this survey</a> in order to improve WP Carousel - Note that you can fill up the survey at any moment', 'wp_carousel').".</p>", WP_CAROUSEL_SURVEY);
		$return[0] = $return[0].'<hr class="wp_carousel_help_separator" />';
		$return[0] = $return[0]."<h5>".__('WP Carousel\'s Quick Help', 'wp_carousel')."</h5>";
		$return[0] = $return[0].'<p>';
		$return[0] = $return[0].sprintf(__('Did you find any error? Please, report them <a href="%s">here (English)</a> or <a href="%s">here (Spanish)</a>.', 'wp_carousel'), 'http://sumolari.com/forums/forum/wp-carousel-2/', 'http://sumolari.com/forums/forum/wp-carousel-2/');
		$return[0] = $return[0].'</p>';
		$return[0] = $return[0].'<hr class="wp_carousel_help_separator" />';
		
		
		$return[1] = $return[1] = "<h5>".__('My carousel does not work: it shows scrollbars and does not slide!', 'wp_carousel')."</h5>";
		$return[1] = $return[1].'<p>';
		$return[1] = $return[1].sprintf(__('Open your WordPress theme footer.php file and check if there\'s a line like: <code>%s</code>. If there is not any line like that, just add that code at the end of the file.', 'wp_carousel'), '&lt;?php wp_footer(); ?&gt;');
		$return[1] = $return[1].'</p>';
		$return[1] = $return[1]."<h5>".__('I have added a post with a video to the carousel and the video is not shown', 'wp_carousel')."</h5>";
		$return[1] = $return[1].'<p>';
		$return[1] = $return[1].__('Some themes do not support videos. If your current WP Carousel theme supports videos, check if the size of the video is bigger than the size of the panel. If it is, the video won\'t be shown.', 'wp_carousel');
		$return[1] = $return[1].'</p>';
		$return[1] = $return[1]."<h5>".__('When I add a post with a video and an image, the carousel shows the video instead of the image', 'wp_carousel')."</h5>";
		$return[1] = $return[1]."<p>";
		$return[1] = $return[1].sprintf(__('When a post has a video and an image, WP Carousel shows the video instead of the image. You can reverse this behaviour by replacing code in line %s in file <code>%s</code> by this: <code>%s</code>', 'wp_carousel'), $wp_carousel_videos_first_line, $wp_carousel_config_file, "define('WP_CAROUSEL_SHOW_VIDEOS_FIRST', false);");
		$return[1] = $return[1]."</p>";
		$return[1] = $return[1]."<h5>".__('There are some "Items" boxes missing! I can see until "Post" but no more!', 'wp_carousel')."</h5>";
		$return[1] = $return[1]."<p>";
		$return[1] = $return[1].sprintf(__('WP Carousel loads by default all the posts of your blog to display a list of them to allow you to choose the one you want to show without having to know its ID, but when you have a lot of posts, WP Carousel needs too much memory and time to show the list. By default when you have more than %s posts, WP Carousel shows a text input instead of a dropdown list, but in some cases %s posts are too much. You can reduce this number by changing line %s in file <code>%s</code>', 'wp_carousel'), WP_CAROUSEL_ITEMS_COUNT_LIMIT, WP_CAROUSEL_ITEMS_COUNT_LIMIT, $wp_carousel_posts_limit_line, $wp_carousel_config_file);
		$return[1] = $return[1]."</p>";
		$return[1] = $return[1].'<hr class="wp_carousel_help_separator" />';
		
		$return[2] = $return[2] = "<h5>".__('How to add items to a carousel', 'wp_carousel')."</h5>";
		$return[2] = $return[2].'<p>';
		$return[2] = $return[2].__('Simply drag the kind of item you want to add and drop it in the carousel\'s area. When you have done that, you\'ll see new options to set up the item you want to add. ', 'wp_carousel');
		$return[2] = $return[2].'</p>';
		$return[2] = $return[2]. "<h5>".__('How to change the carousel\'s name', 'wp_carousel')."</h5>";
		$return[2] = $return[2].'<p>';
		$return[2] = $return[2].__('Click on the current name, change it and then press Enter. ', 'wp_carousel');
		$return[2] = $return[2].'</p>';
				
		$return[3] = $return[3] = "<h5>".__('How to show a carousel', 'wp_carousel')."</h5>";
		$return[3] = $return[3].'<p>';
		$return[3] = $return[3].__('You can show a carousel by three ways:', 'wp_carousel');
		$return[3] = $return[3].sprintf('<ol><li>%s</li><li>%s</li><li>%s</li></ol>', sprintf(__('Adding <code>%s</code> in your WordPress theme.', 'wp_carousel'), 'wp_carousel(CAROUSEL_ID)'), __('Adding WP Carousel\'s Widget to the sidebar.', 'wp_carousel'), sprintf(__('Adding the shortcode <code>%s</code> in the post / page you want to show the carousel.', 'wp_carousel'), '[wp_carousel]ID[/wp_carousel]'));
		$return[3] = $return[3].'</p>';
		$return[3] = $return[3]."<h5>".__('WP Carousel\'s External Integration Mode', 'wp_carousel')."</h5>";
		$return[3] = $return[3].'<p>';
		$return[3] = $return[3].sprintf(__('With WP Carousel\'s External Integration Mode you can show a WP Carousel carousel from a WordPress blog in your WordPress blog. To do this, both blogs must have enabled this mode, which is disabled by default for security reasons. To enable it, just go to line %s in file %s, search %s and replace it with %s ', 'wp_carousel'), $wp_carousel_external_integration_line, '<code>'.$wp_carousel_config_file.'</code>', "<code>define('WP_CAROUSEL_EI', false);</code>", "<code>define('WP_CAROUSEL_EI', true);</code>");
		$return[3] = $return[3].'</p>';
		$return[3] = $return[3].'<hr class="wp_carousel_help_separator" />';
	
		$return[4] = $return[4] = "<h5>".__('Posts\'s images for the carousel', 'wp_carousel')."</h5>";
		$return[4] = $return[4].sprintf("%s %s %s %s %s %s %s %s %s", "<p>", __('WP Carousel loads the image for the carousel in the following order:', 'wp_carousel'), "<ol><li>", __('Value of custom field', 'wp_carousel'), " <em>".WP_CAROUSEL_IMAGE_URL."</em></li><li>", sprintf(__('Post thumbnail (you can set thumbnail size by editing line %s in file %s)', 'wp_carousel'), $wp_carousel_thumbnail_size_line, '<code>'.$wp_carousel_config_file.'</code>'), "</li><li>", __('First image in the post', 'wp_carousel'), "</li></ol></p>");
		$return[4] = $return[4]."<h5>".__('Posts\'s video for the carousel', 'wp_carousel')."</h5>";
		$return[4] = $return[4].sprintf("%s %s %s %s %s %s %s", "<p>", __('WP Carousel loads the video for the carousel in the following order:', 'wp_carousel'), "<ol><li>", __('Value of custom field', 'wp_carousel'), " <em>".WP_CAROUSEL_VIDEO_URL."</em></li><li>", __('First video in the post', 'wp_carousel'), "</li></ol></p>");
		$return[4] = $return[4]."<h5>".__('Posts\'s text for the carousel', 'wp_carousel')."</h5>";
		$return[4] = $return[4].sprintf("%s %s %s %s %s %s %s", "<p>", __('WP Carousel loads the text for the carousel in the following order:', 'wp_carousel'), "<ol><li>", __('Value of custom field', 'wp_carousel'), " <em>".WP_CAROUSEL_CAROUSEL_TEXT."</em></li><li>", __('Posts\'s exceprt', 'wp_carousel'), "</li></ol></p>");
		$return[4] = $return[4].'<hr class="wp_carousel_help_separator" />';
		
		$return[5] = $return[5] = "<h5>".__('I do not want WP Carousel to save backups', 'wp_carousel')."</h5>";
		$return[5] = $return[5]."<p>";
		$return[5] = $return[5].sprintf(__('To disable autosaving backups, simply replace line %s in file <code>%s</code> by this: <code>%s</code>', 'wp_carousel'), $wp_carousel_autosave_backups_line, $wp_carousel_config_file, "define('WP_CAROUSEL_AUTOSAVE_BACKUPS', false);");
		$return[5] = $return[5]."</p>";
		$return[5] = $return[5]. "<h5>".__('I prefer that WP Carousel shows the last posts instead of the ID text input', 'wp_carousel')."</h5>";
		$return[5] = $return[5]."<p>";
		$return[5] = $return[5].sprintf(__('To show the last %s posts instead of showing the ID text input, just change <code>%s</code> to <code>%s</code> in line %s of file <code>%s</code>.', 'wp_carousel'), WP_CAROUSEL_ITEMS_COUNT_LIMIT, "false", "true", $wp_carousel_force_last_posts_instead_of_id_input_line, $wp_carousel_config_file);
		$return[5] = $return[5]."</p>";
		$return[5] = $return[5]. "<h5>".__('How can I create a config file that stores my settings to prevent losing them with newer updates of WP Carousel?', 'wp_carousel')."</h5>";
		$return[5] = $return[5]."<p>";
		$return[5] = $return[5].sprintf(__('In WP Carousel 1.1 you can create a file name <code>%s</code> to store the config. There is a config sample file name <code>%s</code>. Just modify its value and rename it to <code>%s</code> and your settings will not be lost when updating to newer versions. Be sure that you replace the changed files when upgrading, not just replacing the entire folder.', 'wp_carousel'), 'wp-carousel-config.php', 'wp-carousel-config-sample.php', 'wp-carousel-config.php');
		$return[5] = $return[5]."</p>";
		
		if ($echo)
		{
			echo implode('', $return);
		}
		else
		{
						
			if ($item == 'count')
			{
				return count($section_titles);
			}
			
			if ($section != -1)
			{
				if ($item == 'content' && isset($return[$section]))
				{
					$return = $return[$section];
				}
				else if ($item == 'title' && isset($section_titles[$section]))
				{
					$return = $section_titles[$section];
				}
			}
			
			return $return;
		}
		
	}
	
	/*
		@Función: wp_carousel_show_translation_info()
		@Versión: 1.0
		@Parámetros:
								$text: Texto original del pie de página
		@Descripción: Muestra información sobre la traducción
		@Añadida en la versión: 0.5		
	*/
		
	function wp_carousel_show_translation_info ($text)
	{
    	return $text.' | '.__('WP Carousel translated to English by <a href="http://sumolari.com">Sumolari</a>', 'wp_carousel');
    }
	
	// Añadimos la función al filtro
    add_filter('admin_footer_text', 'wp_carousel_show_translation_info'); 
	
	/*
		@Función: wp_carousel()
		@Versión: 3.1
		@Parámetros:
								$id: ID del carrusel a mostrar.
								$mode (show | get | array | carousel_ei | shortcode | internal_carousel_extra): Dependiento del valor, muestra el carrusel (show), lo devuelve (get), lo devuelve evitando modificar el bucle de WordPress (shortcode) o devuelve una matriz con su contenido (array). El modo internal_carousel_extra funciona de forma análoga al modo array, sólo que se salta el límite de veces que se muestra el carrusel, ya que el origen de este modo es el de permitir a los desarrolladores crear Extras que partan del contenido de otros carrusel. NUNCA se debe mostrar un carrusel usando el modo internal_carousel_extra, sería desvirtuar totalmente la finalidad del modo. Para esos fines se debe usar el modo array.
								$force_theme: Fuerza a cargarse el carrusel con un theme específico. El nombre de la carpeta contenedora de dicho theme es el valor de esta variable.
		@Descripción: Muestra el carrusel con ID $id.
		@Añadida en la versión: 0.1
		@Actualizada en la versión: 1.1		
	*/
	
	function wp_carousel($id, $mode='show', $force_theme = false)
	{
		
		/*
		if (!isset($_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN]) || !is_array($_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN]))
		{
			$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN] = array();
		}
				
		switch (true)
		{
			case ($mode == 'internal_carousel_extra'):
			case ($mode == 'array'):
			case ($mode == 'carousel_ei'):
				break;
			case ($mode == 'show'):
			case ($mode == 'get'):
			default:
			
				if (in_array($id, $_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN])):
					if ($mode != 'internal_carousel_extra')
					{
						printf(__('Carousel with ID %s has already been shown. Please, do not show more than once each carousel. If you want to show two carousels with exactly the same content, use Extra &laquo;Internal Carousel&raquo;.', 'wp_carousel'), $id);
						
						exit;
					}
				endif;
			
				$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_CAROUSELS_SHOWN][] = $id;
				
				break;
		}
		*/
	
		global $wp_carousel_path;
		
		/*
			Cargamos los extras
		*/
			
		if ($mode == 'carousel_ei')
		{
			wp_carousel_load_extras(false, '../../../');
		}
		else
		{
			wp_carousel_load_extras(false);
		}
			
		$items = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE));
		$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
		
		if (isset($items[$id]) && isset($config[$id])):
		
			$items = $items[$id];		
			$config = $config[$id];
			
			$config['USE_JCAROUSEL'] = true;
					
			if ($config['SHOW_ARROWS'] != '0' && $config['SLIDE_POSTS'] > 0)
			{
				$config['ARROWS'] = true;
			}
			else
			{
				$config['ARROWS'] = false;
			}
			
			if ($config['ENABLE_PAGINATION'] == '0')
			{
				$config['ENABLE_PAGINATION'] = false;
			}
			else
			{
				$config['ENABLE_PAGINATION'] = true;
			}
			
			if (isset($config['IMG_WIDTH']))
			{
				if ($config['IMG_WIDTH'] != '')
				{
					$config['HAS_IMG_WIDTH'] = true;
				}
				else
				{
					$config['HAS_IMG_WIDTH'] = false;
				}
			}
			else
			{
				$config['HAS_IMG_WIDTH'] = false;
			}
			
			if (isset($config['IMG_HEIGHT']))
			{
				if ($config['IMG_HEIGHT'] != '')
				{
					$config['HAS_IMG_HEIGHT'] = true;
				}
				else
				{
					$config['HAS_IMG_HEIGHT'] = false;
				}
			}
			else
			{
				$config['HAS_IMG_HEIGHT'] = false;
			}
			
			if (isset($config['PANEL_WIDTH']))
			{
				if ($config['PANEL_WIDTH'] != '')
				{
					$config['HAS_PANEL_WIDTH'] = true;
				}
				else
				{
					$config['HAS_PANEL_WIDTH'] = false;
				}
			}
			else
			{
				$config['HAS_PANEL_WIDTH'] = false;
			}
			
			if (isset($config['PANEL_HEIGHT']))
			{
				if ($config['PANEL_HEIGHT'] != '')
				{
					$config['HAS_PANEL_HEIGHT'] = true;
				}
				else
				{
					$config['HAS_PANEL_HEIGHT'] = false;
				}
			}
			else
			{
				$config['HAS_PANEL_HEIGHT'] = false;
			}
			
			if (isset($config['CAROUSEL_WIDTH']))
			{
				if ($config['CAROUSEL_WIDTH'] != '')
				{
					$config['HAS_CAROUSEL_WIDTH'] = true;
				}
				else
				{
					$config['HAS_CAROUSEL_WIDTH'] = false;
				}
			}
			else
			{
				$config['HAS_CAROUSEL_WIDTH'] = false;
			}
			
			if (isset($config['CAROUSEL_HEIGHT']))
			{
				if ($config['CAROUSEL_HEIGHT'] != '')
				{
					$config['HAS_CAROUSEL_HEIGHT'] = true;
				}
				else
				{
					$config['HAS_CAROUSEL_HEIGHT'] = false;
				}
			}
			else
			{
				$config['HAS_CAROUSEL_HEIGHT'] = false;
			}
			
			if (count($items) > 0)
			{
				
				$items = wp_carousel_adapt_items($items);
				
				$delete_posts = array();		
				foreach ($items as $key => $value)
				{	
					if ($value['TYPE'] == 2 && !$value['SHOW']) $delete_posts[] = $value['ID'];	
				}
				
				if ($mode != 'shortcode') // Sólo si no estamos usando el shortcode
				{
					/* Alteramos la consulta a la DB y eliminamos los artículos que queremos ocultar */
					global $wp_query;
					if (is_home())
					{						
						$wp_query->query_vars['post__not_in'] = $delete_posts;
						$wp_query->query($wp_query->query_vars);
					}
					/* Ya están ocultos */
				}
				
				if ($mode != 'shortcode' && wp_carousel_is_in_loop())
				{
					printf('<p>%s</p>', __('<strong>Warning:</strong> You\'re showing WP Carousel <strong>IN</strong> the loop. If you want to show WP Carousel in a post it is better to use the shortcode <code>[wp_carousel]</code> rather than showing WP Carousel in the loop.', 'wp_carousel'));
					printf('<p>%s %d-%d.</p>', __('<strong>Note:</strong> To hide this message, delete lines:', 'wp_carousel'), __LINE__ - 1,__LINE__);
				}
				
				switch ($mode)
				{
					case 'array':
					case 'carousel_ei':
					case 'internal_carousel_extra':
						$return = array(
							'ITEMS' => $items,
							'CONFIG' => $config,
							'ID' => $id
						);
						$c_id = $id;
						eval('if (!function_exists("wp_carousel_load_carousel_'.$c_id.'_js_code")) { function wp_carousel_load_carousel_'.$c_id.'_js_code() { wp_carousel_load_carousel_js('.$c_id.'); } }');
						add_action('wp_footer', 'wp_carousel_load_carousel_'.$c_id.'_js_code');
						return $return;
						break;
					case 'show':
						$c_id = $id;
						unset($id);
						
						if ($force_theme)
						{
							$current_theme = wp_carousel_get_theme_information($force_theme);
						}
						else
						{
							$current_theme = wp_carousel_get_theme_information($config['THEME']);
						}
						
						if ($current_theme['supports']['jcarousel'] && $config['USE_JCAROUSEL'])
						{
							if (!@include('themes/'.$config['THEME'].'/theme-jcarousel.php'))
							{
								echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config['THEME']).' -->';
							}
						}
						else
						{
							if (!@include('themes/'.$config['THEME'].'/theme.php'))
							{
								echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config['THEME']).' -->';
							}
						}
						
						eval('if (!function_exists("wp_carousel_load_carousel_'.$c_id.'_js_code")) { function wp_carousel_load_carousel_'.$c_id.'_js_code() { wp_carousel_load_carousel_js('.$c_id.'); } }');
						add_action('wp_footer', 'wp_carousel_load_carousel_'.$c_id.'_js_code');
						break;
					case 'shortcode':
					case 'get':
						ob_start();
						$c_id = $id;
						unset($id);
						
						if ($force_theme)
						{
							$current_theme = wp_carousel_get_theme_information($force_theme);
						}
						else
						{
							$current_theme = wp_carousel_get_theme_information($config['THEME']);
						}
						
						if ($current_theme['supports']['jcarousel'] && $config['USE_JCAROUSEL'])
						{
							if (!@include('themes/'.$config['THEME'].'/theme-jcarousel.php'))
							{
								echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config['THEME']).' -->';
							}
						}
						else
						{
							if (!@include('themes/'.$config['THEME'].'/theme.php'))
							{
								echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config['THEME']).' -->';
							}
						}
						$out = ob_get_contents();
						ob_end_clean();
						eval('if (!function_exists("wp_carousel_load_carousel_'.$c_id.'_js_code")) { function wp_carousel_load_carousel_'.$c_id.'_js_code() { wp_carousel_load_carousel_js('.$c_id.'); } }');
						add_action('wp_footer', 'wp_carousel_load_carousel_'.$c_id.'_js_code');
						return $out;
						break;
					default:
						break;
				}
		
			}
			else
			{
			}
			
		else:
		
			echo '<!-- WP Carousel Error: '.__('The carousel you\'ve tried to show does not exist', 'wp_carousel').' -->';
		
		endif;	
			
	}
	
	/*
		@Función: wp_carousel_load_theme_css()
		@Versión: 1.1
		@Descripción: Carga el CSS de todos los carruseles.
		@Añadida en la versión: 0.4
		@Actualizada en la versión: 1.0	
	*/
	
	function wp_carousel_load_theme_css()
	{	
		global $wp_carousel_path;
		$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
		
		$loaded_css = array();
		
		if (is_array($config))
		{
			foreach ($config as $config_key => $config_value)
			{
				if (!@include('themes/'.$config_value['THEME'].'/index.php'))
				{
					echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config_value['THEME']).' -->';
				}
								
				if (isset($theme['css']))
				{
					if (is_array($theme['css']))
					{		
						foreach ($theme['css'] as $key => $value)
						{
							if (!in_array($wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value, $loaded_css))
							{
								echo '<link rel="stylesheet" href="'.$wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value.'" type="text/css" media="all" />';
							}
							$loaded_css[] = $wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value;
						}		
					}
				}
				
				unset($theme);
					
			}
		}
	}
	
	/*
		@Función: wp_carousel_load_theme_js()
		@Versión: 1.0
		@Descripción: Carga el código Javascript de todos los carruseles.
		@Añadida en la versión: 1.0
	*/
	
	function wp_carousel_load_theme_js()
	{	
		global $wp_carousel_path;
		$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
		
		$loaded_js = array();
		
		if (is_array($config))
		{
			foreach ($config as $config_key => $config_value)
			{
				if (!@include('themes/'.$config_value['THEME'].'/index.php'))
				{
					echo '<!-- WP Carousel Error: '.sprintf(__('Theme %s does not exists and can\'t be loaded. Please, use a different theme', 'wp_carousel'), $config_value['THEME']).' -->';
				}
								
				if (isset($theme['js']))
				{
					if (is_array($theme['js']))
					{		
						foreach ($theme['js'] as $key => $value)
						{
							if (!in_array($wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value, $loaded_js))
							{
								echo '<script type="text/javascript" src="'.$wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value.'"></script>';
							}
							$loaded_js[] = $wp_carousel_path[6].'themes/'.$config_value['THEME'].'/'.$value;
						}		
					}
				}
				
				unset($theme);
					
			}
		}
	}
	
	/*
		@Función: wp_carousel_load_carousel_js()
		@Versión: 4.0
		@Parámetros:
								$id: ID del carrusel del cual cargaremos su código JS.
		@Descripción: Se carga el código JS del carrusel con ID: $id
		@Añadida en la versión: 0.4	
		@Actualizada en la versión: 1.1
	*/
	
	function wp_carousel_load_carousel_js($id)
	{
		$config = unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
		if (isset($config[$id]))
		{
			$value = $config[$id];
			
			$current_theme = wp_carousel_get_theme_information($value['THEME']);
			
			$use_stepcarousel = false;
			
			if (!isset($value['LOOP_MODE']))
			{
				$value['LOOP_MODE'] = 0;
			}
			
			 if (!isset($value['SHOW_ARROWS']))
			{
				$value['SHOW_ARROWS'] = 0;
			}
			
			if (!isset($value['SLIDE_POSTS']))
			{
				$value['SLIDE_POSTS'] = 0;
			}
			
			if (!isset($value['AUTOSLIDE_TIME']))
			{
				$value['AUTOSLIDE_TIME'] = 0;
			}
			
			if (!isset($value['AUTOSLIDE_POSTS']))
			{
				$value['AUTOSLIDE_POSTS'] = 0;
			}
			else
			{
				$value['AUTOSLIDE_POSTS'] = (int) $value['AUTOSLIDE_POSTS'];
			}
			
			if (!isset($value['VERTICAL_MODE']))
			{
				$value['VERTICAL_MODE'] = 0;
			}
			
			if ($current_theme['supports']['nivo'])
			{
				?>
				<script type="text/javascript">
					jQuery(window).load(function() {
						jQuery('#slider_<?php echo $id; ?>').nivoSlider({
							effect: 'random', // Specify sets like: 'fold,fade,sliceDown'
							slices: 15, // For slice animations
							boxCols: 8, // For box animations
							boxRows: 4, // For box animations
							animSpeed: 500, // Slide transition speed
							pauseTime: <?php echo $value['AUTOSLIDE_TIME']; ?>, // How long each slide will show
							startSlide: 0, // Set starting Slide (0 index)
							directionNav: <?php if ($value['SHOW_ARROWS'] == 1) { echo 'true'; } else { echo 'false'; } ?>, // Next & Prev navigation
							directionNavHide: true, // Only show on hover
							controlNav: <?php if ($value['ENABLE_PAGINATION']) { echo 'true'; } else { echo 'false'; } ?>, // 1,2,3... navigation
							controlNavThumbs: false, // Use thumbnails for Control Nav
							controlNavThumbsFromRel: false, // Use image rel for thumbs
							controlNavThumbsSearch: '.jpg', // Replace this with...
							controlNavThumbsReplace: '_thumb.jpg', // ...this in thumb Image src
							keyboardNav: true, // Use left & right arrows
							pauseOnHover: true, // Stop animation while hovering
							manualAdvance: false, // Force manual transitions
							captionOpacity: 0.8, // Universal caption opacity
							prevText: '<?php _e('Prev', 'wp_carousel'); ?>', // Prev directionNav text
							nextText: '<?php _e('Next', 'wp_carousel'); ?>', // Next directionNav text
							beforeChange: function(){}, // Triggers before a slide transition
							afterChange: function(){}, // Triggers after a slide transition
							slideshowEnd: function(){}, // Triggers after all slides have been shown
							lastSlide: function(){}, // Triggers when last slide is shown
							afterLoad: function(){} // Triggers when slider has loaded
						});
					});
				</script>
				<?php
			}
			else
			{
				?>
			<script type="text/javascript">
			
			jQuery(document).ready(function() {
				jQuery('#carousel_<?php echo $id; ?>').jcarousel({
					scroll: <?php echo $value['SLIDE_POSTS']; ?>,
					wrap: <?php if ($value['LOOP_MODE'] != 0) { ?>'both'<?php } else { ?>null<?php } ?>,
					auto: <?php if ($value['AUTOSLIDE_TIME'] != 0 && $value['AUTOSLIDE_POSTS'] > 0) { echo (int) ($value['AUTOSLIDE_TIME'] / 1000); } else { ?>0<?php } ?>,
					vertical: <?php if ($value['VERTICAL_MODE'] != 0) { ?>true<?php } else { ?>false<?php } ?>
				});
				
				<?php if ($value['SHOW_ARROWS'] == 1): ?>
				jQuery('.carousel_<?php echo $id; ?>_next').click(function() {
					jQuery('#carousel_<?php echo $id; ?>').jcarousel('prev');
					jQuery('#carousel_<?php echo $id; ?>').data('jcarousel').startAuto(0);
					return false;
				});
				
				jQuery('#carousel_<?php echo $id; ?>-paginate a').bind('click', function() {
					jQuery('#carousel_<?php echo $id; ?>').data('jcarousel').scroll(jQuery.jcarousel.intval(jQuery(this).text()));
					jQuery('#carousel_<?php echo $id; ?>').data('jcarousel').startAuto(0);
					return false;
				});
			
				jQuery('.carousel_<?php echo $id; ?>_prev').click(function() {
					jQuery('#carousel_<?php echo $id; ?>').jcarousel('next');
					jQuery('#carousel_<?php echo $id; ?>').data('jcarousel').startAuto(0);
					return false;
				});
				<?php endif; ?>
				
			});

			</script>
				<?php
			}
		}
	}
	
	/*
		@Función: wp_carousel_adapt_items()
		@Versión: 3.0
		@Parámetros:
								$items: Matriz de elementos del carrusel
		@Descripción: Prepara la matriz para que se contenga toda la información necesaria, obtenida de diferentes funciones
		@Añadida en la versión: 0.4
		@Última actualización en la versión: 1.0
	*/
	
	function wp_carousel_adapt_items($items)
	{
			
		foreach ($items as $key => $value)
		{
			$items_adapted[$key] = $value;
			
			switch (true):
			
				case ($items_adapted[$key]['TYPE'] != 1 && $items_adapted[$key]['TYPE'] != 4 && $items_adapted[$key]['TYPE'] != 5 && $items_adapted[$key]['TYPE'] != 6 && $items_adapted[$key]['TYPE'] != 7):
					
					if (!is_numeric($items_adapted[$key]['TYPE']))
					{					
						// Es un extra, así que determinamos si es del tipo "group" o "single"
						
						if (!isset($_SESSION['WP_CAROUSEL_EXTRAS'][$items_adapted[$key]['TYPE']]['type']))
						{
							$_SESSION['WP_CAROUSEL_EXTRAS'][$items_adapted[$key]['TYPE']]['type'] = 'single';
						}
						
						if ($_SESSION['WP_CAROUSEL_EXTRAS'][$items_adapted[$key]['TYPE']]['type'] == 'group')
						{
							eval('$temp_group_items = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$items_adapted[$key]['TYPE']]['item_function'].'("'.base64_encode(serialize($value)).'");');
							
							$temp_key = explode('_', $key);
							
							foreach ($temp_group_items as $tt_key => $tt_value)
							{
								$items_adapted[$temp_key[0].'_'.$temp_key[1].'_'.$tt_key] = $tt_value;
								
								if (!isset($items_adapted[$temp_key[0].'_'.$temp_key[1].'_'.$tt_key]['VIDEO']))
								{
									$items_adapted[$temp_key[0].'_'.$temp_key[1].'_'.$tt_key]['VIDEO'] = '';
								}
															
							}
							
							$items_adapted[$key]['MUST_BE_DELETED'] = true;
													
						}
						else
						{
	
							if (!isset($items_adapted[$key]['TITLE']))
							{
								$items_adapted[$key]['TITLE'] = wp_carousel_item_value($value, $items_adapted[$key]['TYPE'], 'name');
							}
							if (!isset($items_adapted[$key]['DESC']))
							{
								$items_adapted[$key]['DESC'] = wp_carousel_item_value($value, $items_adapted[$key]['TYPE'], 'desc');
							}
							if (!isset($items_adapted[$key]['IMAGE_URL']))
							{
								$items_adapted[$key]['IMAGE_URL'] = wp_carousel_item_value($value, $items_adapted[$key]['TYPE'], 'image_url');
							}
							if (!isset($items_adapted[$key]['VIDEO']))
							{
								$items_adapted[$key]['VIDEO'] = wp_carousel_item_value($value, $items_adapted[$key]['TYPE'], 'video_url');
							}
							if (!isset($items_adapted[$key]['LINK_URL']))
							{
								$items_adapted[$key]['LINK_URL'] = wp_carousel_item_value($value, $items_adapted[$key]['TYPE'], 'link_url');
							}
							
						}
	
					}
					
					if (!isset($items_adapted[$key]['TITLE']))
					{
						$items_adapted[$key]['TITLE'] = wp_carousel_item_value($items_adapted[$key]['ID'], $items_adapted[$key]['TYPE'], 'name');
					}
					if (!isset($items_adapted[$key]['DESC']))
					{
						$items_adapted[$key]['DESC'] = wp_carousel_item_value($items_adapted[$key]['ID'], $items_adapted[$key]['TYPE'], 'desc');
					}
					if (!isset($items_adapted[$key]['IMAGE_URL']))
					{
						$items_adapted[$key]['IMAGE_URL'] = wp_carousel_item_value($items_adapted[$key]['ID'], $items_adapted[$key]['TYPE'], 'image_url');
					}
					if (!isset($items_adapted[$key]['VIDEO']))
					{
						if ($items_adapted[$key]['TYPE'] < 4)
						{
							$items_adapted[$key]['VIDEO'] = wp_carousel_get_video_embed_code($items_adapted[$key]['ID'], 'get');
						}
						elseif (!is_numeric($items_adapted[$key]['TYPE']))
						{
							$items_adapted[$key]['VIDEO'] = '';
						}
					}
					if (!isset($items_adapted[$key]['LINK_URL']))
					{
						$items_adapted[$key]['LINK_URL'] = wp_carousel_item_value($items_adapted[$key]['ID'], $items_adapted[$key]['TYPE'], 'link_url');
					}
					
					if (isset($items_adapted[$key]['MUST_BE_DELETED']))
					{
						if ($items_adapted[$key]['MUST_BE_DELETED'])
						{
							unset($items_adapted[$key]);
						}
					}
							
					break;
				
				case ($items_adapted[$key]['TYPE'] == 4):

					$items_adapted[$key] = $value;
					$items_adapted[$key]['VIDEO'] = $value['VIDEO_URL'];
				
					break;
				
				case ($items_adapted[$key]['TYPE'] == 7):
				
					if (WP_CAROUSEL_EI)
					{
						unset ($items_adapted[$key]);
						$temp_item_content = maybe_unserialize(base64_decode(file_get_contents($value['WP_CAROUSEL_EI_URL'].'?carousel_id='.$value['WP_CAROUSEL_EI_ID'])));
						foreach ($temp_item_content as $temp_key => $temp_value)
						{
							$items_adapted[$value['ORDER'].'_'.$temp_key] = $temp_value;
						}
					}
					else
					{
						unset ($items_adapted[$key]);
					}
					
					break;
				
				case ($items_adapted[$key]['TYPE'] == 5):
				case ($items_adapted[$key]['TYPE'] == 1):
				case ($items_adapted[$key]['TYPE'] == 6):
				
					if (isset($items_adapted[$key]['POSTS_NUMBER']))
					{
						if (is_numeric($items_adapted[$key]['POSTS_NUMBER']) && ($items_adapted[$key]['POSTS_NUMBER'] != '0'))
						{
						}
						else 
						{
							$items_adapted[$key]['POSTS_NUMBER'] = '10';
						}
					}
					else
					{
						$items_adapted[$key]['POSTS_NUMBER'] = '10';
					}
					
					if (isset($items_adapted[$key]['POSTS_ORDER']))
					{
						if ($items_adapted[$key]['POSTS_ORDER'] == 'first_old')
						{
							$temp_query_orderby = 'date';
							$temp_query_order = 'asc';
						}
						elseif ($items_adapted[$key]['POSTS_ORDER'] == 'random')
						{
							$temp_query_orderby = 'rand';
							$temp_query_order = '';
						}
						elseif ($items_adapted[$key]['POSTS_ORDER'] == 'first_new')
						{
							$temp_query_orderby = 'date';
							$temp_query_order = 'desc';
						}
					}
					else
					{
						$items_adapted[$key]['POSTS_ORDER'] = 'first_new';
						$temp_query_orderby = 'date';
						$temp_query_order = 'desc';
					}
					
					switch (true)
					{
						case ($items_adapted[$key]['TYPE'] == 5):
							$temp_tag_info = get_term_by('id', $items_adapted[$key]['ID'], 'post_tag');
							$temp_query = new WP_Query('tag='.$temp_tag_info->slug.'&showposts='.$items_adapted[$key]['POSTS_NUMBER'].'&orderby='.$temp_query_orderby.'&order='.$temp_query_order);
							break;
						case ($items_adapted[$key]['TYPE'] == 1):
							$temp_query = new WP_Query('cat='.$items_adapted[$key]['ID'].'&showposts='.$items_adapted[$key]['POSTS_NUMBER'].'&orderby='.$temp_query_orderby.'&order='.$temp_query_order);
							break;
						case ($items_adapted[$key]['TYPE'] == 6):
							$temp_query = new WP_Query('author='.$items_adapted[$key]['ID'].'&showposts='.$items_adapted[$key]['POSTS_NUMBER'].'&orderby='.$temp_query_orderby.'&order='.$temp_query_order);
							break;
					}
										
					if ($temp_query_orderby == 'rand')
					{
						$temp_counter = 0;
					}
					
					while ($temp_query->have_posts())
					{
						
						if ($temp_query_orderby == 'rand')
						{
							$temp_first_position_key_value = $temp_counter;
						}
						else
						{
							$temp_first_position_key_value = $items_adapted[$key]['ORDER'];
						}
						
						$temp_query->the_post();
						$items_temp_adapted[$temp_first_position_key_value.'_'.get_the_ID().'_2'] = array(
							'ID' => get_the_ID(),
							'TYPE' => 2,
							'ORDER' => $items_adapted[$key]['ORDER'],
							'SHOW' => $items_adapted[$key]['SHOW'],
							'TITLE' => wp_carousel_item_value(get_the_ID(), 2, 'name'),
							'DESC' => wp_carousel_item_value(get_the_ID(), 2, 'desc'),
							'IMAGE_URL' => wp_carousel_item_value(get_the_ID(), 2, 'image_url'),
							'LINK_URL' => wp_carousel_item_value(get_the_ID(), 2, 'link_url'),
							'VIDEO' => wp_carousel_get_video_embed_code(get_the_ID(), 'get')
						);
						
						if ($temp_query_orderby == 'rand')
						{
							$temp_counter++;
						}
					}
					
					wp_reset_query();
					wp_reset_postdata();
					
					if ($items_adapted[$key]['POSTS_ORDER'] == 'first_new') // Primero van los nuevos artículos, así que... ¡A ordenar se ha dicho!
					{
						$temp_max_date = 0;
						$unix_date_for_post = array();
						foreach ($items_temp_adapted as $temp_key => $temp_value)
						{
							$temp_array_key = explode('_', $temp_key); // Obtenemos la ID del artículo o la página en cuestión
							$this_post = get_post($temp_array_key[1]); // Obtenemos los datos del elemento con la ID antes calculada
							$unix_date_for_post[$temp_array_key[1]]  = strtotime($this_post->post_date); // Obtenemos la fecha en formato Unix Timestamp
							
							if ($temp_max_date < $unix_date_for_post[$temp_array_key[1]]) // Comparamos la fecha máxima y la de este elemento: si es mayor la del elemento, se toma como fecha máxima
							{
								$temp_max_date = $unix_date_for_post[$temp_array_key[1]];
							}
						}
						if ($temp_max_date > 0) // Verificamos que no haya errores
						{
							foreach ($items_temp_adapted as $old_temp_key => $temp_value)
							{
								$old_temp_array_key = explode('_', $old_temp_key);
								$new_temp_key = $temp_max_date - $unix_date_for_post[$old_temp_array_key[1]];
								$items_adapted[$new_temp_key.'_'.$old_temp_array_key[1].'_'.$old_temp_array_key[2]] = $temp_value;
							}
						}
						else // Error, omitimos el proceso de ordenado
						{
							foreach ($items_temp_adapted as $temp_key => $temp_value)
							{
								$items_adapted[$temp_key] = $temp_value;
							}
						}
						unset($unix_date_for_post);
					}
					elseif ($items_adapted[$key]['POSTS_ORDER'] == 'random') // Orden aleatorio
					{
						foreach ($items_temp_adapted as $old_temp_key => $temp_value)
						{
							$items_adapted[$old_temp_key] = $temp_value;
						}
					}
					elseif ($items_adapted[$key]['POSTS_ORDER'] == 'first_old') // Ordenamos en orden cronológico inverso
					{
						foreach ($items_temp_adapted as $old_temp_key => $temp_value)
						{
							$old_temp_array_key = explode('_', $old_temp_key);
							$this_post = get_post($old_temp_array_key[1]); // Obtenemos los datos del elemento con la ID antes calculada
							$new_temp_key = strtotime($this_post->post_date);
							$items_adapted[$new_temp_key.'_'.$old_temp_array_key[1].'_'.$old_temp_array_key[2]] = $temp_value;
						}
					}
					
					unset($items_temp_adapted);
					unset($items_adapted[$key]);
					
					break;
					
				default:
					// Error!
					break;
				
			endswitch;
				
		}
		uksort($items_adapted, 'wp_carousel_compare_items_keys');		// Nuevo método de ordenado
		
		return $items_adapted;
	}
	
	/*
		@Función: wp_carousel_compare_items_keys()
		@Versión: 1.0
		@Parámetros:
								$a: Primer índice
								$b: Segundo índice
		@Descripción: Compara dos índices de matrices de elementos. Si el primer índice es menor que el segundo, devuelve -1, si es mayor, 1 y si es igual, 0
		@Nota: Sólo la usa la función wp_carousel_adapt_items(), y no debe usarse en ningún otro caso 
		@Añadida en la versión: 0.4.0.11	
	*/
	
	function wp_carousel_compare_items_keys($a, $b)
	{
		$a_exploded = explode('_', $a);
		$b_exploded = explode('_', $b);
		
		if ($a_exploded[0] < $b_exploded[0]) // Comparamos órdenes
		{
			// El orden es menor, así que primero va B
			return -1;
		}
		elseif ($a_exploded[0] > $b_exploded[0])
		{
			// El orden es mayor, así que primera va A
			return 1;
		}
		else {
			// El orden es el mismo, así que analizamos la ID
			if ($a_exploded[1] < $a_exploded[1])
			{
				// La ID es menor, así que primero va B
				return -1;
			}
			elseif ($a_exploded[1] > $b_exploded[1])
			{
				// La es mayor, así que primero va A
				return 1;
			}
			else
			{
				// Las IDs son iguales, así que analizamos el tipo de contenido
				if ($a_exploded[2] < $b_exploded[2])
				{
					// El tipo de contenido es menor, así que primero va B
					return -1;
				}
				elseif ($a_exploded[2] > $b_exploded[2])
				{
					// El tipo de contenido es mayor, así que primero va A
					return 1;
				}
				else {
					// POSIBLE ERROR: TODO ES IGUAL, así que devolvemos 0
					return 0;
				}
			}
		}
		
	}
	
	/*
		@Función: wp_carousel_adminmenu_links()
		@Versión: 2.1
		@Descripción: Añadimos las páginas de opciones de WP Carousel al menú de WordPress.
		@Añadida en la versión: 0.4		
		@Actualizada en la versión: 0.5.3
	*/
	
	function wp_carousel_adminmenu_links()
	{
		global $wp_carousel_path;
		
		// Por un error con Poedit muevo unas frases aquí, así se soluciona el problema y el editor detecta las frases sin cerrarse de golpe
		$temp_import_name = __(
		'Import', 
		'wp_carousel');
		$temp_uninstall_name = __(
		'Uninstall', 
		'wp_carousel');
		$temp_add_name = __(
		'Add', 
		'wp_carousel');
		// Ya está, lo que viene a continuación vuelve a jugar un papel importante en el plugin 
		
		$items = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE));
				
		$wp_carousel_temp_hook = add_object_page('WP Carousel', 'WP Carousel', 'administrator', 'wp-carousel', 'wp_carousel_options_page', $wp_carousel_path[6].'img/wp_carousel.png');
		
		include($wp_carousel_path[8].'../../../wp-includes/version.php');
		
		if ( ((float)$wp_version) < 3.3)
		{
			add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
		}
		
		// Cargamos la configuración general
		$carousel_config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
						
		if (is_array($items))
		{
			foreach ($items as $key => $value)
			{
				// Cargamos las opciones de este carrusel en concreto
				$this_carousel_config = $carousel_config[$key];
				
				// Obtenemos su nombre o le asignamos uno por defecto si no tiene
				if (isset($this_carousel_config['CAROUSEL_NAME']))
				{
					$this_carousel_name = $this_carousel_config['CAROUSEL_NAME'];
				}
				else
				{
					$this_carousel_name = __('Carousel ', 'wp_carousel').$key;
				}
				
				$wp_carousel_temp_hook = add_submenu_page('wp-carousel', $this_carousel_name, $this_carousel_name, 'administrator', 'edit-carousel-'.$key, 'wp_carousel_carousel_options_page');
				
				if ( ((float)$wp_version) < 3.3)
				{
					add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
				}
				
			}
		}
		
		$wp_carousel_temp_hook = add_submenu_page('wp-carousel', __('Backup Manager', 'wp_carousel'), __('Backup Manager', 'wp_carousel'), 'administrator', 'wp-carousel-backup', 'wp_carousel_backup_page');
		
		if ( ((float)$wp_version) < 3.3)
		{
			add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
		}
				
		$wp_carousel_temp_hook = add_submenu_page('wp-carousel', __(
		'Safe mode Import',
		'wp_carousel'), __(
		'Safe mode Import',
		'wp_carousel'), 'administrator', 'wp-carousel-import', 'wp_carousel_import_page');
		
		if ( ((float)$wp_version) < 3.3)
		{
			add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
		}
		
		$wp_carousel_temp_hook = add_submenu_page('wp-carousel', $temp_uninstall_name, $temp_uninstall_name, 'administrator', 'wp-carousel-uninstall', 'wp_carousel_uninstall_page');
		
		if ( ((float)$wp_version) < 3.3)
		{
			add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
		}
		
		$wp_carousel_temp_hook = add_submenu_page('wp-carousel', $temp_add_name, $temp_add_name, 'administrator', 'wp-carousel-add-theme', 'wp_carousel_add_carousel_page');
		
		if ( ((float)$wp_version) < 3.3)
		{
			add_action('load-'.$wp_carousel_temp_hook, 'wp_carousel_custom_help_tab'); // Modificamos la pestaña de ayuda
		}
		
	}
	
	/*
		@Función: wp_carousel_options_page()
		@Versión: 2.0
		@Parámetros:
								$var: Almacena datos enviados por WordPress, así se evita un problema con la variable $debug
								$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Crea la página principal de WP Carousel.
		@Añadida en la versión: 0.4	
		@Actualizada en la versión: 0.5
	*/
	
	function wp_carousel_options_page($var='', $debug=false)
	{
		// Cargamos la ID del usuario: las configuraciones de esta página se almacenan como metadatos de los usuarios, no en la tabla de WP Carousel
		global $user_ID, $wp_carousel_path;
		
		/*
			Cargamos los extras
		*/
			
		wp_carousel_load_extras($debug);
	
		$items = get_option(WP_CAROUSEL_ITEMS_TABLE);
		
		$will['SHOW_INFO_TABLE'] = true;
		$will['SHOW_UPDATE_MESSAGE'] = false;
		
		$items = maybe_unserialize($items);
		$count = count($items);
		
		if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage'))
		{
			?>
			<div class="updated fade"><p><?php _e('It seems that you use <strong>qTranslate</strong> in this site. <strong>WP Carousel</strong> does not support <strong>qTranslate</strong>\'s custom functions, so there might be some issues with <strong>WP Carousel</strong> translation and other bugs related with UI appearance.', 'wp_carousel'); ?></p></div>
			<?php
		}
		
		if ($will['SHOW_UPDATE_MESSAGE'])
		{
			?>
			<div class="updated fade"><p><?php _e('The new config has been saved, please, reload this page to see changes.', 'wp_carousel'); ?></p></div>
			<?php
		}
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>WP Carousel</h2>
						
			<?php
				if (isset($_GET['action']))
				{
					$action = explode(':', $_GET['action']);

					switch ($action[0])
					{
						case 'DELETE_CAROUSEL':
							if (!isset($_GET['sure']))
							{
								// Mostramos el aviso
								printf(__('<p>Do you really want to delete the carousel with ID "%s"? That can\'t be undone.</p>', 'wp_carousel'), $action[1]);
								printf(__('<p>Click <a href="%s">here</a> to delete the carousel or click <a href="%s">here</a> to return to the carousel\'s options page</p>', 'wp_carousel'), wp_carousel_create_internal_urls('SELF_URL').'&sure=yes', wp_carousel_create_internal_urls('SELF_URL:DELETE_ALL_URL_VARIABLES').'?page=edit-carousel-'.$action[1]);
								$will['SHOW_INFO_TABLE'] = false;
							}
							break;
						default:
							break;
					}
				}
				
				if ($will['SHOW_INFO_TABLE'])
				{
			?>
			<table class="widefat post fixed" cellspacing="0">
			
				<thead>
					<tr>
						<th scope="col" id="name" class="manage-column column-name"><?php _e('Name', 'wp_carousel'); ?></th>
						<th scope="col" id="value" class="manage-column column-value"><?php _e('Value', 'wp_carousel'); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name"><?php _e('Name', 'wp_carousel'); ?></th>
						<th scope="col" class="manage-column column-value"><?php _e('Value', 'wp_carousel'); ?></th>
					</tr>
				</tfoot>

				<tbody>
				
					<tr id="item-1" valign="top">
						<td class="item-name column-name"><strong><?php _e('Number of carousels', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo $count; ?></td>
					</tr>
					<tr id="item-2" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Language', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo get_locale(); ?></td>
					</tr>
					<tr id="item-3" valign="top">
						<td class="item-name column-name"><strong><?php _e('External Integration', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_EI == true) { _e('Enabled', 'wp_carousel'); echo ' ('.$wp_carousel_path[6].'wp-carousel-ei.php)'; } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-4" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Show videos instead of images', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_VIDEOS_FIRST == true) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-5" valign="top">
						<td class="item-name column-name"><strong><?php _e('Autosave backups', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_AUTOSAVE_BACKUPS == true) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-6" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('WordPress option\'s name where carousels items are stored', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_ITEMS_TABLE; ?></td>
					</tr>
					<tr id="item-7" valign="top">
						<td class="item-name column-name"><strong><?php _e('WordPress option\'s name where carousels config is stored', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_CONFIG_TABLE; ?></td>
					</tr>
					<tr id="item-8" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('WordPress option\'s name where WP Carousel backups are stored', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_BACKUP_TABLE; ?></td>
					</tr>
					<tr id="item-9" valign="top">
						<td class="item-name column-name"><strong><?php _e('Categories box in carousel\'s options page', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_CATEGORIES_IN_CAROUSEL_OPTIONS == 1) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-10" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Tags box in carousel\'s options page', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_TAGS_IN_CAROUSEL_OPTIONS == 1) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-11"valign="top">
						<td class="item-name column-name"><strong><?php _e('Posts box in carousel\'s options page', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_POSTS_IN_CAROUSEL_OPTIONS == 1) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-12" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Pages box in carousel\'s options page', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_PAGES_IN_CAROUSEL_OPTIONS == 1) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-13" valign="top">
						<td class="item-name column-name"><strong><?php _e('Authors box in carousel\'s options page', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php if(WP_CAROUSEL_SHOW_AUTHORS_IN_CAROUSEL_OPTIONS == 1) { _e('Enabled', 'wp_carousel'); } else _e('Disabled', 'wp_carousel'); ?></td>
					</tr>
					<tr id="item-14" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php printf(__('Path to wp-carousel.php (Is it wrong? Report it <a href="%s">at the forums</a>)', 'wp_carousel'), 'http://foro.sumolari.com/index.php?board=34.0'); ?></strong></td>
						<td class="item-value column-value"><?php echo $wp_carousel_path[6].'wp-carousel.php'; ?></td>
					</tr>
					<tr id="item-15" valign="top">
						<td class="item-name column-name"><strong><?php _e('Backups stored in Database', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo count(maybe_unserialize(get_option(WP_CAROUSEL_BACKUP_TABLE)), COUNT_RECURSIVE);  ?></td>
					</tr>
					<tr id="item-16" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Custom field for description', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_CAROUSEL_TEXT;  ?></td>
					</tr>
					<tr id="item-17" valign="top">
						<td class="item-name column-name"><strong><?php _e('Custom field for image URL', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_IMAGE_URL;  ?></td>
					</tr>
					<tr id="item-18" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Custom field for link URL', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_LINK_URL;  ?></td>
					</tr>
					<tr id="item-19" valign="top">
						<td class="item-name column-name"><strong><?php _e('Custom field for video URL', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_VIDEO_URL;  ?></td>
					</tr>
					<tr id="item-20" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Version', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value"><?php echo WP_CAROUSEL_VERSION; ?></td>
					</tr>
					<tr id="item-21" valign="top">
						<td class="item-name column-name"><strong><?php _e('Extras', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value">
						<?php if ($_SESSION['WP_CAROUSEL_EXTRAS']) { ?>
							<ul>
								<?php
								foreach ($_SESSION['WP_CAROUSEL_EXTRAS'] as $key => $value)
								{
									echo '<li><a href="'.$value['url'].'">'.$value['name'].'</a> '.$value['version'].' '.__('by', 'wp_carousel').' <a href="'.$value['author_url'].'">'.$value['author'].'</a> ('.$value['desc'].')</li>';
								}
								?>
							</ul>
						<?php } else { ?>
							<p><?php _e('There are not extras in the extras folder', 'wp_carousel'); ?>
						<?php } ?>
						</td>
					</tr>
					<tr id="item-22" class="alternate" valign="top">
						<td class="item-name column-name"><strong><?php _e('Themes', 'wp_carousel'); ?></strong></td>
						<td class="item-value column-value">
						<?php
							$themes = wp_carousel_get_theme_information();
							if (is_array($themes)) {
						?>
							<ul>
								<?php
								foreach ($themes as $key => $value)
								{
									echo '<li><a href="'.$value['url'].'">'.$value['name'].'</a> '.$value['version'].' '.__('by', 'wp_carousel').' <a href="'.$value['author_url'].'">'.__($value['author'], 'wp_carousel').'</a> ('.__($value['desc'], 'wp_carousel').')</li>';
								}
								?>
							</ul>
						<?php } else { ?>
							<p><?php _e('There are not themes in the themes folder', 'wp_carousel'); ?>
						<?php } ?>
						</td>
					</tr>
					

				</tbody>
				
			</table>
			
			<?php
				}
			?>
						
		</div>
		
		<div class="clear"></div>

		<?php
		
	}
	
	/*
		@Función: wp_carousel_carousel_options_page()
		@Versión: 3.0
		@Parámetros:
								$var: Almacena datos enviados por WordPress, así se evita un problema con la variable $debug
								$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Crea la página de opciones de cada carrusel, donde también se añadirán contenidos y se eliminarán. Toma el valor de $_GET['action'] para detectar qué acción debe realizar.
		@Añadida en la versión: 0.4		
		@Última actualización en la versión: 1.0
	*/
	
	/*
	
		Valores de $_GET['action']
			
			__ACTION__:__PARAMETRO__ -> Sintaxis: ACCION:PARAMETRO. En esta lista aparecen todas las acciones y los parámetros que acepta la función. Ojo, sólo acepta un parámetro.
						
			REMOVE:__INTERNAL_ID__ -> Elimina el contenido con la ID INTERNA __INTERNAL_ID__ del carrusel actual.
			
			ADD -> Añade contenido al carrusel actual, el contenido que añadirá lo tomará de $_POST.
			
	*/
	
	function wp_carousel_carousel_options_page($var='', $debug=false)
	{
		// Cargamos la ID del usuario, que usaremos para mostrar una u otra interfaz y las rutas
		global $user_ID, $wp_carousel_path;
		
		$wp_carousel_no_ajax_mode = false; // Damos por hecho que el modo AJAX funciona
		
		/* Comprobamos si podemos cargar el archivo */
		
		if (!is_readable('../wp-blog-header.php'))
		{
			$wp_carousel_no_ajax_mode = true;
		}
		
		/*
			Cargamos los extras
		*/
			
		wp_carousel_load_extras();
		
		// Establecemos $can['UNDO'] en false, ya que no podemos deshacer nada (de momento)
		$can['UNDO'] = false;
		$will['UPDATE_WP_CAROUSEL_OPTION'] = false;
		$will['CANCEL'] = false;
		$will['ADD_COSTUMIZED_CONTENT'] = false;
		$will['SHOW_EDIT_FORM'] = false;
		$will['UPDATE_WP_CAROUSEL_CONFIG'] = false;
		$will['UPDATE_BOTH_WP_CAROUSEL_OPTION_AND_CONFIG'] = false;
					
		// Cargamos la ID del carrusel en la matriz
		$this_carousel['ID'] = explode('-', $_GET['page']);
		$this_carousel['ID'] = $this_carousel['ID'][2];
		
		// Comprobamos que la supuesta ID es un número, si no lo es, no se podrá ejecutar correctamente el script
		if(is_numeric($this_carousel['ID']))
		{
			// Como el valor es numérico, podemos proseguir aquí con el código del plugin
			// Tenemos que crear una variable que almacene el contenido del carrusel, que más adelante le pasaremos a otra función
			$items = get_option(WP_CAROUSEL_ITEMS_TABLE);
			$items = maybe_unserialize($items);
			
			// Cargamos la configuración del carrusel
			$config = get_option(WP_CAROUSEL_CONFIG_TABLE);
			$config = maybe_unserialize($config);
						
			// Vamos a analizar la acción a realizar y la vamos a ejecutar
			if (isset($_GET['action']))
			{
					
				$action = explode(':', $_GET['action']);
				
				switch($action[0])
				{
					case 'REMOVE':
					
						/* ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
												
						break;
		
					case 'ADD':
					
						/* ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
												
						break;
						
					case 'IMPORT':
					
						/* ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
												
						break;
						
					case 'EDIT':
					
						/* ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
												
						break;
						
					case 'SAVE_EDIT':
					
						/* ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
																
						break;
						
					case 'SAVE_OPTIONS':
					
						/*	ESTA ACCION SE REALIZA CON AJAX, YA NO DEBE DARSE MAS ESTE CASO */
												
						break;
						
					case 'UPDATE_THEME':
						$config[$this_carousel['ID']]['THEME'] = $action[1];
						$will['UPDATE_WP_CAROUSEL_CONFIG'] = true;
						break;
						
					case 'SAVE-NO-AJAX':
						if (isset($action[1]) && isset($action[2]))
						{
							
							$carousel_id = $action[2];
							
							/*
								Cargamos el contenido original del carrusel
							*/
							
							$wp_carousel_content = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE)); // Obtenemos el contenido de todos los carruseles
							$carousel_content = $wp_carousel_content[$carousel_id]; // Centramos el contenido en el del carrusel actual
							
							$wp_carousel_config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE)); // Obtenemos la configuración de todos los carruseles
							$carousel_config = $wp_carousel_config[$carousel_id]; // Centramos la configuración en la del carrusel actual
							
							/*
								Actualizamos las matrices para que reemplacen el contenido antiguo por el nuevo y mantengan el viejo contenido no actualizado intacto
							*/
							
							switch (true):
								
								case ($_GET['in_mode'] == 'updateStandardOptions'): // Se han actualizado las opciones estándar
									
										$new_config = maybe_unserialize(base64_decode($action[1]));
					
										$final_config = $carousel_config; // Guardamos las opciones previas en una nueva matriz que manipularemos y convertiremos en las opciones antiguas actualizadas
										
										foreach ($final_config as $fc_key => $fc_value) // Iteramos sobre todos los índices de las opciones antiguas
										{
											if (isset($new_config[$fc_key]))
											{
												$final_config[$fc_key] = $new_config[$fc_key];
											}
										}
										
										foreach ($new_config as $nc_key => $nc_value) // Hacemos lo mismo que antes, sólo que con las opciones nuevas, por si hemos dejado algo por modificar
										{
											if (!isset($final_config[$nc_key]))
											{
												$final_config[$nc_key] = $nc_value;
											}
										}
										
										$final_content = $carousel_content;
									
										break;
								
									case ($_GET['in_mode'] == 'updateThemeOptions'): // En el caso de las opciones del theme, éstas siempre reemplazan a las anteriores
									
										$new_theme_config = maybe_unserialize(base64_decode($action[1]));
									
										$final_config = $carousel_config;
										
										$final_config['THEME_SETTINGS'] = $new_theme_config;
										$final_content = $carousel_content;						
									
										break;
							
								default: // No se ha hecho nada de nada, es decir, ha habido un error
									break;
								case ($_GET['in_mode'] == 'updateSortableContent'):	// Se ha actualizado el contenido, no es necesario hacer nada en este caso
									$final_content = maybe_unserialize(base64_decode($action[1]));
									$final_config = $carousel_config;
									break;
							
							endswitch;
							
							$items[$action[2]] = $final_content;
							$config[$action[2]] = $final_config;
							
							$will['UPDATE_BOTH_WP_CAROUSEL_OPTION_AND_CONFIG'] = true;
						}
						break;
						
					default:
						// No hacemos nada
						break;
				}
			}
			
			// Actualizamos la Base de Datos, si es que hay algo que actualizar...
			if ($will['UPDATE_BOTH_WP_CAROUSEL_OPTION_AND_CONFIG'])
			{
				$items_serialized = serialize($items);
				update_option(WP_CAROUSEL_ITEMS_TABLE, $items_serialized);
				
				$config_serialized = serialize($config);
				update_option(WP_CAROUSEL_CONFIG_TABLE, $config_serialized);
			}
			elseif ($will['UPDATE_WP_CAROUSEL_OPTION'])
			{
				$items_serialized = serialize($items);
				update_option(WP_CAROUSEL_ITEMS_TABLE, $items_serialized);
			}
			elseif ($will['UPDATE_WP_CAROUSEL_CONFIG'])
			{
				$config_serialized = serialize($config);
				update_option(WP_CAROUSEL_CONFIG_TABLE, $config_serialized);
			}
			
			// Ahora que ya hemos acabado de ejecutar acciones, podemos seguir
			$items = $items[$this_carousel['ID']];
			ksort($items);
		}
		
		if ($can['UNDO'])
		{
			switch($action[0])
			{
				case 'REMOVE':
					echo '<div id="message" class="updated fade"><p>';
					printf(__('The item has been removed. Do you want to <a href="%s">undo</a>?', 'wp_carousel'), wp_carousel_create_internal_urls($can['UNDO_URL_TYPE'], 'get', false));
					echo '</p></div>';
					break;
				case 'ADD':
					echo '<div id="message" class="updated fade"><p>';
					printf(__('The item has been added. Do you want to <a href="%s">undo</a>?', 'wp_carousel'), wp_carousel_create_internal_urls($can['UNDO_URL_TYPE'], 'get', false));
					echo '</p></div>';
					break;
				case 'SAVE_EDIT':
					echo '<div id="message" class="updated fade"><p>';
					printf(__('The item has been edited. Do you want to <a href="%s">undo</a>?', 'wp_carousel'), wp_carousel_create_internal_urls($can['UNDO_URL_TYPE'], 'get', false));
					echo '</p></div>';
					break;
				default:
					// No hacemos nada
					break;
			}
		}
			
		?>
				
		<div class="updated fade"><p>
		<?php
			printf(__('To show this carousel in your theme, add this code in the place where you want to show it: %s', 'wp_carousel'), '&nbsp;<code>&lt;?php wp_carousel('.$this_carousel['ID'].'); ?&gt;</code><br />');
			if (WP_CAROUSEL_EI) 
			{
				printf(__('To share this carousel with other users, use this URL: %s', 'wp_carousel'), '&nbsp;<a href="'.$wp_carousel_path[6].'wp-carousel-ei.php?carousel_id='.$this_carousel['ID'].'">'.$wp_carousel_path[6].'wp-carousel-ei.php?carousel_id='.$this_carousel['ID'].'</a>'); 
			}
		?>
		</p></div>
		
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<form name="form_carousel_name" method="post" id="form_carousel_name" onsubmit="return wp_carousel_update_ajax_carousel_name()">
				<?php
					$this_carousel_name = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));

					if (isset($this_carousel_name[$this_carousel['ID']]['CAROUSEL_NAME']))
					{ 
						$this_carousel_name = $this_carousel_name[$this_carousel['ID']]['CAROUSEL_NAME'];
					}
					else
					{
						$this_carousel_name = __('Carousel ', 'wp_carousel').$this_carousel['ID'];
					}
				?>
				<input type="text" name="carousel_name" id="carousel_name" value="<?php echo $this_carousel_name; ?>" />
			</form>  
			<h2><span id="carousel_id"><?php echo $this_carousel['ID']; ?></span></h2>
					
			<?php if (get_the_author_meta('wp_carousel_has_shown_survey', $user_ID) != 'yes'): ?>
				<div class="updated survey"><p><?php printf(__('Help me to improve <strong>WP Carousel</strong>, fill up <a href="%s">this survey</a> - Note that you can fill up the survey at any moment', 'wp_carousel'), WP_CAROUSEL_SURVEY); ?></p></div>
				<?php update_user_meta($user_ID, 'wp_carousel_has_shown_survey', 'yes'); ?>
			<?php endif; ?>
						
			<div id="wp_carousel_ajax_loader">
				<div>
					<img src="<?php echo $wp_carousel_path[6]; ?>img/ajax-loader.gif" align="<?php _e('Saving changes', ' wp_carousel'); ?>" title="<?php _e('Saving changes, please, wait a moment', ' wp_carousel'); ?>" />
				</div>
			</div>
			
			<div id="wp_carousel_ajax_response">
				
			</div>
			
			<a id="current_url_js" href="<?php echo $wp_carousel_path[6]; ?>update_db.php"></a>
			<a id="current_url_get_js" href="<?php echo $wp_carousel_path[6]; ?>get_db.php"></a>
			
			<div class="manage_items">
				
				<div id="items_addable_carousel">
					<h3><?php _e('Items', 'wp_carousel'); ?></h3>
					<div class="items_padder">	
						<div id="sortable_items" class="connected">
							
							<?php if (WP_CAROUSEL_SHOW_CATEGORIES_IN_CAROUSEL_OPTIONS): ?>
							<div id="item_1" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_1" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php _e('Category', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add posts from an <strong>specific category</strong>', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
											
											<dl>
												<dt><?php _e('Category', 'wp_carousel'); ?></dt>
												<dd><?php wp_carousel_dropdown_type_items('1'); ?></dd>
											</dl>
											<dl>
												<dt><?php _e('Posts\' order', 'wp_carousel'); ?></dt>
												<dd>
													<select id="posts_order" name="posts_order">
														<option id="first_old" value="first_old"><?php _e('Show the oldest posts first', 'wp_carousel'); ?></option>
														<option id="first_new" value="first_new"><?php _e('Show the newest posts first', 'wp_carousel'); ?></option>
														<option id="random" value="random"><?php _e('Random order', 'wp_carousel'); ?></option>
													</select>
												</dd>
											</dl>
 											<dl>
												<dt><?php _e('Number of posts', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="posts_number" id="posts_number" value="10" /></dd>
											</dl>
											<dl>
												<dt><?php _e('Must I show this element in the loop?', 'wp_carousel'); ?></dt>
												<dd><input type="checkbox" name="show_in_loop" id="show_in_loop" value="yes" checked="checked" /></dd>
											</dl>
											
											<div class="clear_dl"></div>
																																	
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="1" />
																																												
										</form>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if (WP_CAROUSEL_SHOW_TAGS_IN_CAROUSEL_OPTIONS): ?>
							<div id="item_5" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_5" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php _e('Tag', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add posts from an <strong>specific tag</strong>', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
											<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
												
												<dl>
													<dt><?php _e('Tag', 'wp_carousel'); ?></dt>
													<dd><?php wp_carousel_dropdown_type_items('5'); ?></dd>
												</dl>
												<dl>
													<dt><?php _e('Posts\' order', 'wp_carousel'); ?></dt>
													<dd>
														<select id="posts_order" name="posts_order">
															<option id="first_old" value="first_old"><?php _e('Show the oldest posts first', 'wp_carousel'); ?></option>
															<option id="first_new" value="first_new"><?php _e('Show the newest posts first', 'wp_carousel'); ?></option>
															<option id="random" value="random"><?php _e('Random order', 'wp_carousel'); ?></option>
														</select>		
													</dd>
												</dl>
												<dl>
													<dt><?php _e('Number of posts', 'wp_carousel'); ?></dt>
													<dd><input type="text" name="posts_number" id="posts_number" value="10" /></dd>
												</dl>
												<dl>
													<dt><?php _e('Must I show this element in the loop?', 'wp_carousel'); ?></dt>
													<dd><input type="checkbox" name="show_in_loop" id="show_in_loop" value="yes" checked="checked" /></dd>
												</dl>
												
												<div class="clear_dl"></div>
																																		
												<input type="hidden" name="order" id="order" value="0" />
												<input type="hidden" name="type" id="type" value="5" />
																																													
											</form>
										</div>
									</div>
							</div>
							<?php endif; ?>
							
							<?php if (WP_CAROUSEL_SHOW_POSTS_IN_CAROUSEL_OPTIONS): ?>
							<div id="item_2" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_2" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<div class="item_thumbnail"></div>
									<h4><?php _e('Post', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add a <strong>single post</strong> to the carousel', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
											
											<dl>
												<dt><?php _e('Post', 'wp_carousel'); ?></dt>
												<dd><?php wp_carousel_dropdown_type_items('2'); ?></dd>
											</dl>
											<dl>
												<dt><?php _e('Must I show this element in the loop?', 'wp_carousel'); ?></dt>
												<dd><input type="checkbox" name="show_in_loop" id="show_in_loop" value="yes" checked="checked" /></dd>
											</dl>
											
											<div class="clear_dl"></div>
																																	
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="2" />
											<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
											<input type="hidden" name="posts_number" id="posts_number" value="0" />
																																												
										</form>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if (WP_CAROUSEL_SHOW_PAGES_IN_CAROUSEL_OPTIONS): ?>
							<div id="item_3" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_3" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<div class="item_thumbnail"></div>
									<h4><?php _e('Page', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add a <strong>single page</strong> to the carousel', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
											
											<dl>
												<dt><?php _e('Page', 'wp_carousel'); ?></dt>
												<dd><?php wp_carousel_dropdown_type_items('3'); ?></dd>
											</dl>
											
											<div class="clear_dl"></div>
																																	
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="3" />
											<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
											<input type="hidden" name="posts_number" id="posts_number" value="0" />
											<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
																																												
										</form>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if (WP_CAROUSEL_SHOW_AUTHORS_IN_CAROUSEL_OPTIONS): ?>
							<div id="item_6" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_6" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php _e('Author', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add posts from an <strong>specific author</strong>', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
											<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
												
												<dl>
													<dt><?php _e('Author', 'wp_carousel'); ?></dt>
													<dd><?php wp_carousel_dropdown_type_items('6'); ?></dd>
												</dl>
												<dl>
													<dt><?php _e('Posts\' order', 'wp_carousel'); ?></dt>
													<dd>
														<select id="posts_order" name="posts_order">
															<option id="first_old" value="first_old"><?php _e('Show the oldest posts first', 'wp_carousel'); ?></option>
															<option id="first_new" value="first_new"><?php _e('Show the newest posts first', 'wp_carousel'); ?></option>
															<option id="random" value="random"><?php _e('Random order', 'wp_carousel'); ?></option>
														</select>		
													</dd>
												</dl>
												<dl>
													<dt><?php _e('Number of posts', 'wp_carousel'); ?></dt>
													<dd><input type="text" name="posts_number" id="posts_number" value="10" /></dd>
												</dl>
												<dl>
													<dt><?php _e('Must I show this element in the loop?', 'wp_carousel'); ?></dt>
													<dd><input type="checkbox" name="show_in_loop" id="show_in_loop" value="yes" checked="checked" /></dd>
												</dl>
												
												<div class="clear_dl"></div>
																																		
												<input type="hidden" name="order" id="order" value="0" />
												<input type="hidden" name="type" id="type" value="6" />
																																													
											</form>
										</div>
									</div>
							</div>
							<?php endif; ?>
							
							<div id="item_4" class="item costumized_content">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_4" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php _e('Costumized Content', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add a <strong>costumized content</strong> to the carousel', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
											
											<dl>
												<dt><?php echo _e('Image URL', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="url_image" id="url_image" value="http://" /></dd>
											</dl>
											
											<dl>
												<dt><?php echo _e('Video URL', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="url_video" id="url_video" value="http://" /></dd>
											</dl>
										
											<dl>
												<dt><?php echo _e('Link URL', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="url_link" id="url_link" value="http://" /></dd>
											</dl>
											
											<input type="text" name="post_title" size="30" tabindex="1" id="title" autocomplete="off" value="<?php echo _e('Title', 'wp_carousel'); ?>" />
											
											<br /><br />
											
											<textarea rows='10' cols='26' name='desc' tabindex='2' id='desc'><?php echo _e('Description', 'wp_carousel'); ?></textarea>
											
											<div class="clear_dl"></div>
																																	
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="4" />
											<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
											<input type="hidden" name="posts_number" id="posts_number" value="0" />
											<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
											<input type="hidden" name="category_id" id="category_id" value="0" />
																																												
										</form>
									</div>
								</div>
							</div>
							<?php if (WP_CAROUSEL_EI): ?>
							<div id="item_7" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_7" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php _e('External Carousel', 'wp_carousel'); ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php _e('Drag this item into the carousel to add a <strong>WP Carousel\'s carousel from other WordPress blog</strong> to the carousel', 'wp_carousel'); ?></p>
									<div class="add_form wp_carousel_disable_drag">
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
											
											<dl>
												<dt><?php _e('URL', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="wp_carousel_ei_url" id="wp_carousel_ei_url" value="http://" /></dd>
											</dl>
											<dl>
												<dt><?php _e('Carousel\'s ID', 'wp_carousel'); ?></dt>
												<dd><input type="text" name="wp_carousel_ei_id" id="wp_carousel_ei_id" value="0" /></dd>
											</dl>
											
											<div class="clear_dl"></div>
											
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="7" />
											<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
											<input type="hidden" name="posts_number" id="posts_number" value="0" />
											<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
											<input type="hidden" name="category_id" id="category_id" value="0" />
																																												
										</form>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php
								if ($_SESSION['WP_CAROUSEL_EXTRAS'] || is_array($_SESSION['WP_CAROUSEL_EXTRAS'])):
								foreach ($_SESSION['WP_CAROUSEL_EXTRAS'] as $key => $extra)
								{
							?>
							<div id="item_<?php echo $key; ?>" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_<?php echo $key; ?>" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<h4><?php echo $extra['name']; ?></h4>
								</div>
								<div class="item_content">
									<p class="pre_dropped"><?php echo $extra['desc']; ?></p>
									<div class="add_form wp_carousel_disable_drag">
									
										<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
										
											<table>
													
										<?php
											$must_show_legacy_settings = false;
											if (isset($extra['custom_settings']))
											{
												if (is_array($extra['custom_settings']))
												{
													
													foreach ($extra['custom_settings'] as $setting_key => $setting_value):
														$setting_key = strtoupper($setting_key);
														if (!isset($config['THEME_SETTINGS'][$setting_key]) && $setting_value['type'] != 'group')
														{
															$config['THEME_SETTINGS'][$setting_key] = $setting_value['default_value'];
														}
										?>
												
													<?php if ($setting_value['type'] == 'group') { ?>
														<tr>
															<td colspan="2" class="th_title"><?php _e($setting_value['title'], 'wp_carousel'); ?></td>
														</tr>
													<?php } else { ?>
														<tr>
															<td class="padding_5"><label for="<?php echo $setting_key; ?>"><?php _e($setting_value['title'], 'wp_carousel'); ?></label></td>
															<td class="align_right">
															<?php
															switch (true): 
																case ($setting_value['type'] == 'textarea'):
															?>
																	<textarea name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" cols="30" rows="5"><?php echo $config['THEME_SETTINGS'][$setting_key]; ?></textarea>
															<?php
																	break;
																case ($setting_value['type'] == 'checkbox'):
															?>
																	<input type="checkbox" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="yes"<?php if ($config['THEME_SETTINGS'][$setting_key] == "1") { echo ' checked="checked"'; } ?> />
															<?php
																	break;
																case ($setting_value['type'] == 'text'):
															?>
																	<input name="<?php echo $setting_key; ?>" type="text" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
															<?php
																	break;
																case ($setting_value['type'] == 'password'):
															?>
																	<input name="<?php echo $setting_key; ?>" type="password" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
															<?php
																	break;
																case ($setting_value['type'] == 'select'):
															?>
																	<select name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>">
																	<?php
																		if (is_array($setting_value['values']))
																		{
																			foreach ($setting_value['values'] as $value_key => $value_value):
																				?><option value="<?php echo $value_key; ?>"<?php if ($value_key == $config['THEME_SETTINGS'][$setting_key]) { echo ' selected="selected"'; } ?>><?php echo $value_value; ?></option> <?php
																			endforeach;
																		}
																	?>
																	</select>
															<?php
																	break;
																default:
																	break;
															endswitch;
															?>
															</td>
														</tr>
													<?php } ?>
												
												<?php endforeach; ?>
										
										<?php
												}
												else
												{
													$must_show_legacy_settings = true;
												}
											}
											else
											{
												$must_show_legacy_settings = true;
											}
											
											if ($must_show_legacy_settings)
											{
										?>
												
											<tr>
												<td class="padding_5"><?php _e('Item', 'wp_carousel'); ?></td>
												<td class="align_right"><input type="text" name="category_id" id="category_id" value="" /></td>
											</tr>
											
										<?php } ?>
																						
											<input type="hidden" name="order" id="order" value="0" />
											<input type="hidden" name="type" id="type" value="<?php echo $key; ?>" />
											<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
											<input type="hidden" name="posts_number" id="posts_number" value="0" />
											<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
											
											</table>
																																												
										</form>
									</div>
								</div>
							</div>
							<?php
								}
								endif;
							?>
							
							<div class="clear"></div>
							
						</div>
					</div>
				</div>
				
				<div id="will_be_deleted">
					<h3><?php _e('Delete', 'wp_carousel'); ?></h3>
					<div class="items_padder">
						<div id="sortable_deleted" class="connected2">
							<p><?php _e('Drop items here to remove them from the carousel', 'wp_carousel'); ?>
						</div>
					</div>
					<hr class="fixer">
				</div>
				
				<hr class="wp_carousel_admin_separator" />
				
				<div id="items_in_carousel">
					<h3><?php _e('Carousel', 'wp_carousel'); ?></h3>
					<div class="items_padder">
						<div id="sortable_carousel" class="connected">
						
							<form method="post" onsumbit="return false" class="wp_carousel_toggle_all_form">
								<input type="submit" name="submit" class="wp_carousel_hide_all_form_submit" value="<?php _e('Hide all','wp_carousel'); ?>"  onclick="return wp_carousel_toggle_all()"/>
								<input type="submit" name="submit" class="wp_carousel_show_all_form_submit" value="<?php _e('Show all','wp_carousel'); ?>" onclick="return wp_carousel_toggle_all()" />
							</form>
						
							<form method="post" onsubmit="return wp_carousel_update_ajax_item()">
								<input name="publish" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php echo _e('Save', 'wp_carousel'); ?>" />
							</form>
						
							<?php wp_carousel_carousel_show_carousel_item_list($items, 'drag_drop'); ?>														
							
							<?php if (count($items) > 0 ) { ?>
							
							<form method="post" onsumbit="return false" class="wp_carousel_toggle_all_form">
								<input type="submit" name="submit" class="wp_carousel_hide_all_form_submit" value="<?php _e('Hide all','wp_carousel'); ?>"  onclick="return wp_carousel_toggle_all()"/>
								<input type="submit" name="submit" class="wp_carousel_show_all_form_submit" value="<?php _e('Show all','wp_carousel'); ?>" onclick="return wp_carousel_toggle_all()" />
							</form>
							
							<form method="post" onsubmit="return wp_carousel_update_ajax_item()">
								<input name="publish" type="submit" class="button-primary" tabindex="5" accesskey="p" value="<?php echo _e('Save', 'wp_carousel'); ?>" />
							</form>		
							
							<?php } else { ?>	
							
							<p><?php _e('Drag items here to add them to the carousel', 'wp_carousel'); ?></p>
							
							<?php } ?>
							
						</div>
					</div>
					<hr class="fixer">
				</div>
				
				<div class="clear"></div>
			
			</div>
			
			<?php 			
				wp_carousel_themes_options_area($this_carousel['ID']);
			?>
			
			<?php /*<?php wp_carousel_create_internal_urls('DELETE_CAROUSEL:'.$this_carousel['ID'], 'show'); ?>*/ ?>
			<p><a href="#" class="button-primary button-delete" id="delete_current_carousel"><?php echo _e('Delete this carousel', 'wp_carousel'); ?></a></p>
			
			<div id="overlay_wp_carousel_popup"></div>
			
			<div id="delete_current_carousel_popup">
			<?php		
				printf('<p>'.__('Do you really want to delete the carousel "%s" (ID %s)? That can\'t be undone.', 'wp_carousel').'</p>', '<em>'.$this_carousel_name.'</em>', $this_carousel['ID']);
				printf('<p>'.__('Click <a href="%s">here</a> to delete the carousel or click the darker region to close this popup', 'wp_carousel').'</p>', wp_carousel_create_internal_urls('DELETE_CAROUSEL:'.$this_carousel['ID']).'&sure=yes');		
			?>
			</div>
						
		</div>
		
		<div class="clear"></div>

		<?php	
	}
	
	/*
		@Función: wp_carousel_carousel_show_carousel_item_list()
		@Versión: 2.1
		@Parámetros:
							$items (array): Contiene los elementos del carrusel actual, que parseará la función y mostrará en formato de lista.
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Muestra la lista de elementos del carrusel.
		@Añadida en la versión: 0.4	
		@Última actualización en la versión: 1.0
	*/
	
	function wp_carousel_carousel_show_carousel_item_list($items, $debug=false)
	{
	
		$drag_drop_id = 0;
		
		foreach ($items as $internal_id => $item):
			$drag_drop_id++;
		
			// Cargamos el nombre del elemento
			if ($item['TYPE'] == 4)
			{
				$item['NAME'] = wp_carousel_item_value($internal_id, $item['TYPE'], 'name', $items);
			}
			elseif ($item['TYPE'] == 7)
			{
				$item['NAME'] = __('External Carousel', 'wp_carousel');
			}
			elseif (is_numeric($item['TYPE']))
			{
				$item['NAME'] = wp_carousel_item_value($item['ID'], $item['TYPE'], 'name');
			}
			else
			{
				$item['NAME'] = '';
			}
			
			// Ahora cargamos su descripcion
			if ($item['TYPE'] == 4)
			{
				$item['DESC'] = wp_carousel_item_value($internal_id, $item['TYPE'], 'desc', $items);
			}
			elseif ($item['TYPE'] == 1)
			{
				$item['DESC'] = wp_carousel_item_value($item['ID'], $item['TYPE'], 'desc', $item);
			}
			elseif ($item['TYPE'] == 7)
			{
				$item['DESC'] = sprintf(__('External Carousel from %s', 'wp_carousel'), $item['WP_CAROUSEL_EI_URL']);
			}
			elseif (is_numeric($item['TYPE']))
			{
				$item['DESC'] = wp_carousel_item_value($item['ID'], $item['TYPE'], 'desc');
			}
			else
			{
				$item['DESC'] = '';
			}
			
			// Ahora cargamos la URL de la imagen y del link
			if ($item['TYPE'] == 2 ||$item['TYPE'] == 3)
			{
				$item['LINK_URL'] = wp_carousel_item_value($item['ID'], $item['TYPE'], 'link_url');
				$item['IMAGE_URL'] = wp_carousel_item_value($item['ID'], $item['TYPE'], 'image_url');
			}
			
			// Comprobamos si este elemento es de un tipo de la función "wp_carousel_create_internal_urls()" pueda crear un enlace hacia su página de edición mediante el uso del tipo "EDIT_URL"
			if ($item['TYPE'] != 1 && $item['TYPE'] != 4)
			{
				// Es un elemento con un link hacia su página de edición sencillo de calcular
				$item['HAS_WP_EDIT_URL'] = true;
			}
			elseif ($item['TYPE'] == 1)
			{
				$item['HAS_WP_EDIT_URL'] = false;
			}
			else
			{
				// Hoy no estamos de suerte, el link hacia la página de edición de este elemento es más complejo de lo que querríamos
				$item['HAS_WP_EDIT_URL'] = false;
			}
			
			// Comprobemos qué tipo de enlace hacia su página de edición hemos dicho que tiene
			if ($item['HAS_WP_EDIT_URL'])
			{
				// Tiene un enlace fácil, así que lo almacenamos ya en $item['EDIT_URL']
				$item['EDIT_URL'] = wp_carousel_create_internal_urls('EDIT_URL:'.$item['ID']);
			}
			
			// Comprobemos si tenía un link de algún tipo hacia su página de edición (las categorías no tienen este tipo de link)
			if (!$item['HAS_WP_EDIT_URL'] && $item['TYPE'] != 4)
			{
				// Vale, no tiene un link sencillo y no es un contenido personalizable: estamos ante un elemento sin página de edición
				$item['HAS_EDIT_PAGE_URL'] = false;
			}
			else
			{
				// Bien, o es un elemento sencillo, o es un contenido personalizable, así que tiene página de edición
				$item['HAS_EDIT_PAGE_URL'] = true;
				
				if ($item['TYPE'] == 4)
				{
					$item['EDIT_URL'] = wp_carousel_create_internal_urls('EDIT_COSTUMIZED_CONTENT_URL:'.$internal_id);
				}
				
			}

			?>
							<div id="item_<?php echo $drag_drop_id; ?>" class="item">
								<div class="handle">
									<form method="post" class="wp_carousel_toggle_items_form" onsubmit="return false">
										<input type="hidden" value="shown" name="status" class="wp_carousel_toggle_status" />
										<input type="hidden" value="item_<?php echo $drag_drop_id; ?>" name="id" />
										<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_item(this.form)" class="wp_carousel_toggle_submit" />
									</form>
									<?php
										if (in_array($item['TYPE'], array(2, 3)))
										{
											$this_item_image = wp_carousel_item_value($item['ID'], $item['TYPE'], "image_url"); 
										} else {
											$this_item_image = '';
										}
									?>
									<div class="item_thumbnail" style="background-image:url(<?php
										if ($this_item_image != '')
										{
											echo $this_item_image;
										}
										else
										{
											echo "images/gray-grad.png";
										}
									?>)"></div>
									<h4>
										<?php if (is_numeric($item['TYPE'])) _e(wp_carousel_type_name($item['TYPE'], 'get'), 'wp_carousel'); else echo $_SESSION['WP_CAROUSEL_EXTRAS'][$item['TYPE']]['name']; ?>
									</h4>
								</div>
								<div class="item_content">
																
								<form method="post" class="wp_carousel_ajax_form" onsubmit="return wp_carousel_update_ajax_item()">
									
									<?php if ($item['TYPE'] != 4): ?>	
									<?php if ($item['TYPE'] != 7) { ?>	
										<?php if (is_numeric($item['TYPE'])) { ?>
											<dl>
												<dt><?php _e(wp_carousel_type_name($item['TYPE'], 'get'), 'wp_carousel'); ?></dt>
												<dd><?php wp_carousel_dropdown_type_items($item['TYPE'], $item['ID']); ?></dd>
											</dl>
										<?php } else { ?>
										
											<?php // Este es un EXTRA ?>
										
											<table>
													
										<?php
											$must_show_legacy_settings = false;
											
											if (isset($_SESSION['WP_CAROUSEL_EXTRAS'][$item['TYPE']])):											

											if (isset($_SESSION['WP_CAROUSEL_EXTRAS'][$item['TYPE']]['custom_settings']))
											{
												if (is_array($_SESSION['WP_CAROUSEL_EXTRAS'][$item['TYPE']]['custom_settings']))
												{
													
													foreach ($_SESSION['WP_CAROUSEL_EXTRAS'][$item['TYPE']]['custom_settings'] as $setting_key => $setting_value):
														$setting_key = strtoupper($setting_key);
														if (!isset($config['THEME_SETTINGS'][$setting_key]) && $setting_value['type'] != 'group')
														{
															$config['THEME_SETTINGS'][$setting_key] = $setting_value['default_value'];
														}
										?>
												
													<?php if ($setting_value['type'] == 'group') { ?>
														<tr>
															<td colspan="2" class="th_title"><?php _e($setting_value['title'], 'wp_carousel'); ?></td>
														</tr>
													<?php } else { ?>
														<tr>
															<td class="padding_5"><label for="<?php echo $setting_key; ?>"><?php _e($setting_value['title'], 'wp_carousel'); ?></label></td>
															<td>
															<?php
															switch (true): 
																case ($setting_value['type'] == 'textarea'):
															?>
																	<textarea name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" cols="30" rows="5"><?php echo $item[$setting_key]; ?></textarea>
															<?php
																	break;
																case ($setting_value['type'] == 'checkbox'):
															?>
																	<input type="checkbox" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="yes"<?php if ($item[$setting_key] == "1") { echo ' checked="checked"'; } ?> />
															<?php
																	break;
																case ($setting_value['type'] == 'text'):
															?>
																	<input name="<?php echo $setting_key; ?>" type="text" id="<?php echo $setting_key; ?>" value="<?php echo $item[$setting_key]; ?>" />
															<?php
																	break;
																case ($setting_value['type'] == 'password'):
															?>
																	<input name="<?php echo $setting_key; ?>" type="password" id="<?php echo $setting_key; ?>" value="<?php echo $item[$setting_key]; ?>" />
															<?php
																	break;
																case ($setting_value['type'] == 'select'):
															?>
																	<select name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>">
																	<?php
																		if (is_array($setting_value['values']))
																		{
																			foreach ($setting_value['values'] as $value_key => $value_value):
																				?><option value="<?php echo $value_key; ?>"<?php if ($value_key == $item[$setting_key]) { echo ' selected="selected"'; } ?>><?php echo $value_value; ?></option> <?php
																			endforeach;
																		}
																	?>
																	</select>
															<?php
																	break;
																default:
																	break;
															endswitch;
															?>
															</td>
														</tr>
													<?php } ?>
												
												<?php endforeach; ?>
										
										<?php
												}
												else
												{
													$must_show_legacy_settings = true;
												}
											}
											else
											{
												$must_show_legacy_settings = true;
											}
											
											else:
												$must_show_legacy_settings = true;
											endif;
											
											if ($must_show_legacy_settings)
											{
										?>
												
											<tr>
												<td class="padding_5"><?php _e('Item', 'wp_carousel'); ?></td>
												<td class="align_right"><input type="text" name="category_id" id="category_id" value="<?php echo $item['ID']; ?>" /></td>
											</tr>
											
										<?php } ?>
																						
											
											
											</table>

										<?php } ?>
									<?php } else { ?>											
									<dl>
										<dt><?php _e('URL', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="wp_carousel_ei_url" id="wp_carousel_ei_url" value="<?php echo $item['WP_CAROUSEL_EI_URL']; ?>" /></dd>
									</dl>
									<dl>
										<dt><?php _e('Carousel\'s ID', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="wp_carousel_ei_id" id="wp_carousel_ei_id" value="<?php echo $item['WP_CAROUSEL_EI_ID']; ?>" /></dd>
									</dl>
									
									<?php 
																												
										if (fopen($item['WP_CAROUSEL_EI_URL'], 'r')) {
												
											$wp_carousel_ei_content = file_get_contents($item['WP_CAROUSEL_EI_URL'].'?carousel_id='.$item['WP_CAROUSEL_EI_ID']);
											if ($wp_carousel_ei_content != 'ERROR:WP_CAROUSEL_EI:FALSE' && $wp_carousel_ei_content != 'ERROR:$_GET["carousel_id"]:NOT-SET' && $wp_carousel_ei_content != 'ERROR:$_GET["carousel_id"]:IS-NOT-A-CAROUSEL' && WP_CAROUSEL_EI)
											{
												echo '<p>'.__('This external carousel can be loaded', 'wp_carousel').'</p>';
											}
											else
											{
												if ($wp_carousel_ei_content == 'ERROR:WP_CAROUSEL_EI:FALSE')
												{
													echo '<p>'.__('This carousel can\'t be loaded because its content is not shared', 'wp_carousel').'</p>';
												}
												if ($wp_carousel_ei_content == 'ERROR:$_GET["carousel_id"]:NOT-SET')
												{
													echo '<p>'.__('This carousel can\'t be loaded because the carousel\'s ID hasn\'t been set', 'wp_carousel').'</p>';
												}
												if ($wp_carousel_ei_content == 'ERROR:$_GET["carousel_id"]:IS-NOT-A-CAROUSEL')
												{
													echo '<p>'.__('This carousel can\'t be loaded because it doesn\'t exists', 'wp_carousel').'</p>';
												}
												if (WP_CAROUSEL_EI == 'ERROR:WP_CAROUSEL_EI:FALSE')
												{
													echo '<p>'.__('Your carousel must be set in shared mode to be able to show external carousels', 'wp_carousel').'</p>';
												}
											}
											
										}
										else
										{
											echo '<p>'.__('This carousel can\'t be loaded because WP Carousel can\'t load that URL', 'wp_carousel').'</p>';
										}
									?>
																		
									<input type="hidden" name="order" id="order" value="0" />
									<input type="hidden" name="type" id="type" value="7" />
									<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
									<input type="hidden" name="posts_number" id="posts_number" value="0" />
									<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
									<input type="hidden" name="category_id" id="category_id" value="0" />
									<?php } ?>
									
									<?php if ($item['TYPE'] == 1 || $item['TYPE'] == 5 || $item['TYPE'] == 6) { ?>
									<dl>
										<dt><?php _e('Content\'s order', 'wp_carousel'); ?></dt>
										<dd>
											<select id="posts_order" name="posts_order">
												<option id="first_old" value="first_old"<?php if ($item['POSTS_ORDER'] == 'first_old') echo ' selected="selected"'; ?>><?php _e('Show the oldest content first', 'wp_carousel'); ?></option>
												<option id="first_new" value="first_new"<?php if ($item['POSTS_ORDER'] == 'first_new') echo ' selected="selected"'; ?>><?php _e('Show the newest content first', 'wp_carousel'); ?></option>
												<option id="random" value="random"<?php if ($item['POSTS_ORDER'] == 'random') echo ' selected="selected"'; ?>><?php _e('Random order', 'wp_carousel'); ?></option>
											</select>		
										</dd>
									</dl>
																		
									<dl>
										<dt><?php _e('Number of items', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="posts_number" id="posts_number" value="<?php echo $item['POSTS_NUMBER']; ?>" /></dd>
									</dl>
									<?php } ?>
																		
									<?php if ($item['TYPE'] == 1 || $item['TYPE'] == 2 || $item['TYPE'] == 5 || $item['TYPE'] == 6) { ?>
									<dl>
										<dt><?php _e('Must I show this element in the loop?', 'wp_carousel'); ?></dt>
										<dd><input type="checkbox" name="show_in_loop" id="show_in_loop" value="yes"<?php if ($item['SHOW'] === "yes") { echo ' checked="checked"'; } ?> /></dd>
									</dl>
									<?php } ?>
									<?php else: ?>
									<dl>
										<dt><?php echo _e('Image URL', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="url_image" id="url_image" value="<?php echo $item['IMAGE_URL']; ?>" /></dd>
									</dl>
									
									<dl>
										<dt><?php echo _e('Video URL', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="url_video" id="url_video" value="<?php echo $item['VIDEO_URL']; ?>" /></dd>
									</dl>
									
									<dl>
										<dt><?php echo _e('Link URL', 'wp_carousel'); ?></dt>
										<dd><input type="text" name="url_link" id="url_link" value="<?php echo $item['LINK_URL']; ?>" /></dd>
									</dl>
									
									<input type="text" name="post_title" size="30" tabindex="1" id="title" autocomplete="off" value="<?php echo $item['NAME']; ?>" />
									
									<br /><br />
									
									<textarea rows='10' cols='26' name='desc' tabindex='2' id='desc'><?php echo $item['DESC']; ?></textarea>
									
									<div class="clear_dl"></div>
									
									<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
									<input type="hidden" name="posts_number" id="posts_number" value="0" />
									<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
									<input type="hidden" name="category_id" id="category_id" value="0" />
									<?php endif; ?>
									
									<div class="clear_dl"></div>
																											
									<input type="hidden" name="order" id="order" value="<?php echo $item['ORDER']; ?>" />
									<input type="hidden" name="type" id="type" value="<?php echo $item['TYPE']; ?>" />
									
									<?php if($item['TYPE'] == 3) { ?>
										<input type="hidden" name="posts_order" id="posts_order" value="first_new" />
										<input type="hidden" name="posts_number" id="posts_number" value="0" />
										<input type="hidden" name="show_in_loop" id="show_in_loop" value="yes" />
									<?php } ?>
																																																												
								</form>
								
																														
							</div>
						</div>
			<?php
		endforeach;
		
	}
	
	/*
		@Función: wp_carousel_type_name()
		@Versión: 1.0
		@Parámetros:
							$id: ID del tipo de elemento del carrusel.
							$mode (get | show | get-list | show-list): Sólo admite los valores anteriores. Dependiendo del valor, devolverá el nombre (get), lo mostrará (show), devolverá la lista de tipos en modo de menú desplegable (get-list) o la mostrará (show-list).
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Devuelve (o muestra) el nombre del tipo de elemento del carrusel a partir de la ID del tipo de elemento (por ejemplo, el tipo de elemento cuya ID es 1 es una categoría).
		@Nota: Sirve sólo para hacer correspondencias entre ID del tipo y nombre del mismo, así si en algún momento añado más IDs o modifico alguna, sólo cambio en una función y no en todas :) .
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_type_name($id, $mode='get', $debug=false)
	{
		
		switch ($id)
		{
			case '1':
				$return = __('Category', 'wp_carousel');
				break;
			case '2':
				$return = __('Post', 'wp_carousel');
				break;
			case '3':
				$return = __('Page', 'wp_carousel');
				break;
			case '4':
				$return = __('Customized Content', 'wp_carousel');
				break;
			case '5':
				$return = __('Tag', 'wp_carousel');
				break;
			case '6':
				$return = __('Author', 'wp_carousel');
				break;
			case '7':
				$return = __('External Carousel', 'wp_carousel');
				break;
			default:
				$return = '';
				break;
		}
		
		$list = '<option value="1">'.__('Category', 'wp_carousel').'</option>';
		$list .= '<option value="2">'.__('Post', 'wp_carousel').'</option>';
		$list .= '<option value="3">'.__('Page', 'wp_carousel').'</option>';
		
		switch ($mode)
		{
			case 'get':
				return $return;
				break;
			case 'show':
				echo $return;
				break;
			case 'get-list':
				return $list;
				break;
			case 'show-list':
				echo $list;
				break;
			default:
				break;
		}
		
	}
	
	/*
		@Función: wp_carousel_first_image()
		@Versión: 2.1
		@Parámetros:
							$id: ID del artículo o página de la que se debe extraer la primera imagen.
							$mode (get | show): Sólo admite los valores anteriores. Dependiendo del valor, devolverá el nombre (get) o lo mostrará (show).
		@Descripción: Devuelve (o muestra) la URL de la primera imagen del artículo o de la página con ID $id.
		@Nota: Sí, esta es la única función copiada de las versiones anteriores a la 0.4 de WP Carousel
		@Añadida en la versión: 0.1
		@Última actualización en la versión: 0.5		
	*/
	
	function wp_carousel_first_image($id, $mode='get')
	{
		
		$image_url = '';
		
		ob_start();
		ob_end_clean();
		
		$post = get_post($id);
		
		if (is_object($post) && isset($post->post_content))
		{
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if (isset($matches[1][0])) $image_url = $matches[1][0];
		}
		
		if (empty($image_url)) // Define una imagen por defecto
		{ 
			$image_url = "";
		}
		
		switch ($mode)
		{
			case 'get':
				return $image_url;
				break;
			case 'show':
				echo $image_url;
				break;
			default:
				return $image_url;
				break;
		}
		
	}
	
	/*
		@Función: wp_carousel_get_video_embed_code()
		@Versión: 2.0
		@Parámetros:
							$id: ID del elemento en WordPress.
							$mode (get | show): Sólo admite los valores anteriores. Dependiendo del valor, devolverá el código para insertar el vídeo (get) o lo mostrará (show).
		@Descripción: Devuelve el código para insertar un video del artículo o la página con ID $id.
		@Nota: Sólo los themes que soporten vídeos serán capaces de mostrarlos. Mostrar el vídeo tan sólo requiere añadir una condición que compruebe si se debe mostrar antes el vídeo y si este existe. Revisa los themes que vienen por defecto para ver cómo se hace.
		@Añadida en la versión: 1.0
	*/
	
	function wp_carousel_get_video_embed_code($id, $mode='get')
	{
		
		$video_embed_code = get_post_meta($id, WP_CAROUSEL_VIDEO_URL, true);
				
		if ($video_embed_code == '')
		{
		
			$post = get_post($id);
			
			$content = $post->post_content;
	
			preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $content, $matches);
					
			foreach ($matches as $key => $value)
			{
	
				$final_match = str_replace("\n", '', str_replace('<p>', '', str_replace('</p>', '', do_shortcode($value))));
							
				if ($final_match != $matches[$key])
				{
					$video_embed_code = $value;
					break;
				}
			}
		
		}
		
		switch ($mode)
		{
			case 'show':
				echo $video_embed_code;
				break;
			case 'get':
			default:
				return $video_embed_code;
				break;
		}	

	}
		
	
	/*
		@Función: wp_carousel_item_value()
		@Versión: 3.0
		@Parámetros:
							$id: ID del elemento del carrusel (ID del contenido, NO ID INTERNA). En el caso en el que estemos buscando la información de un contenido personalizable se utilizará la ID INTERNA. En el caso de los Extras, es la matriz que contiene la configuración de la instancia actual del Extra.
							$type: ID del tipo de elemento del que se trata (ver función wp_carousel_type_name()).
							$value (name | desc | image_url | link_url): Sólo admite los valores anteriores. Tipo de valor que se quiere obtener.
							$items: Matriz principal del contenido del carrusel (ojo, es la matriz que contiene los elementos, no la matriz que contiene los carruseles).
							$mode (get | show): Sólo admite los valores anteriores. Dependiendo del valor, devolverá el nombre (get) o lo mostrará (show).
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Devuelve (o muestra) algún valor referente al elemento de tipo $type, con $id, del carrusel.
		@Nota: Aunque en realidad la función es capaz de funcionar con cualquier tipo de contenido, sólo debe aplicarse al contenido personalizable, ya que éste es el único que no tiene una ID fija.
		@Añadida en la versión: 0.4
		@Actualizada en la versión: 1.0
	*/
	
	function wp_carousel_item_value($id, $type, $value, $items=array(), $mode='get', $debug=false)
	{	
		$text_temp = __('This category has %s posts. The limit is set to %s posts.', 'wp_carousel');
		
		$text_temp_oldest = __('The oldest posts will be showed first', 'wp_carousel');
		$text_temp_newest = __('The newest posts will be showed first', 'wp_carousel');
		
		switch ($type)
		{
			case '1':
				// Cargamos en la variable $category todo el contenido de la categoría
				$category = &get_category($id);
				// Cargamos en la variable $return['name'] el nombre de la categoría
				$return['name'] = $category->cat_name;
				// Podríamos cargar la descripción de la categoría, pero al fin y al cabo se muestran artículos de una categoría, así que mostraremos un recuento del contenido que se mostrará
				
				// POEDIT_ERROR:  He tenido problemas al ejecutar __('This category has %s posts', 'wp_carousel') en la posición correspondiente, así que me he visto obligado a crear una variable que almacene su valor para mostrarlo más adelante.
				
				if (!isset($items['POSTS_NUMBER']))
				{
					$items['POSTS_NUMBER'] = '10';
				}
								
				$return['desc'] = '<p>'.sprintf($text_temp, $category->category_count, $items['POSTS_NUMBER']).'</p><p>';
				
				if (isset($items['POSTS_ORDER']))
				{
					if ($items['POSTS_ORDER']== 'first_old')
					{
						$return['desc'].= $text_temp_oldest;
					}
					else
					{
						$return['desc'].= $text_temp_newest;
					}
				}
				else
				{
					$return['desc'].= $text_temp_newest;
				}
				
				$return['desc'].= '</p>';
				
				break;
			case '2':
				// Cargamos en $return['name'] el nombre del elemento
				$return['name'] = get_the_title($id);
				// Cargamos en $return['image_url'] la URL de la imagen
				$return['image_url'] = '';
				if (function_exists('get_the_post_thumbnail'))
				{
					$img_url_temp = get_post_thumbnail_id($id);
					$attachment_array = wp_get_attachment_image_src($img_url_temp, WP_CAROUSEL_DEFAULT_THUMBNAIL_SIZE);
					$return['image_url'] = $attachment_array[0];
				}
				if ($return['image_url'] == '')
				{
					$return['image_url'] = get_post_meta($id, WP_CAROUSEL_IMAGE_URL, true);
				}
				if ($return['image_url'] == '')
				{
					$return['image_url'] = wp_carousel_first_image($id, 'get');
				}
				// Obtenemos la URL del enlace del artículo
				$return['link_url'] = get_post_meta($id, WP_CAROUSEL_LINK_URL, true);
				if ($return['link_url'] == '')
				{
					$return['link_url'] = get_permalink($id);
				}
				// Comprobamos que el elemento tiene extracto
				$post_excerpt = has_excerpt($id);
				// Cargamos el extracto del elemento en la variable $post_excerpt. Si el elemento no tiene extracto, entonces $post_excerpt tendrá por valor false (booleano)
				if ($post_excerpt)
				{
					$post_temp = &get_post($id);
					$post_excerpt = $post_temp->post_excerpt;
				}
				// Ahora cargamos el valor del campo personalizado
				$post_meta_carousel_text = get_post_meta($id, WP_CAROUSEL_CAROUSEL_TEXT, true);
				// Si el campo personalizado no está en blanco, mostraremos como descripción su valor, si está en blanco, mostraremos el extracto
				if ($post_meta_carousel_text == '')
				{
					// El campo personalizado está en blanco, así que mostraremos el extracto
					
					$return['desc'] = $post_excerpt;
					
					if ($post_excerpt == '')
					{
						$post_temp = &get_post($id);
						$return['desc'] = $post_temp->post_content;
					}
					
				}
				else
				{
					$return['desc'] = $post_meta_carousel_text;
				}
				break;
			case '3':
				// Cargamos en $return['name'] el nombre del elemento
				$return['name'] = get_the_title($id);
				// Cargamos en $return['image_url'] la URL de la imagen
				$return['image_url'] = '';
				if (function_exists('get_the_post_thumbnail'))
				{
					$img_url_temp = get_post_thumbnail_id($id);
					$attachment_array = wp_get_attachment_image_src($img_url_temp, WP_CAROUSEL_DEFAULT_THUMBNAIL_SIZE);
					$return['image_url'] = $attachment_array[0];
				}
				if ($return['image_url'] == '')
				{
					$return['image_url'] = get_post_meta($id, WP_CAROUSEL_IMAGE_URL, true);
				}
				if ($return['image_url'] == '')
				{
					$return['image_url'] = wp_carousel_first_image($id, 'get');
				}
				// Obtenemos la URL del enlace del artículo
				$return['link_url'] = get_post_meta($id, WP_CAROUSEL_LINK_URL, true);
				if ($return['link_url'] == '')
				{
					$return['link_url'] = get_permalink($id);
				}
				// Ahora cargamos el valor del campo personalizado
				$page_meta_carousel_text = get_post_meta($id, WP_CAROUSEL_CAROUSEL_TEXT, true);
				$return['desc'] = $page_meta_carousel_text;
				
				if ($return['desc'] == '')
				{
					$post_temp = &get_post($id);
					$return['desc'] = $post_temp->post_content;
				}
				
				break;
			case '4':
				// Cargamos en $return['name'] el nombre del elemento
				$return['name'] = $items[$id]['TITLE'];
				// Cargamos en $return['image_url'] la URL de la imagen
				$return['image_url'] = $items[$id]['IMAGE_URL'];
				
				// Obtenemos la URL del enlace del elemento
				$return['link_url'] = $items[$id]['LINK_URL'];
	
				$return['desc'] = $items[$id]['DESC'];
				break;
			case 5:
				// Cargamos en la variable $tag todo el contenido de la etiqueta
				$tag = &get_tag($id);

				// Cargamos en la variable $return['name'] el nombre de la etiqueta
				$return['name'] = $tag->name;
				// Podríamos cargar la descripción de la etiqueta, pero al fin y al cabo se muestran artículos de una etiqueta, así que mostraremos un recuento del contenido que se mostrará
								
				if (!isset($items['POSTS_NUMBER']))
				{
					$items['POSTS_NUMBER'] = '10';
				}
								
				$return['desc'] = '<p>'.sprintf(__('This tag has %s posts', 'wp_carousel'), $tag->count).'</p>';
				
				break;
			case 6:

				// Cargamos en la variable $return['name'] el nombre para mostrar del autor
				$return['name'] = get_the_author_meta('display_name', $id);
								
				// Cargamos la descripcion del autor en la variable $return['desc']
				$return['desc'] = get_the_author_meta('user_description ', $id);
				break;
			default:
				if (isset($_SESSION['WP_CAROUSEL_EXTRAS'][$type]))
				{
					eval('$return["name"] = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$type]['title_function'].'("'.base64_encode(serialize($id)).'");');
					eval('$return["desc"] = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$type]['desc_function'].'("'.base64_encode(serialize($id)).'");');
					eval('$return["link_url"] = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$type]['link_url_function'].'("'.base64_encode(serialize($id)).'");');
					eval('$return["image_url"] = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$type]['image_url_function'].'("'.base64_encode(serialize($id)).'");');
					eval('$return["video_url"] = '.$_SESSION['WP_CAROUSEL_EXTRAS'][$type]['video_url_function'].'("'.base64_encode(serialize($id)).'");');
				}
				break;
		}
		
		if (!in_array($value, array('name', 'desc', 'link_url', 'image_url', 'video_url')))
		{
			$value = 'name';
		}
				
		$return[$value] = preg_replace("/\[wp_carousel\](.*?)\[\/wp_carousel\]/is", '', $return[$value]); 
		$return[$value] = preg_replace("/\[wp_carousel theme=\"(.*?)\"\](.*?)\[\/wp_carousel\]/is", '', $return[$value]); 
		
		switch ($mode)
		{
			case 'get':
				return $return[$value];
				break;
			case 'show':
				echo $return[$value];
				break;
			default:
				break;
		}
		
	}
	
	/*
		@Función: wp_carousel_calculate_new_id()
		@Versión: 1.0
		@Parámetros:
							$items: Matriz principal de contenido, se debe enviar la matriz correspondiente al contenido del carrusel que se quiere analizar, no la matriz que contiene los carruseles.
							$tpye: ID del tipo de contenido del cual se ha de calcular la siguiente ID.
							$mode (get | show): Sólo admite los valores anteriores. Dependiendo del valor, devolverá la URL (get) o la mostrará (show).
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Dependiendo del tipo de contenido y de la matriz de contenido que se le envíe a la función, ésta devolverá la ID que debería tener el siguiente elemente del mismo tipo y orden para que no reemplace a ningún otro.
		@Nota: La usa la función wp_carousel_carousel_options_page()
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_calculate_new_id($items, $type, $mode='get', $debug=false)
	{
		$will['CANCEL'] = false;
	
		if (!is_array($items))
		{
			$will['CANCEL'] = true;
		}
		
		if (!is_numeric($type))
		{
			$will['CANCEL'] = true;
		}
	
		if (!$will['CANCEL'])
		{
			$id_list_temp = array();
			
			foreach ($items as $key => $value)
			{
				
				$key_temp = explode('_', $key);

				if ($key_temp[2] == $type)
				{
					$id_list_temp[] = $value['ID'];
				}
	
			}
			
			if (count($id_list_temp) == 0)
			{
				$max_id_temp = -1;
			}
			else
			{
				$max_id_temp = max($id_list_temp);
			}
			
			//Por lógica, si la ID más alta es $max_id_temp, la siguiente ID no estará ocupada por ningún elemento
			$id_returned = $max_id_temp + 1;
			
			if (in_array($id_returned, $id_list_temp))
			{
				// Pensemos con un poco de lógica: Hay una variable en alguna función que espera que esta función le de un valor numérico de la próxima ID. Si por algún motivo resulta que esta función no tiene algún comprobador de que la nueva ID no existe ya, entonces habrá corrupción de datos y el usuario tendrá pérdidas de contenido del carrusel. Para evitar esto recurriremos a un método no muy correcto: generar un número aleatorio. Está claro que es muy probable que este número se repita, así que para ello lo multiplicaremos dos veces y le sumaremos un entero. Esperemos que así reduzcamos las posibilidades de repetición.
				$number[0] = rand($id_returned, getrandmax());
				$number[1] = rand($id_returned, getrandmax());
				$number[2] = rand($id_returned, getrandmax());
				$number[3] = rand($id_returned, getrandmax());
				// Esta simple operación matemática puede modificarse sin miedo para crear un algoritmo más eficaz a la hora de generar una ID que no se haya usado ya.
				$id_returned = ($number[0] * $number[1] * $number[2]) + $number[3];
			}
			
		}
		
		switch ($mode)
		{
			case 'get':
				return $id_returned;
				break;
			case 'show':
				echo $id_returned;
				break;
			default:
				break;
		}
		
	}
	
	/*
		@Función: wp_carousel_strleft()
		@Versión: 1.0
		@Parámetros:
							$s1: Cadena de texto principal.
							$s2: Cadena de texto a buscar a partir de la cual se recorta la variable $s1.
		@Descripción: Busca en la cadena de texto $s1 la cadena de texto $s2 y devuelve la cadena de texto $s1 hasta  el punto en el que aparece la cadena $s2.
		@Nota: La usa la función wp_carousel_create_internal_urls() y es tan simple que no tiene registro (log) ni modo debug
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_strleft($s1, $s2)
	{
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	/*
		@Función: wp_carousel_create_internal_urls()
		@Versión: 1.2
		@Parámetros:
							$type: Cadena de texto que indica el tipo de URL y el elemento al que se dirige (por ejemplo, tipo editar y elemento artículo con ID 55).
							$mode (get | show | array): Sólo admite los valores anteriores. Dependiendo del valor, devolverá la URL (get), la mostrará (show) o devolverá la matriz que contiene todos los datos intermedios (array).
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Genera la URL a cierta página, como por ejemplo a la página de edición de cierto artículo o a la página de opciones de cierto carrusel.
		@Añadida en la versión: 0.4
		@Última actualización en la versión: 0.5
	*/
	
	/*
		Valores de $type (sólo acepta un parámetro)
			
			__TYPE__:__PARAMETRO__ -> Sintaxis: TIPO:PARAMETRO. En esta lista aparecen todos los tipos y los parámetros que acepta la función. Ojo, la función sólo reconoce el primer parámetro, así que usar más es tontería.
						
			SELF_URL -> URL a la página actual (ojo, página, no archivo) - Sólo válida para el Panel de Administración
			
			REAL_SELF_URL -> URL a la página actual, eliminado parámetros de URL (indicado cuando no se trata del Panel de Administraión)
						
			POST_URL:__ID__ -> Permalink del artículo (o página) con la ID __ID__.
			
			EDIT_URL:__ID__ -> URL a la página de edición del artículo / adjunto / revisión / página con ID __ID__.
			
			THEME_FOLDER_URL -> URL a la carpeta de los themes de WP Carousel.
			
			EDIT_COSTUMIZED_CONTENT_URL:__INTERNAL_ID__ -> URL a la página de edición del contenido personalizado con ID INTERNA __INTERNAL_ID__.
			
			REMOVE_URL:__INTERNAL_ID__ -> URL a la página que elimina el contenido con ID INTERNA __INTERNAL_ID__ del carrusel
			
			UNDO_REMOVE:__SERIALIZED_BACKUP__ -> URL a la página que deshace el borrado, importa el backup __SERIALIZED_BACKUP__.
			
			DELETE_CAROUSEL:__CAROUSEL_ID__ -> URL a la página que elimina el carrusel con ID __CAROUSEL_ID__.
			
			__TYPE__:SAVE_ONLY_FIRST_URL_VARIABLE -> Mismo resultado que __TYPE__, y mantiene sólo la primera variable de URL
			
			__TYPE__:DELETE_ALL_URL_VARIABLES -> Mismo resultado que __TYPE__, sólo que elimina TODAS las variables de URL
			
	*/
	
	function wp_carousel_create_internal_urls($type, $mode='get', $debug=false)
	{
		global $wp_carousel_path;
		
		// Comprobemos que es lo que nos piden que detectemos
		$type_exploded = explode(':', $type);
		
		// Si el recuento de acciones es mayor que uno, estamos ante una acción con parámetro
		if (count($type_exploded) > 1)
		{
			// Ok, tenemos parámetros, veamos qué nos piden que hagamos
			switch ($type_exploded[1])
			{
				case 'SAVE_ONLY_FIRST_URL_VARIABLE':
					// Tenemos que guardar sólo la primera variable de URL
					$delete['URL_VARIABLES'] = true; // Esta determina si se borran o no las variables de URL A PARTIR DE LA PRIMERA (no incluída)
					$delete['FIRST_URL_VARIABLE'] = false; // Esta determina si se borra la primera variable, si es así, se borran todas
					break;
				case 'DELETE_ALL_URL_VARIABLES':
					// Tenemos que borrar todos las variables de URL
					$delete['URL_VARIABLES'] = true; // Esta determina si se borran o no las variables de URL A PARTIR DE LA PRIMERA (no incluída)
					$delete['FIRST_URL_VARIABLE'] = true; // Esta determina si se borra la primera variable, si es así, se borran todas
					break;
				default:
					break;
			}
		}
		else
		{
			// Vale, no hay parámetros, así que podemos seguir tranquilamente, pero antes démosles a las variables que controlan que los parámetros se borren el valor false (booleano), así no se borrarán los parámetros (a no ser que el tipo de acción lo requiera por defecto)
			$delete['URL_VARIABLES'] = false; // Esta determina si se borran o no las variables de URL A PARTIR DE LA PRIMERA (no incluída)
			$delete['FIRST_URL_VARIABLE'] = false; // Esta determina si se borra la primera variable, si es así, se borran todas
		}
		
		switch ($type_exploded[0])
		{
			case 'EDIT_URL':
				$url['FLOORED'] = get_edit_post_link($type_exploded[1]);
				break;
			case 'EDIT_COSTUMIZED_CONTENT_URL':
				$base_url = wp_carousel_create_internal_urls('SELF_URL:SAVE_ONLY_FIRST_URL_VARIABLE', 'get', false);
				$base_url.= '&action=EDIT:'.$type_exploded[1].'#edit_content';
				$url['FLOORED'] = $base_url;
				break;
			case 'DELETE_CAROUSEL':
				$base_url = wp_carousel_create_internal_urls('SELF_URL:DELETE_ALL_URL_VARIABLES', 'get', false);
				$base_url.= '?page=wp-carousel&action=DELETE_CAROUSEL:'.$type_exploded[1];
				$url['FLOORED'] = $base_url;
				break;
			case 'SELF_URL':
				
				$request_uri_exploded = explode('/', $_SERVER['REQUEST_URI']);
				$request_uri_exploded_cound = count($request_uri_exploded);
				
				foreach ($request_uri_exploded as $key => $value)
				{
					$new_temp_key = $request_uri_exploded_cound - $key;
					$request_uri_exploded_new[$new_temp_key] = $value;
				}
				ksort($request_uri_exploded_new);
				
				$url['COMPLETE'] = get_bloginfo('wpurl').'/wp-admin/'.$request_uri_exploded_new[1];
				$url['FLOORED'] = $url['COMPLETE'];
							
				break;
			case 'REAL_SELF_URL':
			
				$this_page_url = 'http';
				
				if (isset($_SERVER["HTTPS"]))
				{
					if ($_SERVER["HTTPS"] == "on")
					{
						$this_page_url .= "s";
					}
				}
				
				$this_page_url .= "://";
				
				if ($_SERVER["SERVER_PORT"] != "80")
				{
					$this_page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
				}
				else
				{
					$this_page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				}
								
				$url['COMPLETE'] = $this_page_url;
				$url['FLOORED'] = $this_page_url;
				
				break;
			case 'REMOVE_URL':
				$base_url = wp_carousel_create_internal_urls('SELF_URL:SAVE_ONLY_FIRST_URL_VARIABLE', 'get', false);
				$base_url.= '&action=REMOVE:'.$type_exploded[1];
				break;
			case 'UNDO_REMOVE':
				$base_url = wp_carousel_create_internal_urls('SELF_URL:SAVE_ONLY_FIRST_URL_VARIABLE', 'get', false);
				$base_url.= '&action=IMPORT:'.$type_exploded[1];
				$url['FLOORED'] = $base_url;
				break;
			case 'POST_URL':
				$url['FLOORED'] = get_permalink($type_exploded[1]);
				break;
			case 'THEME_FOLDER_URL':
				$url['FLOORED'] = get_bloginfo('wpurl').'/'.$wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/themes';
				break;
			default:
				$errors[] = 'La accion no se contempla en la lista de acciones, la accion en cuestion es: "'.$type_exploded[0].'"';
				break;
		}
		
		if (isset($delete['FIRST_URL_VARIABLE']) || isset($delete['URL_VARIABLES']))
		{
			if ($delete['FIRST_URL_VARIABLE'])
			{
				$url['FLOORED'] = explode('?', $url['COMPLETE']);
				$url['FLOORED'] = $url['FLOORED'][0];
			}
			elseif ($delete['URL_VARIABLES'])
			{
				$url['FLOORED'] = explode('&', $url['COMPLETE']);
				$url['FLOORED'] = $url['FLOORED'][0];
			}
		}
		
		$return = $url['FLOORED'];
		
		switch ($mode)
		{
			case 'get':
				return $return;
				break;
			case 'show':
				echo $return;
				break;
			case 'array':
				return $url;
				break;
			default:
				break;
		}
		
	}
	
	/*
		@Función: wp_carousel_dropdown_type_items()
		@Versión: 3.0
		@Parámetros:
							$type: ID del tipo de elemento que se quiere mostrar en la lista. No acepta contenido personalizable
							$selected: ID del elemento que debe devolverse seleccionado
							$debug (bool): Determina si al acabar de ejecutar la función se debe mostrar el registro o no.
		@Descripción: Muestra un listado de artículos en formato de menú desplegable
		@Nota: La función está basada en la que usa WP Main Menu para mostrar los artículos
		@Añadida en la versión: 0.5	
		@Actualizada en la versión: 1.0	
	*/
	
	function wp_carousel_dropdown_type_items($type, $selected = '', $debug=false)
	{
		
		switch ($type)
		{
			case '1':
				wp_dropdown_categories('name=category_id&selected='.$selected);
				break;
			case '2':
								
				$total_posts_count = 0;
				foreach (wp_count_posts() as $temp_count)
				{
					$total_posts_count += $temp_count;
				}
				
				if (WP_CAROUSEL_ITEMS_COUNT_LIMIT > $total_posts_count || WP_CAROUSEL_ITEMS_COUNT_LIMIT == '-1')
				{
					echo "<select name='category_id' id='category_id' class='postform' >";
					$posts_query = new WP_Query('showposts=-1');
					while ($posts_query->have_posts())
					{
						$posts_query->the_post();
						echo '<option value="'.get_the_ID().'"';
						if (get_the_ID() == $selected)
						{
							echo ' selected="selected"';
						}
						echo '>'.get_the_title().'</option>';
					}
					echo "</select>";
				}
				else
				{
					/*
						Mostrar los últimos N artículos en lugar del input para id
					*/
					
					if (WP_CAROUSEL_SHOW_LAST_POSTS_INSTEAD_OF_ID_INPUT && $selected == '')
					{
					
						echo "<select name='category_id' id='category_id' class='postform' >";
						$posts_query = new WP_Query('showposts='.WP_CAROUSEL_ITEMS_COUNT_LIMIT);
						while ($posts_query->have_posts())
						{
							$posts_query->the_post();
							echo '<option value="'.get_the_ID().'"';
							if (get_the_ID() == $selected)
							{
								echo ' selected="selected"';
							}
							echo '>'.get_the_title().'</option>';
						}
						echo "</select>";	
					
					}
					else
					{						
						echo '<input type="text" name="category_id" id="category_id" class="postform" value="'.$selected.'" title="'.get_the_title($selected).'" />';
					}
				}
				break;
			case '3':
				wp_dropdown_pages('name=category_id&selected='.$selected);
				break;
			case '5':
				echo "<select name='category_id' id='category_id' class='postform' >";
				$tag_list = get_tags();
				foreach ($tag_list as $key => $tag)
				{
					echo '<option value="'.$tag->term_id.'"';
					if ($tag->term_id == $selected)
					{
						echo ' selected="selected"';
					}
					echo '>'.$tag->name.'</option>';
				}
				echo "</select>";
				break;
			case '6':
				wp_dropdown_users('name=category_id&selected='.$selected);
				break;
			default:
				break;
		}
	
	}

	/*
		@Función: wp_carousel_themes_options_area()
		@Versión: 3.0
		@Parámetros:
							$id: Se corresponde con la ID del carrusel actual.
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Muestra el formulario para seleccionar themes y cambiar opciones de visualización
		@Añadida en la versión: 0.4		
		@Actualizada en la versión: 1.1
	*/
	
	function wp_carousel_themes_options_area($id, $debug=false)
	{
		
		global $wp_carousel_path;
		
		$will['CANCEL'] = false;
		
		if (!is_numeric($id))
		{
			$will['CANCEL'] = true;
		}
		
		if (!$will['CANCEL'])
		{

			$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
			$config = $config[$id];
			if (!isset($config['SHOW_ARROWS'])) $config['SHOW_ARROWS'] = '0';
			if (!isset($config['SLIDE_POSTS']) || !is_numeric($config['SLIDE_POSTS']) || $config['SLIDE_POSTS'] < 0) $config['SLIDE_POSTS'] = '1';
			if (!isset($config['ENABLE_PAGINATION'])) $config['ENABLE_PAGINATION'] = 'p';
			if (!isset($config['PAGINATION_MODE'])) $config['PAGINATION_MODE'] = 'normal';
			if (!isset($config['AUTOSLIDE_TIME']) || !is_numeric($config['AUTOSLIDE_TIME']) || $config['AUTOSLIDE_TIME'] < 0) $config['AUTOSLIDE_TIME'] = '0';
			if (!isset($config['AUTOSLIDE_POSTS']) || !is_numeric($config['AUTOSLIDE_POSTS']) || $config['AUTOSLIDE_POSTS'] < 0) $config['AUTOSLIDE_POSTS'] = '0';
			if (!isset($config['LOOP_MODE'])) $config['LOOP_MODE'] = '0';
			if (!isset($config['PANEL_WIDTH'])) $config['PANEL_WIDTH'] = '';
			if (!isset($config['PANEL_HEIGHT'])) $config['PANEL_HEIGHT'] = '';
			if (!isset($config['IMG_WIDTH'])) $config['IMG_WIDTH'] = '';
			if (!isset($config['IMG_HEIGHT'])) $config['IMG_HEIGHT'] = '';
			if (!isset($config['CAROUSEL_WIDTH'])) $config['CAROUSEL_WIDTH'] = '';
			if (!isset($config['CAROUSEL_HEIGHT'])) $config['CAROUSEL_HEIGHT'] = '';
			if (!isset($config['USE_JCAROUSEL'])) $config['USE_JCAROUSEL'] = '0';
			if (!isset($config['VERTICAL_MODE'])) $config['VERTICAL_MODE'] = '0';
		?>
		
		<a name="jump_themes"></a>
				
		<ul id="themes_carousel" class="jcarousel-skin-themes_carousel">
			<?php wp_carousel_list_themes($id); ?>
		</ul>
		
		<div id="wp_carousel_ajax_response_for_options">
				
		</div>
		
		<div class="wp_carousel_tabs wp_carousel_tabs_js">
		
			<ul>
				<li><a href="#standard_options"><?php echo _e('Standard options', 'wp_carousel'); ?></a></li>
				<li class="right"><a href="#theme_options"><?php echo _e('Current theme options', 'wp_carousel'); ?></a></li>
			</ul>
			
			<div class="clear"></div>
			
			<?php
					
				$current_theme = wp_carousel_get_theme_information($config['THEME']);
			
			?>
			
			<div id="standard_options">
			
				<form name="standard_options" method="post" id="theme_standard_options" onsubmit="return wp_carousel_update_ajax_standard_options()">
					<input name="publish" type="submit" class="button-primary right padding-top" id="publish" tabindex="5" accesskey="p" value="<?php echo _e('Save changes', 'wp_carousel'); ?>" /> 
					
						<table class="form-table th-more-width">
						<?php
						
						foreach ($current_theme['supports'] as $s_key => $s_value)
						{
							if ($s_key == 'arrows')
							{
								?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Manual slides & arrows', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top">
								<th scope="row"><label for="show_arrows"><?php _e('Show arrows for manual slide?', 'wp_carousel'); ?></label></th>
								<td><input type="checkbox" name="show_arrows" id="show_arrows" value="yes"<?php if ($config['SHOW_ARROWS'] == "1") { echo ' checked="checked"'; } ?> /></td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="slide_posts"><?php _e('Panels moved in each manual slide (0 for disable manual slides and arrows)', 'wp_carousel'); ?></label></th>
								<td>
									<input name="slide_posts" type="text" id="slide_posts" value="<?php echo $config['SLIDE_POSTS']; ?>" />
								</td>
							</tr>
								<?php else: ?>
							<tr valign="top">
								<th colspan="2"><?php _e('This theme does supports neither arrows nor manual slides ', 'wp_carousel'); ?></th>
							</tr>
								<?php endif; ?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Autoslide', 'wp_carousel'); ?></h3></th>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="autoslide_time"><?php echo _e('Time between each autoslide (0 for disable autoslides)', 'wp_carousel'); ?></label></th>
								<td>
									<input name="autoslide_time" type="text" id="autoslide_time" value="<?php echo $config['AUTOSLIDE_TIME']; ?>" />
								</td>
							</tr>
							<?php if (!$current_theme['supports']['nivo']) : ?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Loop mode', 'wp_carousel'); ?></h3></th>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="loop_mode"><?php echo _e('Enable loop mode?', 'wp_carousel'); ?></label></th>
								<td><input type="checkbox" name="loop_mode" id="loop_mode" value="yes"<?php if ($config['LOOP_MODE'] == "1") { echo ' checked="checked"'; } ?> /></td>
							<?php endif; ?>
							</tr>
								<?
							}
							
							if ($s_key == 'pagination')
							{
								?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Pagination', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top">
								<th scope="row"><label for="enable_pagination"><?php echo _e('Show pagination icons?', 'wp_carousel'); ?></label></th>
								<td><input type="checkbox" name="enable_pagination" id="enable_pagination" value="yes"<?php if ($config['ENABLE_PAGINATION'] == "1") { echo ' checked="checked"'; } ?> /></td>
							</tr>
								<?php else: ?>
							<tr valign="top">
								<th colspan="2"><?php _e('This theme does not support pagination', 'wp_carousel'); ?></th>
							</tr>
								<?php endif;
							}
							
							if ($s_key == 'vertical_mode')
							{
								?>
							<tr valign="top" class="jcarousel_feature">
								<th colspan="2"><h3><?php _e('Vertical Mode', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top" class="jcarousel_feature">
								<th scope="row"><label for="enable_vertical_mode"><?php echo _e('Enable vertical mode rather than horizontal one?', 'wp_carousel'); ?></label></th>
								<td><input type="checkbox" name="vertical_mode" id="vertical_mode" value="yes"<?php if ($config['VERTICAL_MODE'] == "1") { echo ' checked="checked"'; } ?> /></td>
							</tr>
								<?php else: ?>
							<tr valign="top" class="jcarousel_feature">
								<th colspan="2"><?php _e('This theme does not support vertical mode', 'wp_carousel'); ?></th>
							</tr>
								<?php endif;
							}
							
							if ($s_key == 'carousel_size')
							{
								?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Carousel size', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top">
								<th scope="row"><label for="carousel_width"><?php echo _e('Carousel\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="carousel_width" type="text" id="carousel_width" value="<?php echo $config['CAROUSEL_WIDTH']; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="carousel_height"><?php echo _e('Carousel\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="carousel_height" type="text" id="carousel_height" value="<?php echo $config['CAROUSEL_HEIGHT']; ?>" />
								</td>
							</tr>
								<?php else: ?>
							<tr valign="top">
								<th colspan="2"><?php _e('This theme does not support custom carousel\'s size', 'wp_carousel'); ?></th>
							</tr>
								<?php endif;
							}
							
							if ($s_key == 'panel_size')
							{
								?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Panel size', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top">
								<th scope="row"><label for="panel_width"><?php echo _e('Panel\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="panel_width" type="text" id="panel_width" value="<?php echo $config['PANEL_WIDTH']; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="panel_height"><?php echo _e('Panel\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="panel_height" type="text" id="panel_height" value="<?php echo $config['PANEL_HEIGHT']; ?>" />
								</td>
							</tr>
								<?php else: ?>
							<tr valign="top">
								<th colspan="2"><?php _e('This theme does not support custom panel\'s size', 'wp_carousel'); ?></th>
							</tr>
								<?php endif;
							}
							
							if ($s_key == 'image_size')
							{
								?>
							<tr valign="top">
								<th colspan="2"><h3><?php _e('Image size', 'wp_carousel'); ?></h3></th>
							</tr>
								<?php if ($s_value): ?>
							<tr valign="top">
								<th scope="row"><label for="img_width"><?php echo _e('Image\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="img_width" type="text" id="img_width" value="<?php echo $config['IMG_WIDTH']; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><label for="img_height"><?php echo _e('Image\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
								<td>
									<input name="img_height" type="text" id="img_height" value="<?php echo $config['IMG_HEIGHT']; ?>" />
								</td>
							</tr>
								<?php else: ?>
							<tr valign="top">
								<th colspan="2"><?php _e('This theme does not support custom image\'s size', 'wp_carousel'); ?></th>
							</tr>
								<?php endif;
							}
							
						}
						
					?>
						</table>
						<br /><br />
						<input name="publish" type="submit" class="button-primary right padding-top" id="publish" tabindex="5" accesskey="p" value="<?php echo _e('Save changes', 'wp_carousel'); ?>" /> 
						<br /><br />
				</form>
								
			</div>
			
			<div id="theme_options">
				<form name="theme_custom_options" method="post" id="theme_custom_options" onsubmit="return wp_carousel_update_ajax_theme_options()">
					<input name="publish" type="submit" class="button-primary right" id="publish" tabindex="5" accesskey="p" value="<?php echo _e('Save changes', 'wp_carousel'); ?>" />  
					
					<table class="form-table th-more-width">
					
						<?php
						
							if (!isset($current_theme['custom_settings']) || !is_array($current_theme['custom_settings']))
							{
								$current_theme['custom_settings'] = array(
									'group_noconfig' => array(
										'type' => 'group',
										'title' => __('This theme does not support custom settings', 'wp_carousel').' '
									)
								);
							}
						
							foreach ($current_theme['custom_settings'] as $setting_key => $setting_value):
								$setting_key = strtoupper($setting_key);
								if (!isset($config['THEME_SETTINGS'][$setting_key]) && $setting_value['type'] != 'group')
								{
									$config['THEME_SETTINGS'][$setting_key] = $setting_value['default_value'];
								}
						?>
						
							<?php if ($setting_value['type'] == 'group') { ?>
								<tr valign="top">
									<th colspan="2"><h3><?php _e($setting_value['title'], 'wp_carousel'); ?></h3></th>
								</tr>
							<?php } else { ?>
								<tr valign="top">
									<th scope="row"><label for="<?php echo $setting_key; ?>"><?php _e($setting_value['title'], 'wp_carousel'); ?></label></th>
									<td>
									<?php
									switch (true): 
										case ($setting_value['type'] == 'textarea'):
									?>
											<textarea name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" cols="30" rows="5"><?php echo $config['THEME_SETTINGS'][$setting_key]; ?></textarea>
									<?php
											break;
										case ($setting_value['type'] == 'checkbox'):
									?>
											<input type="checkbox" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="yes"<?php if ($config['THEME_SETTINGS'][$setting_key] == "1") { echo ' checked="checked"'; } ?> />
									<?php
											break;
										case ($setting_value['type'] == 'text'):
									?>
											<input name="<?php echo $setting_key; ?>" type="text" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
									<?php
											break;
										case ($setting_value['type'] == 'password'):
									?>
											<input name="<?php echo $setting_key; ?>" type="password" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
									<?php
											break;
										case ($setting_value['type'] == 'select'):
									?>
											<select name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>">
											<?php
												if (is_array($setting_value['values']))
												{
													foreach ($setting_value['values'] as $value_key => $value_value):
														?><option value="<?php echo $value_key; ?>"<?php if ($value_key == $config['THEME_SETTINGS'][$setting_key]) { echo ' selected="selected"'; } ?>><?php echo $value_value; ?></option> <?php
													endforeach;
												}
											?>
											</select>
									<?php
											break;
										default:
											break;
									endswitch;
									?>
									</td>
								</tr>
							<?php } ?>
						
						<?php endforeach; ?>
							
						</table>
						<br /><br />
						<input name="publish" type="submit" class="button-primary right padding-top" id="publish" tabindex="5" accesskey="p" value="<?php echo _e('Save changes', 'wp_carousel'); ?>" /> 
						<br /><br />
				</form>
			</div>
			
		</div>
		<?php
			
		}
	}
	
	/*
		@Función: wp_carousel_add_carousel_page()
		@Versión: 1.0
		@Parámetros:
							$parameters: Almacena parámetros que envía WordPress.
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Añade carruseles a la matriz principal.
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_add_carousel_page($parameters, $debug=false)
	{
		
		$page_title_temp = __('New carousel added', 'wp_carousel');
		$text_temp = __('A carousel with ID %s has been added into the DataBase. Click <a href="%s">here</a> to add another one or click <a href="%s">here</a> to go to its options page.', 'wp_carousel');
		
		$items = get_option(WP_CAROUSEL_ITEMS_TABLE);
		$items = maybe_unserialize($items);
		
		// POEDIT_ERROR: He tenido problemas al ejecutar _e('New carousel added', 'wp_carousel'); en la posición correspondiente, así que me he visto obligado a crear una variable que almacene su valor para mostrarlo más adelante.
		
		// POEDIT_ERROR: Mismo problema que arriba, pero con este código __('A carousel with ID %s has been added into the DataBase. Click <a href="%s">here</a> to add another one or click <a href="%s">here</a> to go to its options page.', 'wp_carousel')
				
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>WP Carousel - <?php echo $page_title_temp; ?></h2>
			<?php end($items); ?>
			<p><?php printf($text_temp, key($items), wp_carousel_create_internal_urls('SELF_URL'), wp_carousel_create_internal_urls('SELF_URL:DELETE_ALL_URL_VARIABLES').'?page=edit-carousel-'.key($items)); ?></p>
					
		</div>
		<div class="clear"></div>
		
		<?php
		
	}
	
	/*
		@Función: wp_carousel_list_themes()
		@Versión: 2.1
		@Parámetros:
							$id: Se corresponde con la ID del carrusel actual.
							$debug (bool): Sólo para mantener compatibilidad con código antiguo
		@Descripción: Muestra un listado de los themes disponibles para WP Carousel
		@Añadida en la versión: 0.4	
		@Actualizada en la versión: 1.1	
	*/
	
	function wp_carousel_list_themes($id, $debug=false)
	{
		global $wp_carousel_path, $wp_carousel_default_supported_supportable_features, $wp_carousel_default_unsupported_supportable_features;
		
		$will['CANCEL'] = false;
	
		$config = get_option(WP_CAROUSEL_CONFIG_TABLE);
		$config = maybe_unserialize($config);
		$config = $config[$id];
		
		if (!is_numeric($id))
		{
			$will['CANCEL'] = true;
		}
		
		if ($will['CANCEL'])
		{
			return;
		}
		else
		{
			
			$dir = '../'.$wp_carousel_path[8].'/plugins/'.$wp_carousel_path[2].'/themes';
			$url = wp_carousel_create_internal_urls('THEME_FOLDER_URL' , 'get');
			
			$themeinfo = wp_carousel_get_theme_information();
	
			if (!isset($config['THEME']))
			{
				$config['THEME'] = 'default';
			}
			
			$total_avaible_themes = count($themeinfo);
				
			$count = count($themeinfo);
			$counter = 0;
			foreach ($themeinfo as $key => $value)
			{
				?>
				<li<?php if ($key == $config['THEME']) { echo ' class="current_theme"'; } ?>>
				
					<div class="theme-info-icon-container">
						<div class="theme-<?php echo $value['name']; ?> info-icon">
							<div class="info-container">
								<?php
									echo '<h3>'.__('This theme supports:', 'wp_carousel').'</h3><ul>';
									
									if ($value['supports']['nivo']):
									
										echo '<li>Nivo Slider</li>';
									
									endif;
									
									if ($value['supports']['jcarousel'])
									{
										echo '<li>jCarousel';
																					
										if (!isset($value['supports']['vertical_mode']))
										{
											$value['supports']['vertical_mode'] = false;
										}
										
										if ($value['supports']['vertical_mode'])
										{
											_e(' (and vertical mode)', 'wp_carousel');
										}
										else
										{
											_e(' (but not vertical mode)', 'wp_carousel');
										}
										
										echo '</li>';
										
									}
									if ($value['supports']['pagination'])
									{
										echo '<li>'.__('Pagination', 'wp_carousel').'</li>';
									}
									if ($value['supports']['arrows'])
									{
										echo '<li>'.__('Arrows for manual movement', 'wp_carousel').'</li>';
									}
									if ($value['supports']['carousel_size'])
									{
										echo '<li>'.__('Custom carousel size', 'wp_carousel').'</li>';
									}
									if ($value['supports']['panel_size'])
									{
										echo '<li>'.__('Custom panel size', 'wp_carousel').'</li>';
									}
									if ($value['supports']['image_size'])
									{
										echo '<li>'.__('Custom image size', 'wp_carousel').'</li>';
									}
									echo '</ul><h3>'.__('This theme does not support:', 'wp_carousel').'</h3><ul>';
									if (!$value['supports']['jcarousel'])
									{
										echo '<li>jCarousel</li>';
									}
									if (!$value['supports']['pagination'])
									{
										echo '<li>'.__('Pagination', 'wp_carousel').'</li>';
									}
									if (!$value['supports']['arrows'])
									{
										echo '<li>'.__('Arrows for manual movement', 'wp_carousel').'</li>';
									}
									if (!$value['supports']['carousel_size'])
									{
										echo '<li>'.__('Custom carousel size', 'wp_carousel').'</li>';
									}
									if (!$value['supports']['panel_size'])
									{
										echo '<li>'.__('Custom panel size', 'wp_carousel').'</li>';
									}
									if (!$value['supports']['image_size'])
									{
										echo '<li>'.__('Custom image size', 'wp_carousel').'</li>';
									}
									
									if (isset($value['supports']['options']))
									{
										// Disabled for styling issues
										//echo '<strong>'.__('This theme has custom options', 'wp_carousel').'</strong>';
									}
																		
									echo '</ul>';
								?>
							</div>
						</div>
					</div>
				
					<h4>
						<a href="#" class="<?php echo wp_carousel_create_internal_urls('SELF_URL:SAVE_ONLY_FIRST_URL_VARIABLE').'&action=UPDATE_THEME:'.$key; ?>">
							<img src="<?php echo $url.'/'.$key.'/screenshot.png'; ?>" alt="<?php echo $value['name']; ?>" />
						</a>
						<span class="theme-name"><?php echo $value['name']; ?></span>
						<br />
						<span class="theme-author"><?php echo ' ('.__('by', 'wp_carousel').' '.$value['author'].')'; ?></span>
					</h4>

					<p class="theme-description">
						<?php echo $value['desc']; ?>
						<hr class="theme-info-separator" />
						<?php printf(__('Version %s', 'wp_carousel'), $value['version']); ?>
						<hr class="theme-info-separator" />
						<span class="action-links">
							<?php
							if ($key == $config['THEME'])
							{
								echo '<span class="theme_activated">'.__('Activated', 'wp_carousel').'</span>';
							}
							else
							{
							?>
							<?php if (!$value['supports']['jcarousel'] && !$value['supports']['nivo']) { ?>
								<?php _e('This theme is not compatible with this version of WP Carousel', 'wp_carousel'); ?>
							<?php } else { ?>
								<a href="<?php echo wp_carousel_create_internal_urls('SELF_URL:SAVE_ONLY_FIRST_URL_VARIABLE').'&action=UPDATE_THEME:'.$key; ?>#jump_themes"><?php _e('Activate', 'wp_carousel'); ?></a>
							<?php } ?>
							<?php } ?>
							 | 
							<a href="<?php echo $value['url']; ?>"><?php _e('Theme\'s site', 'wp_carousel'); ?></a>
							 | 
							<a href="<?php echo $value['author_url']; ?>"><?php _e('Author\'s site', 'wp_carousel'); ?></a>
						</span>
						<hr class="theme-info-separator" />
						<?php printf(__('All of this theme&#8217;s files are located in <code>%s</code>.', 'wp_carousel'), $dir.'/'.$key); ?>
					</p>
				
				</li>
				<?php			
			}
						
		}
		
	}
	
	/*
		@Función: wp_carousel_export_page()
		@Versión: 1.0
		@Descripción: Obtiene el código de exportación y muestra página de exportación
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_export_page()
	{
		$items = get_option(WP_CAROUSEL_ITEMS_TABLE);
		$config = get_option(WP_CAROUSEL_CONFIG_TABLE);
		$export = array('ITEMS' => $items, 'CONFIG' => $config);
		$export = serialize($export);
		$export = base64_encode($export);
		?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2>WP Carousel - <?php echo _e('Export page', 'wp_carousel'); ?></h2>
						
			<p><?php echo _e('Copy this code and paste it in a text file to make a backup. If you want to load a backup, copy this code and paste it in the import page.', 'wp_carousel'); ?></p>
			<form>
				<textarea cols="60" rows="10"><?php echo $export; ?></textarea>
			</form>
		</div>
		<div class="clear"></div>
		<?php
	}
	
	/*
		@Función: wp_carousel_backup_page()
		@Versión: 1.0
		@Descripción: Gestiona los backups de WP Carousel
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_backup_page()
	{
		global $wp_carousel_path;
		
		$items = get_option(WP_CAROUSEL_ITEMS_TABLE);
		$config = get_option(WP_CAROUSEL_CONFIG_TABLE);
		$export = array('ITEMS' => $items, 'CONFIG' => $config);
		$export = serialize($export);
		$export = base64_encode($export);
		
		$items = maybe_unserialize($items);
		$config = maybe_unserialize($config);
		$backups = maybe_unserialize(get_option(WP_CAROUSEL_BACKUP_TABLE));
				
		?>
		
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2><?php _e('Backup Manager', 'wp_carousel'); ?></h2>
			
			<a id="current_url_js" href="<?php echo $wp_carousel_path[6]; ?>restore_db.php"></a>
			<a id="current_url_get_js" href="<?php echo $wp_carousel_path[6]; ?>get_db.php"></a>
			
			<div id="ajax_backup_response">
			</div>
				
			<?php
				$total_carousels = count($items);
				$total_cols = $total_carousels + 1;
				
				$backups_per_day = array();
				
				if (is_array($backups)):
					foreach ($backups as $carousel_id => $backups_in_this_carousel)
					{
						foreach ($backups_in_this_carousel as $backup_date => $backup)
						{
							
							if (is_array($backup)):
							
							$backup_day = date('Y-m-d', $backup_date);
							$backup_time = date('H:i:s', $backup_date);
							
							$backup['date'] = $backup_date;
													
							if (!isset($backups_per_day[$backup_day]))
							{
								$backups_per_day[$backup_day] = array();
							}
							
							if (!isset($backups_per_day[$backup_day][$carousel_id]))
							{
								$backups_per_day[$backup_day][$carousel_id] = array();
							}
							
							$backups_per_day[$backup_day][$carousel_id][$backup_time] = $backup;
							
							endif;
						}
					}
				endif;
				
			?>		
			
			<table id="backup_table" cellspacing="0">
				<thead>
					<tr id="carousels_headers">
						<th>
							<form method="post" class="wp_carousel_toggle_all_form" onsumbit="return false">
								<input type="submit" name="submit" class="wp_carousel_hide_all_form_submit" value="<?php _e('Hide all','wp_carousel'); ?>"  onclick="return wp_carousel_toggle_all_backups()"/>
								<input type="submit" name="submit" class="wp_carousel_show_all_form_submit" value="<?php _e('Show all','wp_carousel'); ?>" onclick="return wp_carousel_toggle_all_backups()" />
							</form>
						</th>
					<?php if (is_array($backups)): ?>
					<?php foreach ($backups as $carousel_id => $backup): ?>
						<?php
							// Obtenemos su nombre o le asignamos uno por defecto si no tiene
							if (isset($config[$carousel_id]['CAROUSEL_NAME']))
							{
								$this_carousel_name = $config[$carousel_id]['CAROUSEL_NAME'];
							}
							else
							{
								$this_carousel_name = __('Carousel ', 'wp_carousel').$carousel_id;
							}
						?>
						<th>
							<form method="post" class="wp_carousel_<?php echo $carousel_id; ?>_toggle_items_form wp_carousel_toggle_form" onsubmit="return false">
								<input type="hidden" value="shown" name="status" class="wp_carousel_<?php echo $carousel_id; ?>_toggle_status wp_carousel_toggle_status" />
								<input type="hidden" value="<?php echo $carousel_id; ?>" name="id" />
								<input type="submit" name="submit" value="-" onclick="wp_carousel_toggle_backup_item(this.form)" class="wp_carousel_toggle_submit wp_carousel_<?php echo $carousel_id; ?>_toggle_submit wp_carousel_toggle_submit" />
							</form>
							<span class="cell_related_with_carousel_<?php echo $carousel_id; ?> cell_related_with_carousel"><?php echo $this_carousel_name; ?></span>
						</th>
					<?php endforeach; ?>
					<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						if (is_array($backups)): 
											
							$total_carousels = count($backups);
							
							ksort($backups_per_day);							
							
							foreach ($backups_per_day as $day_of_the_backup => $backups_in_this_day):
					?>
						<tr>
							<td class="date_cell"><strong><?php echo $day_of_the_backup; ?></strong></td>
							<?php
								$loop_counter = 0;
								$carousels_ids_array = array_keys($backups);
								foreach ($backups_in_this_day as $carousel_id => $backups_in_this_carousel):
									
									$last_carousel_id = end(array_keys($backups_in_this_day));
									
									$carousels_to_remove_from_array = array();
									foreach ($carousels_ids_array as $id_position => $id_value)
									{
										$carousels_to_remove_from_array[] = $id_position;
										if ($id_value != $carousel_id)
										{
											echo '<td></td>';	
										}
										else
										{
											break;
										}
									}
									
									foreach ($carousels_to_remove_from_array as $ctrfa_key => $ctrfa_value)
									{
										unset($carousels_ids_array[$ctrfa_value]);
									}
									unset($carousels_to_remove_from_array);
									
							?>
								<td>
									<table class="backups_in_this_carousel_and_day cell_related_with_carousel_<?php echo $carousel_id; ?> cell_related_with_carousel">
										<?php foreach($backups_in_this_carousel as $backup_time => $backup): ?>
											<tr><td><?php echo '<strong>'.$backup_time.'</strong> - <a href="#" class="preview_carousel" id="'.str_replace(':', '-', $backup_time).'">'.__('Preview', 'wp_carousel').'</a> | <a href="#" class="restore_carousel" id="'.$backup['date'].'_'.$carousel_id.'">'.__('Restore', 'wp_carousel').'</a>'; ?></td></tr>
										<?php endforeach; ?>
									</table>
								</td>
						<?php
								$loop_counter++;
								
								if ($last_carousel_id == $carousel_id)
								{
									$carousels_to_remove_from_array = array();
									foreach ($carousels_ids_array as $id_position => $id_value)
									{
										$carousels_to_remove_from_array[] = $id_position;
										if ($id_value != $carousel_id)
										{
											echo '<td></td>';	
										}
										else
										{
											break;
										}
									}
									
									foreach ($carousels_to_remove_from_array as $ctrfa_key => $ctrfa_value)
									{
										unset($carousels_ids_array[$ctrfa_value]);
									}
									unset($carousels_to_remove_from_array);
								}								
							
							endforeach;
						?>
						</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
						
			<?php
				foreach ($backups_per_day as $day_of_the_backup => $backups_in_this_day):
					foreach ($backups_in_this_day as $carousel_id => $backups_in_this_carousel):
						foreach($backups_in_this_carousel as $backup_time => $backup):
						
								$config = maybe_unserialize($backup['config']);
								
								// Establecemos los valores por defecto de las opciones no establecidas
								
								if (!isset($config['SHOW_ARROWS'])) $config['SHOW_ARROWS'] = '0';
								if (!isset($config['SLIDE_POSTS']) || !is_numeric($config['SLIDE_POSTS']) || $config['SLIDE_POSTS'] < 0) $config['SLIDE_POSTS'] = '1';
								if (!isset($config['ENABLE_PAGINATION'])) $config['ENABLE_PAGINATION'] = 'p';
								if (!isset($config['PAGINATION_MODE'])) $config['PAGINATION_MODE'] = 'normal';
								if (!isset($config['AUTOSLIDE_TIME']) || !is_numeric($config['AUTOSLIDE_TIME']) || $config['AUTOSLIDE_TIME'] < 0) $config['AUTOSLIDE_TIME'] = '0';
								if (!isset($config['AUTOSLIDE_POSTS']) || !is_numeric($config['AUTOSLIDE_POSTS']) || $config['AUTOSLIDE_POSTS'] < 0) $config['AUTOSLIDE_POSTS'] = '0';
								if (!isset($config['LOOP_MODE'])) $config['LOOP_MODE'] = '0';
								if (!isset($config['PANEL_WIDTH'])) $config['PANEL_WIDTH'] = '';
								if (!isset($config['PANEL_HEIGHT'])) $config['PANEL_HEIGHT'] = '';
								if (!isset($config['IMG_WIDTH'])) $config['IMG_WIDTH'] = '';
								if (!isset($config['IMG_HEIGHT'])) $config['IMG_HEIGHT'] = '';
								if (!isset($config['CAROUSEL_WIDTH'])) $config['CAROUSEL_WIDTH'] = '';
								if (!isset($config['CAROUSEL_HEIGHT'])) $config['CAROUSEL_HEIGHT'] = '';
								if (!isset($config['USE_JCAROUSEL'])) $config['USE_JCAROUSEL'] = '0';
								if (!isset($config['VERTICAL_MODE'])) $config['VERTICAL_MODE'] = '0';
			
			?>
				<div id="preview_backup_<?php echo str_replace(':', '-', $backup_time); ?>" class="preview_backup_popup">
					
					<div class="col-left"><div class="padding">
					
						<div id="items_in_carousel_backup">
							<h3><?php _e('Carousel', 'wp_carousel'); ?></h3>
							<div class="items_padder">		
								<?php
									if (is_array(maybe_unserialize($backup['items'])))
									{
										wp_carousel_carousel_show_carousel_item_list(maybe_unserialize($backup['items']), 'drag_drop');
									}
									else
									{
										echo '<p>'.__('There are no carousel\'s items in this backup', 'wp_carousel').'</p>';
									}
								?>																															
							</div>
							<hr class="fixer">
						</div>
					
					</div></div>
					<div class="col-right">
						<div class="standard_options"><div class="padding">
							<h2><?php _e('General options', 'wp_carousel'); ?></h2>
							
							<form onsubmit="return false">
								<?php
								
									$current_theme = wp_carousel_get_theme_information($config['THEME']);
								
								?>
									<table class="form-table-backup th-more-width">
									
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Current Theme', 'wp_carousel'); ?></h3></th>
										</tr>
									
										<tr valign="top">
											<th scope="row"><?php _e('Current Theme', 'wp_carousel'); ?></th>
											<td><?php _e($current_theme['name'], 'wp_carousel'); ?> (<?php echo $config['THEME']; ?>)</td>
										</tr>
									
									<?php
									
									foreach ($current_theme['supports'] as $s_key => $s_value)
									{
										if ($s_key == 'arrows')
										{
											?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Manual slides & arrows', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top">
											<th scope="row"><label for="show_arrows"><?php _e('Show arrows for manual slide?', 'wp_carousel'); ?></label></th>
											<td><input type="checkbox" name="show_arrows" id="show_arrows" value="yes"<?php if ($config['SHOW_ARROWS'] == "1") { echo ' checked="checked"'; } ?> /></td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="slide_posts"><?php _e('Panels moved in each manual slide (0 for disable manual slides and arrows)', 'wp_carousel'); ?></label></th>
											<td>
												<input name="slide_posts" type="text" id="slide_posts" value="<?php echo $config['SLIDE_POSTS']; ?>" />
											</td>
										</tr>
											<?php else: ?>
										<tr valign="top">
											<th colspan="2"><?php _e('This theme does supports neither arrows nor manual slides ', 'wp_carousel'); ?></th>
										</tr>
											<?php endif; ?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Autoslide', 'wp_carousel'); ?></h3></th>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="autoslide_time"><?php echo _e('Time between each autoslide (0 for disable autoslides)', 'wp_carousel'); ?></label></th>
											<td>
												<input name="autoslide_time" type="text" id="autoslide_time" value="<?php echo $config['AUTOSLIDE_TIME']; ?>" />
											</td>
										</tr>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Loop mode', 'wp_carousel'); ?></h3></th>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="loop_mode"><?php echo _e('Enable loop mode?', 'wp_carousel'); ?></label></th>
											<td><input type="checkbox" name="loop_mode" id="loop_mode" value="yes"<?php if ($config['LOOP_MODE'] == "1") { echo ' checked="checked"'; } ?> /></td>
										</tr>
											<?
										}
										
										if ($s_key == 'pagination')
										{
											?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Pagination', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top">
											<th scope="row"><label for="enable_pagination"><?php echo _e('Show pagination icons?', 'wp_carousel'); ?></label></th>
											<td><input type="checkbox" name="enable_pagination" id="enable_pagination" value="yes"<?php if ($config['ENABLE_PAGINATION'] == "1") { echo ' checked="checked"'; } ?> /></td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="pagination_mode"><?php echo _e('Pagination mode:', 'wp_carousel'); ?></label></th>
											<td>
												<select name="pagination_mode" id="pagination_mode">
													<option value="normal"<?php if ($config['PAGINATION_MODE'] == 'normal') echo ' selected="selected"'; ?>><?php _e('Slide one item', 'wp_carousel'); ?></option>
													<option value="group"<?php if ($config['PAGINATION_MODE'] == 'group') echo ' selected="selected"'; ?>><?php _e('Slide one group of items (only jCarousel)', 'wp_carousel'); ?></option>
												</select>
											</td>
										</tr>
											<?php else: ?>
										<tr valign="top">
											<th colspan="2"><?php _e('This theme does not support pagination', 'wp_carousel'); ?></th>
										</tr>
											<?php endif;
										}
										
										if ($s_key == 'vertical_mode')
										{
											?>
										<tr valign="top" class="jcarousel_feature">
											<th colspan="2"><h3><?php _e('Vertical Mode', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top" class="jcarousel_feature">
											<th scope="row"><label for="enable_vertical_mode"><?php echo _e('Enable vertical mode rather than horizontal one?', 'wp_carousel'); ?></label></th>
											<td><input type="checkbox" name="vertical_mode" id="vertical_mode" value="yes"<?php if ($config['VERTICAL_MODE'] == "1") { echo ' checked="checked"'; } ?> /></td>
										</tr>
											<?php else: ?>
										<tr valign="top" class="jcarousel_feature">
											<th colspan="2"><?php _e('This theme does not support vertical mode', 'wp_carousel'); ?></th>
										</tr>
											<?php endif;
										}
										
										if ($s_key == 'carousel_size')
										{
											?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Carousel size', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top">
											<th scope="row"><label for="carousel_width"><?php echo _e('Carousel\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="carousel_width" type="text" id="carousel_width" value="<?php echo $config['CAROUSEL_WIDTH']; ?>" />
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="carousel_height"><?php echo _e('Carousel\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="carousel_height" type="text" id="carousel_height" value="<?php echo $config['CAROUSEL_HEIGHT']; ?>" />
											</td>
										</tr>
											<?php else: ?>
										<tr valign="top">
											<th colspan="2"><?php _e('This theme does not support custom carousel\'s size', 'wp_carousel'); ?></th>
										</tr>
											<?php endif;
										}
										
										if ($s_key == 'panel_size')
										{
											?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Panel size', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top">
											<th scope="row"><label for="panel_width"><?php echo _e('Panel\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="panel_width" type="text" id="panel_width" value="<?php echo $config['PANEL_WIDTH']; ?>" />
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="panel_height"><?php echo _e('Panel\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="panel_height" type="text" id="panel_height" value="<?php echo $config['PANEL_HEIGHT']; ?>" />
											</td>
										</tr>
											<?php else: ?>
										<tr valign="top">
											<th colspan="2"><?php _e('This theme does not support custom panel\'s size', 'wp_carousel'); ?></th>
										</tr>
											<?php endif;
										}
										
										if ($s_key == 'image_size')
										{
											?>
										<tr valign="top">
											<th colspan="2"><h3><?php _e('Image size', 'wp_carousel'); ?></h3></th>
										</tr>
											<?php if ($s_value): ?>
										<tr valign="top">
											<th scope="row"><label for="img_width"><?php echo _e('Image\'s <strong>Width</strong> (Width and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="img_width" type="text" id="img_width" value="<?php echo $config['IMG_WIDTH']; ?>" />
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><label for="img_height"><?php echo _e('Image\'s <strong>Height</strong> (Height and Unit - Note that some units might not work in some themes):', 'wp_carousel'); ?></label></th>
											<td>
												<input name="img_height" type="text" id="img_height" value="<?php echo $config['IMG_HEIGHT']; ?>" />
											</td>
										</tr>
											<?php else: ?>
										<tr valign="top">
											<th colspan="2"><?php _e('This theme does not support custom image\'s size', 'wp_carousel'); ?></th>
										</tr>
											<?php endif;
										}
										
									}
									
								?>
									</table>
							</form>
							
						</div></div>
						<div class="theme_options"><div class="padding">
							<h2><?php _e('Theme\'s options', 'wp_carousel'); ?></h2>
							
							<?php
							
								if (!isset($current_theme['custom_settings']))
								{
									$current_theme['custom_settings'] = '';
								}
							
								if (is_array($current_theme['custom_settings'])):
							?>
							
							<form onsubmit="return false">
															
								<table class="form-table-backup th-more-width">
								
									<?php
										foreach ($current_theme['custom_settings'] as $setting_key => $setting_value):
											$setting_key = strtoupper($setting_key);
											if (!isset($config['THEME_SETTINGS'][$setting_key]) && $setting_value['type'] != 'group')
											{
												$config['THEME_SETTINGS'][$setting_key] = $setting_value['default_value'];
											}
									?>
									
										<?php if ($setting_value['type'] == 'group') { ?>
											<tr valign="top">
												<th colspan="2"><h3><?php _e($setting_value['title'], 'wp_carousel'); ?></h3></th>
											</tr>
										<?php } else { ?>
											<tr valign="top">
												<th scope="row"><label for="<?php echo $setting_key; ?>"><?php _e($setting_value['title'], 'wp_carousel'); ?></label></th>
												<td>
												<?php
												switch (true): 
													case ($setting_value['type'] == 'textarea'):
												?>
														<textarea name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" cols="30" rows="5"><?php echo $config['THEME_SETTINGS'][$setting_key]; ?></textarea>
												<?php
														break;
													case ($setting_value['type'] == 'checkbox'):
												?>
														<input type="checkbox" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="yes"<?php if ($config['THEME_SETTINGS'][$setting_key] == "1") { echo ' checked="checked"'; } ?> />
												<?php
														break;
													case ($setting_value['type'] == 'text'):
												?>
														<input name="<?php echo $setting_key; ?>" type="text" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
												<?php
														break;
													case ($setting_value['type'] == 'password'):
												?>
														<input name="<?php echo $setting_key; ?>" type="password" id="<?php echo $setting_key; ?>" value="<?php echo $config['THEME_SETTINGS'][$setting_key]; ?>" />
												<?php
														break;
													case ($setting_value['type'] == 'select'):
												?>
														<select name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>">
														<?php
															if (is_array($setting_value['values']))
															{
																foreach ($setting_value['values'] as $value_key => $value_value):
																	?><option value="<?php echo $value_key; ?>"<?php if ($value_key == $config['THEME_SETTINGS'][$setting_key]) { echo ' selected="selected"'; } ?>><?php echo $value_value; ?></option> <?php
																endforeach;
															}
														?>
														</select>
												<?php
														break;
													default:
														break;
												endswitch;
												?>
												</td>
											</tr>
										<?php } ?>
									
									<?php endforeach; ?>
										
									</table>
							</form>
							
							<?php 
								else:
								_e('This theme does not support custom settings', 'wp_carousel');
								endif;
							?>
							
						</div></div>
					</div>
					
				</div>
			<?php
						endforeach;
					endforeach;
				endforeach;
			?>
						
			<div id="overlay_wp_carousel_popup"></div>
			
			<p><a href="#" id="wp_carousel_show_export_code"><?php _e('Show export code', 'wp_carousel'); ?></a></p>
			
			<div id="wp_carousel_export_code_popup">
				<p><?php _e('This code is and export code. You should copy and paste this code in a text file (and save it too) when you are going to make a modification to the plugin which can cause problems to the DataBase. If the Backup Manager fails, this code will allow you to restore all your carousels in the Safe mode Import page', 'wp_carousel'); ?></p>
				<p>
				<form>
					<textarea cols="60" rows="10"><?php echo $export; ?></textarea>
				</form>
				</p>
			</div>
			
			<div id="wp_carousel_ajax_loader">
				<div>
					<img src="<?php echo $wp_carousel_path[6]; ?>img/ajax-loader.gif" align="<?php _e('Saving changes', ' wp_carousel'); ?>" title="<?php _e('Saving changes, please, wait a moment', ' wp_carousel'); ?>" />
				</div>
			</div>
			
		</div>
		<div class="clear"></div>
		<?php
	}
	
	/*
		@Función: wp_carousel_import_page()
		@Versión: 1.1
		@Descripción: Pide el código de exportación y carga el backup.
		@Añadida en la versión: 0.4	
		@Actualizada en la versión: 1.0	
	*/
	
	function wp_carousel_import_page()
	{
		$bad_backup = false;
		if (isset($_POST['import']))
		{
			if ($_POST['import'] != '')
			{
				$array = $_POST['import'];
				$array = base64_decode($array);
				$array = maybe_unserialize($array);
				$items = $array['ITEMS'];
				$config = $array['CONFIG'];
				if (!is_array(maybe_unserialize($items)))
				{
					$bad_backup = true;
				}
				if (!is_array(maybe_unserialize($config)))
				{
					$bad_backup = true;
				}
				if (!$bad_backup)
				{
					update_option(WP_CAROUSEL_ITEMS_TABLE, $items);
					update_option(WP_CAROUSEL_CONFIG_TABLE, $config);
				}
			}
		}
		?>		
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2><?php echo _e('Safe mode Import page', 'wp_carousel'); ?></h2>
					
			<p><?php printf(__('The Backup Manager is a revolutionary way to manage the backups, but as this is a new feature, it may have bugs. If the Backup Manager can\'t start, you can use an export code, which you must have copied in a previous moment, to restore all your carousels. This process will erase all the backups, but it can restore the plugin to an stable status. Use it only if you can\'t use the Backup Manager. If you found an error in the Backup Manager, please, report it in the <a href="%s">forums</a> as soon as possible.', 'wp_carousel'), 'http://foro.sumolari.com'); ?></p>
			<?php if ($bad_backup) { echo '<p>'.__('That code is not a valid WP Carousel backup code', 'wp_carousel').'</p>'; } ?>
			<form action="<?php echo wp_carousel_create_internal_urls('SELF_URL', 'get'); ?>" method="post" name="import-form" id="import-form">
				<textarea cols="60" rows="10" name="import" id="import"></textarea>
				<br />
				<input type="submit" name="submit" id="submit" class="button primary-button" value="<?php echo _e('Import', 'wp_carousel'); ?>" />
			</form>
		</div>
		<div class="clear"></div>
		<?php
	}
	
	/*
		@Función: wp_carousel_uninstall_page()
		@Versión: 1.0
		@Descripción: Muestra la página para desinstalar el carrusel
		@Añadida en la versión: 0.4		
	*/
	
	function wp_carousel_uninstall_page()
	{
		?>		
		<div class="wrap">
			<div id="icon-options-general" class="icon32"><br></div>
			<h2><?php _e('Delete WP Carousel\'s backup data', 'wp_carousel'); ?></h2>
			<p><?php _e('Sometimes WP Carousel\'s backup table can be more big than it should be. If you are having issues with Backup Manager, deleting backups stored at Database could fix the problem. This action will delete only backups.', 'wp_carousel'); ?></p>
			<p><?php printf(__('Click <a href="%s">here</a> to delete backups stored in Database.', 'wp_carousel'), wp_carousel_create_internal_urls('SELF_URL:DELETE_ALL_URL_VARIABLES').'?page=wp-carousel&action=DELETEBACKUPS'); ?></p>
			
			<div id="icon-options-general" class="icon32"><br></div>
			<h2><?php _e('Uninstall WP Carousel', 'wp_carousel'); ?></h2>
			<p><?php _e('Do you want to uninstall WP Carousel? Has WP Carousel done something in the wrong way? This will delete all the content in the Database that was added by WP Carousel. It can\'t be undone. Are you sure?', 'wp_carousel'); ?></p>
			<p><?php printf(__('Click <a href="%s">here</a> to uninstall WP Carousel and delete all data created by it.', 'wp_carousel'), wp_carousel_create_internal_urls('SELF_URL:DELETE_ALL_URL_VARIABLES').'?page=wp-carousel&action=UNINSTALL'); ?></p>
		</div>
		<div class="clear"></div>
		<?php
	}	
	
	/*
		@Clase: WP_Carousel_Widget
		@Versión: 2.0
		@Descripción: Se encarga del plugin de la sidebar de WP Carousel
		@Añadida en la versión: 0.3
		@Actualizada en la versión: 1.0
	*/
	
	class WP_Carousel_Widget extends WP_Widget
	{
	
		function WP_Carousel_Widget()
		{
			parent::WP_Widget(false, $name = 'WP Carousel');	
		}
	
		function widget($args, $instance)
		{		
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			echo $before_widget;
			if ($title)
			{
				echo $before_title . $title . $after_title;
			}
			wp_carousel($instance['id']);
			echo $after_widget;
		}
	
		function update($new_instance, $old_instance)
		{				
			return $new_instance;
		}
	
		function form($instance)
		{
			if (isset($instance['id']))
			{
				$id = esc_attr($instance['id']);
			}
			else
			{
				$id = '';
			}
			if (isset($instance['title']))
			{
				$title = esc_attr($instance['title']);
			}
			else
			{
				$title = '';
			}
			?>
				<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo _e('Widget Title', 'wp_carousel'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
				<p><label for="<?php echo $this->get_field_id('id'); ?>"><?php echo _e('Carousel', 'wp_carousel'); ?></label></p>
				<select name="<?php echo $this->get_field_name('id'); ?>" id="<?php echo $this->get_field_id('id'); ?>" class="widefat">
					<?php
						foreach(maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE)) as $carousel_id => $carousel_config):

							if (isset($carousel_config['CAROUSEL_NAME']))
							{
								$carousel_name = $carousel_config['CAROUSEL_NAME'];
							}
							else
							{
								$carousel_name = __('Carousel ', 'wp_carousel').$carousel_id;
							}
							
					?>
						<option id="<?php echo $carousel_id; ?>" value="<?php echo $carousel_id; ?>"<?php if ($id == $carousel_id) echo ' selected="selected"'; ?>><?php echo $carousel_name; ?></option>
					<?php endforeach; ?>
				</select>
			<?php 
		}

	}
	
	/*
		@Función: wp_carousel_shortcode_filter()
		@Versión: 1.0
		@Parámetros:
							$atts: Atributos opcionales del Shortcode, el único que ahora mismo es el atributo theme, que permite forzar un theme diferente al activo en el carrusel actual, aunque si no hay ningún carrusel que tenga activado ese theme, no se cargará el CSS ni el JS del theme en cuestión, por tanto los resultados pueden variar. No recomiendo el uso de este atributo a no ser que se sepa lo que se está haciendo.
							$id: Contenido que será filtrado y devuelto tras las modificaciones pertinentes.
		@Descripción: Filtra el contenido enviado en la variable $content en busca del Shortcode [wp_carousel theme="NOMBRE DE LA CARPETA CONTENEDORA DEL THEME"]ID[/wp_carousel] para reemplazarlo por el carrusel correspondiente aplicando el theme indicado
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_shortcode_filter($atts, $id)
	{
		extract(shortcode_atts(array('theme' => false), $atts));
		
		return wp_carousel($id, 'shortcode', $theme);		
	}
	
	// Añadimos el Shortcode a WordPress
	
	add_shortcode('wp_carousel', 'wp_carousel_shortcode_filter');
	
	/*
		@Función: wp_carousel_add_wp_carousel_shortcode_button()
		@Versión: 1.0
		@Descripción: Añade las funciones para mostrar el botón para añadir un carrusel en el editor de WordPress (en modo visual)
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_add_wp_carousel_shortcode_button()
	{
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
		{
			return;
		}
		
		if (get_user_option('rich_editing') == 'true')
		{
			add_filter('mce_external_plugins', 'wp_carousel_add_wp_carousel_shortcode_tinymce_plugin');
			add_filter('mce_buttons', 'wp_carousel_register_wp_carousel_shortcode_button');
		}
	}
	
	add_action('init', 'wp_carousel_add_wp_carousel_shortcode_button');
	
	/*
		@Función: wp_carousel_register_wp_carousel_shortcode_button()
		@Versión: 1.0
		@Parámetros:
							$buttons: Botones anteriores al del Shortcode de WP Carousel
		@Descripción: Añade el botón del Shortcode de WP Carousel a la lista de botones
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_register_wp_carousel_shortcode_button($buttons)
	{
		array_push($buttons, "|", "wp_carousel");
		return $buttons;
	}
	
	/*
		@Función: wp_carousel_add_wp_carousel_shortcode_tinymce_plugin()
		@Versión: 1.0
		@Parámetros:
							$plugin_array: Lista de plugins del editor TinyMCE
		@Descripción: Añade el código Javascript del botón de WP Carousel a la lista de plugins del editor TinyMCE de WordPress
		@Añadida en la versión: 1.0	
	*/

	function wp_carousel_add_wp_carousel_shortcode_tinymce_plugin($plugin_array)
	{
		global $wp_carousel_path;
		$plugin_array['wp_carousel'] = $wp_carousel_path[6].'js/editor_plugin.js';
		return $plugin_array;
	}
	
	/*
		@Función: wp_carousel_refresh_mce()
		@Versión: 1.0
		@Parámetros:
							$ver: Versión actual del editor TinyMCE
		@Descripción: Aumenta la versión de TinyMCE para evitar problemas de cache
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_refresh_mce($ver)
	{
		$ver += 3;
		return $ver;
	}

	add_filter('tiny_mce_version', 'wp_carousel_refresh_mce');
	
	/*
		@Función: wp_carousel_mark_that_is_in_loop()
		@Versión: 1.0
		@Descripción: Establece el valor de la variable de sesión correspondiente para indicar que está actualmente en el loop de WordPress
		@Nota: Esta función sólo existe porque no conozco ninguna forma de detectar si se está en el loop de WordPress o no. Seguramente haya alguna manera, así que si la descubres, te agradecería mucho que me la explicases, para así eliminar el código innecesario
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_mark_that_is_in_loop()
	{
		$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_IS_IN_LOOP_INDICATOR] = true;
	}
	
	add_action('loop_start', 'wp_carousel_mark_that_is_in_loop');
	
	/*
		@Función: wp_carousel_mark_that_is_not_in_loop()
		@Versión: 1.0
		@Descripción: Establece el valor de la variable de sesión correspondiente para indicar que NO está actualmente en el loop de WordPress
		@Nota: Esta función sólo existe porque no conozco ninguna forma de detectar si se está en el loop de WordPress o no. Seguramente haya alguna manera, así que si la descubres, te agradecería mucho que me la explicases, para así eliminar el código innecesario
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_mark_that_is_not_in_loop()
	{
		$_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_IS_IN_LOOP_INDICATOR] = false;
	}
	
	add_action('loop_end', 'wp_carousel_mark_that_is_not_in_loop');
	
	/*
		@Función: wp_carousel_is_in_loop()
		@Versión: 1.0
		@Descripción: Devuelve true si se ejecuta la función en el loop de WordPress o false en caso contrario
		@Nota: Esta función sólo existe porque no conozco ninguna forma de detectar si se está en el loop de WordPress o no. Seguramente haya alguna manera, así que si la descubres o ya la conoces, te agradecería mucho que me la explicases, para así eliminar el código innecesario
		@Añadida en la versión: 1.0	
	*/
	
	function wp_carousel_is_in_loop()
	{
		return $_SESSION[WP_CAROUSEL_SESSION_VARIABLE_NAME_FOR_IS_IN_LOOP_INDICATOR];
	}
	
?>