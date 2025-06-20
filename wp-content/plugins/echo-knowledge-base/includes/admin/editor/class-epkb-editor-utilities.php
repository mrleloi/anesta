<?php

/**
 * Various utility functions for editor 
 *
 * @copyright   Copyright (C) 2020, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Editor_Utilities {

	/**
	 * Get visual Editor URLs
	 *
	 * @param $kb_config
	 * @param string $main_page_zone_name
	 * @param string $article_page_zone_name
	 * @param string $archive_page_zone_name
	 * @param bool $use_backend_mode
	 * @return array
	 */
	public static function get_editor_urls( $kb_config, $main_page_zone_name='', $article_page_zone_name='', $archive_page_zone_name='', $use_backend_mode = true, $preopen_setting = '' ) {

		if ( $use_backend_mode && EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ) {
			$url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#settings__editor' );

			return [
				'main_page_url' => $url,
				'article_page_url' => $url,
				'archive_url' => $url,
				'search_page_url' => $url
			];
		}

		$params = array( 'action' => 'epkb_load_editor' );

		if ( ! empty( $preopen_setting ) ) {
			$params['preopen_setting'] = $preopen_setting;
		}

		$main_page_zone_name = EPKB_Core_Utilities::is_run_setup_wizard_first_time() ? 'templates' : $main_page_zone_name;

		$first_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
		$main_url = empty( $first_main_page_url ) ? '' : add_query_arg( $params + ( empty( $main_page_zone_name ) ? [] : array( 'preopen_zone' => $main_page_zone_name ) ), $first_main_page_url );

		$article_url = EPKB_KB_Handler::get_first_kb_article_url( $kb_config );
		$article_url = empty( $article_url ) ? '' : add_query_arg(  $params + ( empty( $article_page_zone_name ) ? [] : array( 'preopen_zone' => $article_page_zone_name ) ), $article_url );

		$archive_url = EPKB_KB_Handler::get_kb_category_with_most_articles_url( $kb_config );
		$archive_url = empty( $archive_url ) ? '' : add_query_arg(  $params + ( empty( $archive_page_zone_name ) ? [] : array( 'preopen_zone' => $archive_page_zone_name ) ), $archive_url );

		$search_url = '';
		if ( EPKB_Utilities::is_advanced_search_enabled() ) {

			// get search query: first title letter from first article
			$posts = get_posts( array(
				'numberposts' => 1,
				'post_type'   => EPKB_KB_Handler::get_post_type( $kb_config['id'] ),
			) );

			// provide Editor url for Search page only if we can find KB Main Page and articles
			if ( ! empty( $posts ) && ! empty( $first_main_page_url ) ) {
				$search_query = substr( $posts[0]->post_title, 0, 1 );

				$search_query_param = apply_filters( 'eckb_search_query_param', '', $kb_config['id'] );

				/* TODO: remove in May 2023, bypass for ASEA */
				if ( empty( $search_query_param ) ) {
					$search_query_param = _x( 'kb-search', 'search query parameter in URL', 'echo-advanced-search' );
				}
				/** END remove */

				$search_url = add_query_arg( array( 'preopen_zone' => 'search_zone', 'action' => 'epkb_load_editor', $search_query_param => $search_query ), $first_main_page_url );
			}
		}

		return [
			'main_page_url' => $main_url,
			'article_page_url' => $article_url,
			'archive_url' => $archive_url,
			'search_page_url' => $search_url ];
	}

	/**
	 * Return visual Editor URL for given page type
	 * @param $page_type
	 * @param $zone_name
	 * @return array|mixed|string
	 */
	public static function get_one_editor_url( $page_type, $zone_name='' ) {
		$kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();

		$url = '';
		if ( $page_type == 'main_page' ) {
			$url = self::get_editor_urls( $kb_config, $zone_name );
			$url = $url['main_page_url'];

		} else if ( $page_type == 'article_page' ) {
			$url = self::get_editor_urls( $kb_config, '', $zone_name );
			$url = $url['article_page_url'];

		} else if ( $page_type == 'archive_page' ) {
			$url = self::get_editor_urls( $kb_config, '', '', $zone_name );
			$url = $url['archive_url'];
		} else if ( $page_type == 'search_page' ) {
			$url = self::get_editor_urls( $kb_config );
			$url = $url['search_page_url'];
		}

		return $url;
	}

	/**
	 * Determine what page are we editing in the visual Editor
	 * @return string
	 */
	public static function epkb_front_end_editor_type() {
		global $eckb_kb_id, $post;
		
		$editor_type = '';
		
		if ( is_archive() ) {
			// show Editor link except on Category Archive Page without any article
			$editor_type = empty($post) ? '' : 'archive-page';
			
		} else if ( ! empty($post) && $post->post_type == 'page' ) {
			$editor_type = 'main-page';
			
		} else if ( ! empty($post) && EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			$editor_type = 'article-page';
		}

		if ( $editor_type == 'main-page' && EPKB_Utilities::is_advanced_search_enabled() ) {

			$search_query_param = apply_filters( 'eckb_search_query_param', '', $eckb_kb_id );

			/* TODO: remove in May 2023, bypass for ASEA */
			if ( empty( $search_query_param ) ) {
				$search_query_param = _x( 'kb-search', 'search query parameter in URL', 'echo-advanced-search' );
			}
			/** END remove */

			 if ( EPKB_Utilities::get( $search_query_param ) ) {
				$editor_type = 'search-page';
			}
		}
		return $editor_type;
	}

	/**
	 * When reloading page after making changes in the Editor, populate KB config with the new values while rendering the page
	 * @param $kb_config
	 * @return array
	 */
	public static function update_kb_from_editor_config( $kb_config ) {

		// do not make any changes to config unless Editor is active
		if ( empty( $kb_config['id'] ) || empty( $_REQUEST['epkb-editor-page-loaded'] ) || empty( $_REQUEST['epkb-editor-settings'] ) || ( isset($_REQUEST['epkb-editor-kb-id']) && $kb_config['id'] != $_REQUEST['epkb-editor-kb-id'] ) ) {
			return $kb_config;
		}

		$orig_config = $kb_config;

		$new_kb_config = json_decode(stripcslashes($_REQUEST['epkb-editor-settings'] ), true);
		if ( empty( $new_kb_config ) || ! is_array($new_kb_config) ) {
			return $kb_config;
		}

		// if user selected preset then populate the config from the preset
		if ( ! empty( $_REQUEST['epkb-editor-preset'] ) ) {

			$preset_fields = EPKB_KB_Wizard_Themes::get_theme_fields();
			$preset_values = json_decode(stripcslashes($_REQUEST['epkb-editor-preset'] ), true);
			foreach ( $preset_fields as $field_name ) {
				if ( ! isset( $preset_values[$field_name] ) || $preset_values[$field_name] === '' ) {
					continue;
				}

				$kb_config[$field_name] = $preset_values[$field_name];
			}
		}

		// populate kb config from Editor settings
		foreach ( $new_kb_config as $zone_name => $zone ) {
			foreach ( $zone['settings'] as $field_name => $field ) {

				if ( ! isset( $field['value'] ) ) {
					continue;
				}

				// handle sidebar components priority differently TODO FUTURE remove if live preview instead of reload for sidebar config changes
				if ( EPKB_Editor_Config_Base::is_sidebar_priority( $field_name ) ) {
					$kb_config['article_sidebar_component_priority'][$field_name] = $field['value'];
				} else {
					$kb_config[$field_name] = $field['value'];
				}
			}
		}

		// update layouts if it changed
		if ( ! empty( $kb_config['kb_main_page_layout'] ) && ! empty( $orig_config['kb_main_page_layout'] ) && $orig_config['kb_main_page_layout'] != $kb_config['kb_main_page_layout'] ) {
			$new_kb_config = EPKB_Editor_Controller::reset_layout( $orig_config, $kb_config );
			$kb_config = is_array($new_kb_config) ? $new_kb_config : $kb_config;
		}

		return $kb_config;
	}

	/**
	 * if the Editor is active then use its configuration changes to update the current configuration
	 * @param $config
	 * @return mixed
	 */
	public static function update_from_editor_config( $config ) {

		// do not make any changes to the configuration unless Editor is active
		if ( empty( $_REQUEST['epkb-editor-page-loaded'] ) || empty( $_REQUEST['epkb-editor-settings'] ) ) {
			return $config;
		}

		$new_config = json_decode(stripcslashes($_REQUEST['epkb-editor-settings'] ), true);
		if ( empty( $new_config ) || ! is_array($new_config) ) {
			return $config;
		}

		// use Editor configuration to update current configuration
		foreach ( $new_config as $zone_name => $zone ) {
			foreach ( $zone['settings'] as $field_name => $field ) {
				if ( ! isset( $config[$field_name] ) ) {
					continue;
				}

				$config[$field_name] = $field['value'];
			}
		}

		return $config;
	}

	public static function get_editor_settings( $editor_page_type ) {
		$editor_config = false;

		// handle KB editor pages
		switch ( $editor_page_type ) {
			case 'main-page':
				$editor_config = new EPKB_Editor_Main_Page_Config();
				break;

			case 'article-page':
				$editor_config = new EPKB_Editor_Article_Page_Config();
				break;

			case 'archive-page':
				$editor_config = new EPKB_Editor_Archive_Page_Config();
				break;

			case 'search-page':
				$editor_config = new EPKB_Editor_Search_Page_Config();
				break;
		}

		// ensure everything is loaded correctly
		if ( empty($editor_config) || ! $editor_config->is_initialized() ) {
			return [];
		}

		return $editor_config->setting_zones;
	}
}