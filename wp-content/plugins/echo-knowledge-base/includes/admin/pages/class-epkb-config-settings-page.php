<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration menu and pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Config_Settings_Page {

	private $kb_config;
	private $kb_config_specs;
	private $elay_enabled;
	private $asea_enabled;
	private $eprf_enabled;
	private $widg_enabled;
	private $kblk_enabled;
	private $is_basic_layout;
	private $is_tabs_layout;
	private $is_categories_layout;
	private $is_classic_layout;
	private $is_drill_down_layout;
	private $is_grid_layout;
	private $is_sidebar_layout;
	private $is_elay_layout;
	private $is_kb_templates;
	private $is_modular_main_page;
	private $is_old_elay;   // FUTURE TODO: remove in December 2024

	public function __construct( $kb_config ) {

		$this->kb_config = apply_filters( 'eckb_kb_config', $kb_config );

		// in case user still has old Elegant Layouts we need to have at least defaults for newly added config


		$this->kb_config_specs = EPKB_Core_Utilities::retrieve_all_kb_specs( $this->kb_config['id'] );

		$this->elay_enabled = EPKB_Utilities::is_elegant_layouts_enabled();
		$this->asea_enabled = EPKB_Utilities::is_advanced_search_enabled();
		$this->eprf_enabled = EPKB_Utilities::is_article_rating_enabled();
		$this->widg_enabled = EPKB_Utilities::is_kb_widgets_enabled();
		$this->kblk_enabled = EPKB_Utilities::is_link_editor_enabled();

		$this->is_basic_layout = EPKB_Layout::BASIC_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_tabs_layout = EPKB_Layout::TABS_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_categories_layout = EPKB_Layout::CATEGORIES_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_classic_layout = EPKB_Layout::CLASSIC_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_drill_down_layout = EPKB_Layout::DRILL_DOWN_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_grid_layout = EPKB_Layout::GRID_LAYOUT == $this->kb_config['kb_main_page_layout'];
		$this->is_elay_layout = EPKB_Layouts_Setup::is_elay_layout( $this->kb_config['kb_main_page_layout'] );
		$this->is_sidebar_layout = EPKB_Layout::SIDEBAR_LAYOUT == $this->kb_config['kb_main_page_layout'];

		$this->is_kb_templates = $this->kb_config['templates_for_kb'] == 'kb_templates';

		$this->is_modular_main_page = $this->kb_config['modular_main_page_toggle'] == 'on';

		$this->is_old_elay = $this->elay_enabled && class_exists( 'Echo_Elegant_Layouts' ) && version_compare( Echo_Elegant_Layouts::$version, '2.14.1', '<=' );
	}

	/**
	 * Return configuration array of vertical Tabs for Settings top-level tab
	 *
	 * @return array
	 */
	public function get_vertical_tabs_config() {

		$contents_configs = $this->get_contents_configs();
		$sub_contents_configs = $this->get_sub_contents_configs();
		$helpful_info_box_configs = $this->get_helpful_info_box_config();
		$access_to_get_started = EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_need_help_read' ) || EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' );

		$tabs_config = [];

		// Get Started
		if ( $access_to_get_started ) {
			$tabs_config['get-started'] = array(
				'title'     => __( 'Get Started', 'echo-knowledge-base' ),
				'icon'      => 'epkbfa epkbfa-rocket',
				'key'       => 'about-kb',
				'active'    => true,
				'contents'  => array(
					array(
						'title'             => __( 'Quick Links', 'echo-knowledge-base' ),
						'body_html'         => $this->get_quick_links_box(),
					),
					array(
						'title'             => __( 'Helpful Information', 'echo-knowledge-base' ),
						'body_html'         => $this->get_helpful_info_box( $helpful_info_box_configs ),
					),
					array(
						'title'             => __( 'Switch Template', 'echo-knowledge-base' ),
						'desc'              => __( 'If you are having issues with KB layout and spacing, see the KB and Current Template modes.', 'echo-knowledge-base' ),
						'body_html'         => '',
						'read_more_url'     => '#settings__general__general-settings',
						'read_more_text'    => __( 'Click here', 'echo-knowledge-base' ),
						'css_class'         => 'epkb-admin__form-tab-content--manage-theme-compat',
					),
				),
			);
		}

		// Main Page
		$no_module_label = __( 'Unused', 'echo-knowledge-base' );
		$row_1_module = $this->kb_config['ml_row_1_module'];
		$row_2_module = $this->kb_config['ml_row_2_module'];
		$row_3_module = $this->kb_config['ml_row_3_module'];
		$row_4_module = $this->kb_config['ml_row_4_module'];
		$row_5_module = $this->kb_config['ml_row_5_module'];
		$tabs_config['main-page'] = array(
			'title'     => __( 'KB Main Page', 'echo-knowledge-base' ),
			'icon'      => 'epkb-main-page-icon',
			'key'       => 'main-page',
			'active'    => ! $access_to_get_started,
			'sub_tabs'  => array(
				array(
					'icon'      => 'epkb-admin__form-sub-tab-icon-text',
					'icon_text' => __( 'Row 1', 'echo-knowledge-base' ),
					'title'     => $this->is_modular_main_page
									? ( $row_1_module == 'none' ? $no_module_label : $this->kb_config_specs['ml_row_1_module']['options'][$row_1_module] )
									: $this->kb_config_specs['ml_row_1_module']['options']['search'],
					'key'       => 'main-page-ml-row-1',
					'data'      => [ 'module-selector' => 'ml_row_1_module', 'no-module-label' => $no_module_label, 'selected-module' => $row_1_module ],
					'class'     => $this->is_modular_main_page ? ( $row_1_module == 'none' ? 'epkb-admin__form-sub-tab--unused' : '' ) : '',
				),
				array(
					'icon'      => 'epkb-admin__form-sub-tab-icon-text',
					'icon_text' => __( 'Row 2', 'echo-knowledge-base' ),
					'title'     => $this->is_modular_main_page
									? ( $row_2_module == 'none' ? $no_module_label : $this->kb_config_specs['ml_row_2_module']['options'][$row_2_module] )
									: $this->kb_config_specs['ml_row_2_module']['options']['categories_articles'],
					'key'       => 'main-page-ml-row-2',
					'data'      => [ 'module-selector' => 'ml_row_2_module', 'no-module-label' => $no_module_label, 'selected-module' => $row_2_module ],
					'class'     => $this->is_modular_main_page ? ( $row_2_module == 'none' ? 'epkb-admin__form-sub-tab--unused' : '' ) : '',
				),
				array(
					'icon'      => 'epkb-admin__form-sub-tab-icon-text',
					'icon_text' => __( 'Row 3', 'echo-knowledge-base' ),
					'title'     => $this->is_modular_main_page
									? ( $row_3_module == 'none' ? $no_module_label : $this->kb_config_specs['ml_row_3_module']['options'][$row_3_module] )
									: $no_module_label,
					'key'       => 'main-page-ml-row-3',
					'data'      => [ 'module-selector' => 'ml_row_3_module', 'no-module-label' => $no_module_label, 'selected-module' => $row_3_module ],
					'class'     => $this->is_modular_main_page ? ( $row_3_module == 'none' ? 'epkb-admin__form-sub-tab--unused' : '' ) : 'epkb-admin__form-sub-tab--unused',
				),
				array(
					'icon'      => 'epkb-admin__form-sub-tab-icon-text',
					'icon_text' => __( 'Row 4', 'echo-knowledge-base' ),
					'title'     => $this->is_modular_main_page
									? ( $row_4_module == 'none' ? $no_module_label : $this->kb_config_specs['ml_row_4_module']['options'][$row_4_module] )
									: $no_module_label,
					'key'       => 'main-page-ml-row-4',
					'data'      => [ 'module-selector' => 'ml_row_4_module', 'no-module-label' => $no_module_label, 'selected-module' => $row_4_module ],
					'class'     => $this->is_modular_main_page ? ( $row_4_module == 'none' ? 'epkb-admin__form-sub-tab--unused' : '' ) : 'epkb-admin__form-sub-tab--unused',
				),
				array(
					'icon'      => 'epkb-admin__form-sub-tab-icon-text',
					'icon_text' => __( 'Row 5', 'echo-knowledge-base' ),
					'title'     => $this->is_modular_main_page
									? ( $row_5_module == 'none' ? $no_module_label : $this->kb_config_specs['ml_row_5_module']['options'][$row_5_module] )
									: $no_module_label,
					'key'       => 'main-page-ml-row-5',
					'data'      => [ 'module-selector' => 'ml_row_5_module', 'no-module-label' => $no_module_label, 'selected-module' => $row_5_module ],
					'class'     => $this->is_modular_main_page ? ( $row_5_module == 'none' ? 'epkb-admin__form-sub-tab--unused' : '' ) : 'epkb-admin__form-sub-tab--unused',
				),
			),
		);

		// Articles Page
		$tabs_config['article-page'] = array(
			'title'     => __( 'KB Article Page', 'echo-knowledge-base' ),
			'icon'      => 'epkb-article-page-icon',
			'key'       => 'article-page',
			'active'    => false,
			'sub_tabs'  => array(
				array(
					'title'     => __( 'Settings', 'echo-knowledge-base' ),
					'key'       => 'article-page-settings',
				),
			),
		);
		$tabs_config['article-page']['sub_tabs'][] = array(
			'title'     => __( 'Search Box', 'echo-knowledge-base' ),
			'key'       => 'article-page-search-box',
		);
		$tabs_config['article-page']['sub_tabs'][] = array(
			'title'     => __( 'Sidebar', 'echo-knowledge-base' ),
			'key'       => 'article-page-sidebar',
		);
		$tabs_config['article-page']['sub_tabs'][] = array(
			'title'     => __( 'Table of Contents ( TOC )', 'echo-knowledge-base' ),
			'key'       => 'article-page-toc',
		);
		$tabs_config['article-page']['sub_tabs'][] = array(
			'title'     => __( 'Rating and Feedback', 'echo-knowledge-base' ),
			'key'       => 'article-page-ratings',
		);

		// Archive Page
		if ( $this->is_kb_templates ) {
			$tabs_config['archive-page'] = array(
				'title'  => __( 'Category Archive Page', 'echo-knowledge-base' ),
				'icon'   => 'epkb-archive-page-icon',
				'key'    => 'archive-page',
				'active' => false,
			);
		}

		// Labels
		$tabs_config['labels'] = array(
			'title'     => __( 'Labels', 'echo-knowledge-base' ),
			'icon'      => 'ep_font_icon_tag',
			'key'       => 'labels',
			'active'    => false,
		);

		// General
		$is_classic_drill_down_layout = $this->kb_config['kb_main_page_layout'] === 'Drill-Down' || $this->kb_config['kb_main_page_layout'] === 'Classic';
		$tabs_config['general'] = array(
			'title'     => __( 'General' ),
			'icon'      => 'epkb-toggle-icon',
			'key'       => 'general',
			'active'    => false,
			'sub_tabs'  => array(
				array(
					'title'     => __( 'Settings', 'echo-knowledge-base' ),
					'key'       => 'general-settings',
				),
				array(
					'title'     => __( 'Full Editor', 'echo-knowledge-base' ),
					'key'       => 'general-full-editor',
					'contents'  => array(
						// 'fields' is not used for this sub-tab content
						array(
							'title'     => __( 'Visual Editor', 'echo-knowledge-base' ),
							'css_class' => ( $is_classic_drill_down_layout ? 'epkb-admin__form-tab-content--change-style-not-available' : '' ),
							'desc'      => ( $is_classic_drill_down_layout ? __( 'Not available for Classic layout and Drill Down Layout.', 'echo-knowledge-base' ) : '' ),
							'body_html' => $this->show_frontend_editor_links(),
						),
					),
				),
			),
		);

		// Show first special content, then generated for settings 
		foreach ( $tabs_config as $key => $config ) {

			$contents_configs[$config['key']] = isset( $contents_configs[$config['key']] ) ? $contents_configs[$config['key']] : [];

			$tabs_config[$key]['contents'] = empty( $config['contents'] )
				? $this->apply_fields_in_contents_config( $contents_configs[$config['key']] )
				: array_merge( $config['contents'], $this->apply_fields_in_contents_config( $contents_configs[$config['key']] ) );

			if ( empty( $config['sub_tabs'] ) ) {
				continue;
			}

			foreach ( $config['sub_tabs'] as $sub_key => $sub_config ) {
				$tabs_config[$key]['sub_tabs'][$sub_key]['contents'] = empty( $sub_config['contents'] )
					? $this->apply_fields_in_contents_config( $sub_contents_configs[$sub_config['key']] )
					: array_merge( $sub_config['contents'], $this->apply_fields_in_contents_config( $sub_contents_configs[$sub_config['key']] ) );
			}
		}

		return $tabs_config;
	}

	/**
	 * Convert fields in contents configuration array into HTML for each group of fields
	 *
	 * @param $contents_config
	 * @return array
	 */
	private function apply_fields_in_contents_config( $contents_config ) {

		foreach ( $contents_config as $tab => $tab_config ) {

			// leave only corresponding settings (depends on requirements for each field)
			$settings_list = $this->filter_settings( $tab_config['fields'] );

			// unset tab if it has empty fields set
			if ( empty( $settings_list ) ) {
				unset( $contents_config[$tab] );
				continue;
			}

			$contents_config[$tab]['body_html'] = $this->get_settings_html( $settings_list );

			if ( isset( $contents_config[$tab]['dependency'] ) ) {
				$contents_config[$tab]['css_class'] = empty( $contents_config[$tab]['css_class'] )
					? 'eckb-condition-depend__' . implode( ' eckb-condition-depend__', $tab_config['dependency'] )
					: $contents_config[$tab]['css_class'] . ' eckb-condition-depend__' . implode( ' eckb-condition-depend__', $tab_config['dependency'] );
				$contents_config[$tab]['data'] = array(
					'dependency-ids' => implode( ' ', $tab_config['dependency'] ),
					'enable-on-values' => implode( ' ', $tab_config['enable_on'] )
				);
			}

			if ( ! empty( $tab_config['learn_more_links'] ) ) {
				$contents_config[$tab]['body_html'] .= $this->learn_more_block( $tab_config['learn_more_links'] );
			}
		}

		return $contents_config;
	}

	/**
	 * Get HTML list of specific KB config settings for given tab
	 *
	 * @param $settings_list
	 * @return false|string
	 */
	private function get_settings_html( $settings_list ) {
		ob_start();     ?>
		<div class="epkb-admin__kb__form">  <?php
			foreach ( $settings_list as $setting_name => $requirement ) {
				$this->show_kb_setting_html( $setting_name );
			} ?>
		</div>  <?php
		return ob_get_clean();
	}

	/**
	 * Display HTML for single KB config setting by its name
	 *
	 * @param $setting_name
	 * @param bool $show_included
	 */
	private function show_kb_setting_html( $setting_name, $show_included=false ) {

		// handle custom display of certain fields
		if ( in_array( $setting_name, [ 'toc_toggler', 'toc_locations', 'toc_left', 'toc_content', 'toc_right', 'advanced_search_mp_presets', 'advanced_search_ap_presets', 'editor_backend_mode',
			'nav_sidebar_left', 'nav_sidebar_right', 'kb_sidebar_left', 'kb_sidebar_right', 'theme_compatibility_mode', 'typography_message', 'kb_sidebar_left_toggler',
			'kb_sidebar_right_toggler', 'templates_for_kb', 'kb_main_page_layout', 'eprf_pro_description',
			'advanced_search_mp_filter_toggle_pro', 'advanced_search_mp_description_below_input_pro', 'advanced_search_mp_background_color_pro',
			'advanced_search_ap_filter_toggle_pro', 'advanced_search_ap_description_below_input_pro', 'advanced_search_ap_background_color_pro',
			'epkb_ml_custom_css', 'ml_row_1_desktop_width', 'ml_row_2_desktop_width',
			'ml_articles_list_column_1', 'ml_articles_list_column_2', 'ml_articles_list_column_3',
			'ml_row_3_desktop_width', 'ml_row_4_desktop_width', 'ml_row_5_desktop_width', 'ml_faqs_category_ids', 'ml_faqs_kb_id', 'ml_resource_links_settings_tabs',
			'ml_resource_links_1_icon_font', 'ml_resource_links_2_icon_font', 'ml_resource_links_3_icon_font', 'ml_resource_links_4_icon_font',
			'ml_resource_links_5_icon_font', 'ml_resource_links_6_icon_font', 'ml_resource_links_7_icon_font', 'ml_resource_links_8_icon_font',
			'ml_resource_links_1_icon_image', 'ml_resource_links_2_icon_image', 'ml_resource_links_3_icon_image', 'ml_resource_links_4_icon_image',
			'ml_resource_links_5_icon_image', 'ml_resource_links_6_icon_image', 'ml_resource_links_7_icon_image', 'ml_resource_links_8_icon_image',
			'ml_resource_links_settings_pro_description', 'ml_categories_articles_kblk_pro', 'ml_articles_list_kblk_pro',
			'ml_resource_links_1_settings_tab_content', 'ml_resource_links_2_settings_tab_content', 'ml_resource_links_3_settings_tab_content', 'ml_resource_links_4_settings_tab_content',
			'ml_resource_links_5_settings_tab_content', 'ml_resource_links_6_settings_tab_content', 'ml_resource_links_7_settings_tab_content', 'ml_resource_links_8_settings_tab_content',
			'general_typography', 'ml_resource_links_settings_old_elay_warning', 'sidebar_main_page_intro_text_link', 'article_search_sidebar_layout_msg', 'article_list_spacing', ] ) ) {
			$this->show_custom_display_fields( $setting_name, $show_included );
			return;
		}

		if ( ! isset( $this->kb_config_specs[$setting_name] ) ) {
			return;
		}

		$field_spec = $this->kb_config_specs[$setting_name];
		$field_spec = wp_parse_args( $field_spec, EPKB_KB_Config_Specs::get_defaults() );
		$field_spec = $this->set_custom_field_specs( $field_spec );

		// render included fields only inside other fields
		if ( isset( $field_spec['is_included'] ) && ! $show_included ) {
			return;
		}

		$input_group_class = empty( $field_spec['input_group_class'] ) ? '' : $field_spec['input_group_class'];

		// display fields based on type
		$type = empty( $field_spec['type'] ) ? '' : $field_spec['type'];

		$input_args = array(
			'specs' => $setting_name,
			'desc'  => empty( $field_spec['desc'] ) ? '' : $field_spec['desc'],
		);

		// fields with 'OR' logic dependency to other field values
		if ( isset( $field_spec['dependency'] ) ) {
			$input_group_class .= ' ' . 'eckb-condition-depend__' . implode( ' eckb-condition-depend__', $field_spec['dependency'] );
			$input_args['group_data'] = array(
				'dependency-ids' => implode( ' ', $field_spec['dependency'] ),
				'enable-on-values' => implode( ' ', $field_spec['enable_on'] )
			);
		}

		// fields with 'AND' logic dependency to other field values
		if ( isset( $field_spec['dependency_and'] ) ) {
			$dependencies_ids = array_keys( $field_spec['dependency_and'] );
			$input_group_class .= ' ' . 'eckb-condition-depend-and__' . implode( ' eckb-condition-depend-and__', $dependencies_ids );
			$input_args['group_data']['dependency-and'] = '';
			foreach ( $dependencies_ids as $one_dependency_id ) {
				$input_args['group_data']['dependency-and'] .= ' ' . $one_dependency_id . '--' . $field_spec['dependency_and'][$one_dependency_id];
			}
		}

		// add group data from specs if available
		if ( isset( $field_spec['group_data'] ) ) {
			$input_args['group_data'] = empty( $input_args['group_data'] ) ? $field_spec['group_data'] : array_merge_recursive( $input_args['group_data'], $field_spec['group_data'] );
		}

		$input_args = $this->set_input_tooltip( $input_args );

		switch( $type ) {

			case EPKB_Input_Filter::COLOR_HEX:
				EPKB_HTML_Elements::color( array_merge_recursive( $input_args, [
					'value' => $this->kb_config[$setting_name],
					'input_group_class' => $input_group_class,
				] ) );
				break;

			case EPKB_Input_Filter::SELECTION:

				// icon selection
				if ( in_array( $setting_name, [ 'expand_articles_icon', 'breadcrumb_icon_separator', 'sidebar_expand_articles_icon', 'section_head_category_icon_location', 'width',
					'rating_like_style', 'article_nav_sidebar_type_left', 'article_nav_sidebar_type_right', 'rating_mode', 'rating_stats_footer_toggle', 'ml_resource_links_icon_location' ] ) ) {
					EPKB_HTML_Elements::radio_buttons_icon_selection( array_merge_recursive( $input_args, [
						'value'             => $this->kb_config[$setting_name],
						'input_group_class' => $input_group_class,
					] ) );
					break;
				}

				if ( in_array( $setting_name, ['article_toc_hx_level', 'article_toc_hy_level'] ) ) {
					EPKB_HTML_Elements::radio_buttons_horizontal( array_merge_recursive( $input_args, [
						'value'             => $this->kb_config[$setting_name],
						'input_group_class' => 'epkb-radio-horizontal-button-group-container--small-btn ' . $input_group_class,
					] ) );
					break;
				}

				if ( in_array( $setting_name, ['template_category_archive_page_style', 'articles_comments_global', 'rating_feedback_trigger_stars',
					'rating_feedback_required_stars', 'rating_feedback_trigger_like', 'rating_feedback_required_like'] ) ) {
					EPKB_HTML_Elements::dropdown( array_merge_recursive( $input_args, [
						'value' => $this->kb_config[$setting_name],
						'input_group_class' => $input_group_class,
					] ) );
					break;
				}

				if ( in_array( $setting_name, ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'] ) ) {
					switch ( $setting_name ) {
						case 'ml_row_1_module': $custom_selection_label = __( 'first row', 'echo-knowledge-base' ); break;
						case 'ml_row_2_module': $custom_selection_label = __( 'second row', 'echo-knowledge-base' ); break;
						case 'ml_row_3_module': $custom_selection_label = __( 'third row', 'echo-knowledge-base' ); break;
						case 'ml_row_4_module': $custom_selection_label = __( 'fourth row', 'echo-knowledge-base' ); break;
						case 'ml_row_5_module': $custom_selection_label = __( 'fifth row', 'echo-knowledge-base' ); break;
						default: $custom_selection_label = ''; break;
					}
					EPKB_HTML_Elements::custom_dropdown( array_merge_recursive( $input_args, [
						'value' => $this->kb_config[$setting_name],
						'input_group_class' => 'epkb-row-module-setting ' . $input_group_class,
						'group_data' => array( 'custom-selection-group' => 'ml-row', 'custom-selection-label' => $custom_selection_label, 'settings-group' => 'ml-row', 'custom-unselection-group' => 'ml-row' ),
					] ) );
					break;
				}

				if ( in_array( $setting_name, ['ml_categories_articles_sidebar_position_1', 'ml_categories_articles_sidebar_position_2'] ) ) {
					switch ( $setting_name ) {
						case 'ml_categories_articles_sidebar_position_1': $custom_selection_label = __( 'position 1', 'echo-knowledge-base' ); break;
						case 'ml_categories_articles_sidebar_position_2': $custom_selection_label = __( 'position 2', 'echo-knowledge-base' ); break;
						default: $custom_selection_label = ''; break;
					}
					EPKB_HTML_Elements::custom_dropdown( array_merge_recursive( $input_args, [
						'value' => $this->kb_config[$setting_name],
						'input_group_class' => 'epkb-ml-categories-articles-sidebar-positions ' . $input_group_class,
						'group_data' => array( 'custom-selection-group' => 'ml-categories-articles-sidebar-position', 'custom-selection-label' => $custom_selection_label ),
					] ) );
					break;
				}

				if ( ! empty( $field_spec['options'] ) && count( $field_spec['options'] ) == 4 ) {
					EPKB_HTML_Elements::radio_buttons_horizontal( array_merge_recursive( $input_args, [
						'value'             => $this->kb_config[ $setting_name ],
						'input_group_class' => 'epkb-radio-horizontal-button-group-container--small-btn ' . $input_group_class,
					] ) );
					break;
				}

				EPKB_HTML_Elements::radio_buttons_horizontal( array_merge_recursive( $input_args, [
					'value' => $this->kb_config[$setting_name],
					'input_group_class' => $input_group_class,
				] ) );
				break;

			case EPKB_Input_Filter::CHECKBOX:
				EPKB_HTML_Elements::checkbox_toggle( array_merge_recursive( $input_args, [
					'id'        => $setting_name,
					'text'      => $field_spec['label'],
					'checked'   => $this->kb_config[$setting_name] == 'on',
					'name'      => $setting_name,
					'input_group_class' => $input_group_class,
				] ) );
				if ( $this->is_old_elay && $setting_name == 'modular_main_page_toggle' && ( $this->is_modular_main_page || $this->is_elay_layout )  ) {
					EPKB_HTML_Forms::notification_box_middle( array(
						'type' => 'error',
						'desc' => '<p>' . esc_html__( 'Sidebar and Grid layouts are supported in the "KB - Elegant Layouts" add-on version higher than 2.14.1.', 'echo-knowledge-base' ) .
							'<br>' . sprintf( esc_html__( 'Please %supgrade%s the add-on to use Modular Main Page feature for the Sidebar and Grid layouts.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/" target="_blank">', '</a>' ) . '</p>',
					) );
				}
				break;

			case EPKB_Input_Filter::WP_EDITOR:
				EPKB_HTML_Elements::wp_editor( array_merge_recursive( $input_args, [
					'value'             => $this->kb_config[$setting_name],
					'editor_options'    => [ 'teeny' => 1, 'media_buttons' => false ],
					'input_group_class' => $input_group_class,
				] ) );
				break;

			case 'textarea':
				EPKB_HTML_Elements::textarea( array_merge_recursive( $input_args, [
					'value'             => $this->kb_config[$setting_name],
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-input-group epkb-admin__input-field epkb-admin__textarea-field ' . $input_group_class,
				] ) );
				break;

			case EPKB_Input_Filter::TEXT:
				if ( in_array( $setting_name, [ 'advanced_search_mp_description_below_input', 'advanced_search_mp_description_below_title', 'advanced_search_ap_description_below_input', 'advanced_search_ap_description_below_title' ] ) ) {
					EPKB_HTML_Elements::textarea( [
						'specs'             => $setting_name,
						'value'             => $this->kb_config[$setting_name],
						'main_tag'          => 'div',
						'input_group_class' => 'epkb-input-group epkb-admin__input-field epkb-admin__textarea-field ' . $input_group_class,
					] );
				} else {
					EPKB_HTML_Elements::text( array_merge_recursive( $input_args, [
						'value'             => $this->kb_config[$setting_name],
						'input_group_class' => $input_group_class,
					] ) );
				}
				break;

			default:
				EPKB_HTML_Elements::text( array_merge_recursive( $input_args, [
					'value'             => $this->kb_config[$setting_name],
					'input_group_class' => $input_group_class,
					'label'             => empty( $field_spec['label'] ) ? '' : $field_spec['label'],
				] ) );
		}
	}

	/**
	 * Display custom HTML for single KB config setting by its name
	 *
	 * @param $setting_name
	 * @param bool $show_included
	 */
	private function show_custom_display_fields( $setting_name, $show_included=false ) {

		// allow set custom specs for non-specs fields (used for custom field layout)
		$field_spec = isset( $this->kb_config_specs[$setting_name] ) ? $this->kb_config_specs[$setting_name] : ['name' => $setting_name];
		$field_spec = $this->set_custom_field_specs( $field_spec );

		// render included fields only inside other fields
		if ( isset( $field_spec['is_included'] ) && ! $show_included ) {
			return;
		}

		$input_group_class = empty( $field_spec['input_group_class'] ) ? '' : $field_spec['input_group_class'];
		$group_data = [];

		// fields with 'OR' logic dependency to other field values
		if ( isset( $field_spec['dependency'] ) ) {
			$input_group_class .= ' ' . 'eckb-condition-depend__' . implode( ' eckb-condition-depend__', $field_spec['dependency'] );
			$group_data = array(
				'dependency-ids' => implode( ' ', $field_spec['dependency'] ),
				'enable-on-values' => implode( ' ', $field_spec['enable_on'] )
			);
		}

		// fields with 'AND' logic dependency to other field values
		if ( isset( $field_spec['dependency_and'] ) ) {
			$dependencies_ids = array_keys( $field_spec['dependency_and'] );
			$input_group_class .= ' ' . 'eckb-condition-depend-and__' . implode( ' eckb-condition-depend-and__', $dependencies_ids );
			$group_data['dependency-and'] = '';
			foreach ( $dependencies_ids as $one_dependency_id ) {
				$group_data['dependency-and'] .= ' ' . $one_dependency_id . '--' . $field_spec['dependency_and'][$one_dependency_id];
			}
		}

		if ( $setting_name == 'toc_toggler' ) {
			EPKB_HTML_Elements::checkbox_toggle( [
				'id'        => $setting_name,
				'text'      => __( 'Show TOC', 'echo-knowledge-base' ),
				'checked'   => ! empty( $this->kb_config['article_sidebar_component_priority']['toc_left'] ) || ! empty( $this->kb_config['article_sidebar_component_priority']['toc_content'] ) || ! empty( $this->kb_config['article_sidebar_component_priority']['toc_right'] ),
				'name'      => $setting_name,
				'tooltip_external_links' => [
						[ 'link_text' => 'Learn More', 'link_url' => 'https://www.echoknowledgebase.com/documentation/table-of-content' ]
				]
			] );
		}

		if ( $setting_name == 'toc_locations' ) {
			EPKB_HTML_Elements::checkboxes_as_icons_selection( [
				'name' => $setting_name,
				'label' => __( 'TOC Location', 'echo-knowledge-base' ),
				'values' => [
					( empty( $this->kb_config['article_sidebar_component_priority']['toc_left'] ) ? null : 'toc_left' ),
					( empty( $this->kb_config['article_sidebar_component_priority']['toc_content'] ) ? null : 'toc_content' ),
					( empty( $this->kb_config['article_sidebar_component_priority']['toc_right'] ) ? null : 'toc_right' ),
				],
				'options' => array(
					'toc_left' => __( 'Left', 'echo-knowledge-base' ),
					'toc_content' => __( 'Top', 'echo-knowledge-base' ),
					'toc_right' => __( 'Right', 'echo-knowledge-base' ),
				),
				'default' => '0',
			]);
		}

		if ( in_array( $setting_name, ['toc_left', 'toc_content', 'toc_right'] ) ) {
			switch ( $setting_name ) {
				case 'toc_left':
					$field_label = is_rtl() ? __( 'Display on the Right', 'echo-knowledge-base' ) : __( 'Display on the Left', 'echo-knowledge-base' );
					$options = array(
						'0' => '-----',
						'1' => __( 'Position', 'echo-knowledge-base' ) . ' 1',
						'2' => __( 'Position', 'echo-knowledge-base' ) . ' 2',
						'3' => __( 'Position', 'echo-knowledge-base' ) . ' 3',
					);
					break;
				case 'toc_content':
					$field_label = __( 'Display Above the Article', 'echo-knowledge-base' );
					$options = array(
						'0' => '-----',
						'1' => __( 'Displayed', 'echo-knowledge-base' ),
					);
					break;
				case 'toc_right':
					$field_label = is_rtl() ? __( 'Display on the Left', 'echo-knowledge-base' ) : __( 'Display on the Right', 'echo-knowledge-base' );
					$options = array(
						'0' => '-----',
						'1' => __( 'Position', 'echo-knowledge-base' ) . ' 1',
						'2' => __( 'Position', 'echo-knowledge-base' ) . ' 2',
						'3' => __( 'Position', 'echo-knowledge-base' ) . ' 3',
					);
					break;
				default:
					$field_label = '';
					$options = [];
					break;
			}
			EPKB_HTML_Elements::custom_dropdown( array(
				'input_group_class' => 'epkb-admin__input-field epkb-admin__input-field--toc-positions' . ' ' . $input_group_class . ' ',
				'group_data'        => $group_data,
				'label'             => $field_label,
				'label_class'       => 'epkb-main_label',
				'name'              => $setting_name,
				'value'             => $this->kb_config['article_sidebar_component_priority'][$setting_name],
				'options'           => $options,
				'options_icons'     => true,
			) );
			if ( $setting_name == 'toc_right' ) {    ?>
				<p class="epkb-input-group__field-description"><?php esc_html_e( 'Number 1 places the element at the top, 2 below it, and so on.', 'echo-knowledge-base' ); ?></p>    <?php
			}
		}

		if ( in_array( $setting_name, [ 'advanced_search_mp_presets', 'advanced_search_ap_presets' ] ) ) {
			EPKB_HTML_Elements::radio_buttons_horizontal( [
				'name'              => $setting_name,
				'group_data'        => $group_data,
				'input_group_class' => 'epkb-radio-horizontal-button-group-container' . ' ' . $input_group_class . ' ',
				'label'             => __( 'Pre-made Designs', 'echo-knowledge-base' ),
				'value'             => 'current',
				'options'           => array(
					'current' => __( 'Current', 'echo-knowledge-base' ),
					'1-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '1',
					'2-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '2',
					'3-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '3',
					'4-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '4',
					'5-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '5',
					'6-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '6',
					/* '7-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '7',  TODO update for new designs
					'8-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '8',
					'9-search' => __( 'Design', 'echo-knowledge-base' ) . ' ' . '9', */
				),
			] );
		}

		if ( $setting_name == 'editor_backend_mode' ) {
			EPKB_HTML_Elements::radio_buttons_horizontal( [
				'label'     => __( 'Launch the Editor', 'echo-knowledge-base' ),
				'value'     => EPKB_Core_Utilities::is_kb_flag_set( $setting_name ) ? '1' : '0',
				'name'      => $setting_name,
				'options'   => [
					'0'  => __( 'On the Frontend', 'echo-knowledge-base' ),
					'1'   => __( 'On the Backend', 'echo-knowledge-base' ),
				],
				'default'   => '0',
			] );
		}

		if ( $setting_name == 'theme_compatibility_mode' ) {    ?>
			<p><?php esc_html_e( 'Display KB Main Page, Article Pages and Category Archive Pages using either your current theme template or KB template.', 'echo-knowledge-base' ); ?></p><br><?php
		}

		if ( $setting_name == 'typography_message' ) {  ?>
			<p><?php
				esc_html_e( 'Typography, such as font size, font family and font weight can be adjusted using the Visual Editor.', 'echo-knowledge-base' ); ?>

				<a href="<?php echo add_query_arg( array( 'action' => 'epkb_load_editor' ), EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config ) ); ?>" data-open-editor-link="<?php echo EPKB_Core_Utilities::is_kb_flag_set( 'editor_backend_mode' ) ? 'back' : 'front'; ?>"
				    target="_blank"><?php esc_html_e( 'Open Visual Editor', 'echo-knowledge-base' ); ?></a>
				<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
			</p> <?php
		}

		if ( in_array( $setting_name, [ 'nav_sidebar_left', 'nav_sidebar_right' ] ) && isset( $this->kb_config['article_sidebar_component_priority'][$setting_name] ) ) {
			EPKB_HTML_Elements::custom_dropdown( array(
				'input_group_class' => 'epkb-admin__input-field' . ' ' . $input_group_class . ' ',
				'group_data'        => $group_data,
				'label'             => __( 'Sidebar Navigation Location', 'echo-knowledge-base' ),
				'label_class'       => 'epkb-main_label',
				'name'              => $setting_name,
				'value'             => $this->kb_config['article_sidebar_component_priority'][$setting_name],
				'options'           => array(
					'0' => '-----',
					'1' => __( 'Position', 'echo-knowledge-base' ) . ' 1',
					'2' => __( 'Position', 'echo-knowledge-base' ) . ' 2',
					'3' => __( 'Position', 'echo-knowledge-base' ) . ' 3',
				),
				'options_icons'     => true,
			) );
		}

		if ( $setting_name == 'kb_sidebar_left_toggler' ) {
			EPKB_HTML_Elements::checkbox_toggle( [
				'id'        => $setting_name,
				'text'      => __( 'Show Widgets from KB Sidebar ', 'echo-knowledge-base' ),
				'checked'   => $this->kb_config['article_sidebar_component_priority']['kb_sidebar_left'] != '0',
				'name'      => $setting_name,
				'group_data' => array_merge( [ 'control-toggler' => 'kb_sidebar_left', 'control-disabled-value' => '0', 'control-enabled-value' => '1' ], $group_data ),
			] );
		}

		if ( $setting_name == 'kb_sidebar_right_toggler' ) {
			EPKB_HTML_Elements::checkbox_toggle( [
				'id'        => $setting_name,
				'text'      => __( 'Show Widgets from KB Sidebar ', 'echo-knowledge-base' ),
				'checked'   => $this->kb_config['article_sidebar_component_priority']['kb_sidebar_right'] != '0',
				'name'      => $setting_name,
				'group_data' => array_merge( [ 'control-toggler' => 'kb_sidebar_right', 'control-disabled-value' => '0', 'control-enabled-value' => '1' ], $group_data ),
			] );
		}

		if ( in_array( $setting_name, [ 'kb_sidebar_left', 'kb_sidebar_right' ] ) && isset( $this->kb_config['article_sidebar_component_priority'][$setting_name] ) ) {
			EPKB_HTML_Elements::custom_dropdown( array(
				'input_group_class' => 'epkb-admin__input-field' . ' ' . $input_group_class . ' ',
				'group_data'        => $group_data,
				'label'             => __( 'Widget Location', 'echo-knowledge-base' ),
				'label_class'       => 'epkb-main_label',
				'name'              => $setting_name,
				'value'             => $this->kb_config['article_sidebar_component_priority'][$setting_name],
				'options'           => array(
					'0' => '-----',
					'1' => __( 'Position', 'echo-knowledge-base' ) . ' 1',
					'2' => __( 'Position', 'echo-knowledge-base' ) . ' 2',
					'3' => __( 'Position', 'echo-knowledge-base' ) . ' 3',
				),
				'options_icons'     => true,
			) );
		}

		if ( $setting_name == 'templates_for_kb' ) { ?>
			<div class="epkb-input-group epkb-admin__radio-icons epkb-admin__input-field epkb-admin__select-field " id="templates_for_kb_group">

			<span class="epkb-main_label "><?php esc_html_e( 'Choose Template','echo-knowledge-base' ); ?></span>

			<div class="epkb-radio-buttons-container " id="templates_for_kb">
				<div class="epkb-input-container">
					<label class="epkb-label" for="templates_for_kb0">
						<span class="epkb-label__text"><?php esc_html_e( 'Current Theme Template','echo-knowledge-base' ); ?></span>
						<input class="epkb-input" type="radio" name="templates_for_kb" id="templates_for_kb0" value="current_theme_templates" autocomplete="off" <?php checked( $this->is_kb_templates, false ); ?>>
						<span class="epkbfa epkbfa-font epkbfa-current_theme_templates epkbfa-input-icon"></span>
					</label>
					<div class="epkb-templates-description">
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Blog Sidebar On article page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-question"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php printf( esc_html__( 'Full Width Page (if your theme allows)', 'echo-knowlegde-base' ) ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Category Archive displayed by theme', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-question"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Padding / Margin options', 'echo-knowlegde-base' ); ?></div>
						</div>
					</div>
				</div>
				<div class="epkb-input-container">
					<label class="epkb-label" for="templates_for_kb1">
						<span class="epkb-label__text"><?php esc_html_e( 'Knowledge Base Template','echo-knowledge-base' ); ?></span>
						<input class="epkb-input" type="radio" name="templates_for_kb" id="templates_for_kb1" value="kb_templates" autocomplete="off" <?php checked( $this->is_kb_templates ); ?>>
						<span class="epkbfa epkbfa-font epkbfa-kb_templates epkbfa-input-icon"></span>
					</label>
					<div class="epkb-templates-description">
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-times"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Blog Sidebar On article page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line epkb-editor-settings-accordeon-item__description-line--margin">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'Full Width Page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e( 'KB Styled Category Archive page', 'echo-knowlegde-base' ); ?></div>
						</div>
						<div class="epkb-editor-settings-accordeon-item__description-line">
							<div class="epkb-editor-settings-accordeon-item__description-icon"><i class="epkbfa epkbfa-check"></i></div>
							<div class="epkb-editor-settings-accordeon-item__description-text"><?php esc_html_e ('Padding / Margin options', 'echo-knowlegde-base' ); ?></div>
						</div>
					</div>
				</div>
			</div>
			</div><?php
		}

		if ( $setting_name == 'kb_main_page_layout' ) {
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );
			$ix = 0;    ?>

			<div class="epkb-input-group epkb-admin__radio-icons <?php echo esc_attr( $input_group_class ); ?>" id="<?php echo esc_attr( $setting_name ); ?>_group" <?php echo $group_data_escaped; ?>>

				<span class="epkb-main_label"><?php echo esc_html( $this->kb_config_specs[$setting_name]['label'] );
					EPKB_HTML_Elements::display_tooltip( '', '', [], [
						[ 'link_text' => __('KB Layouts', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/changing-layouts' ],
						[ 'link_text' => __('Tabs Layout', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/using-tabs-layout' ],
						[ 'link_text' => __('Grid Layout', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/grid-layout' ],
						[ 'link_text' => __('Sidebar Layout', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/sidebar-layout' ],
						[ 'link_text' => __('Main Page Width', 'echo-knowledge-base'), 'link_url' => '://www.echoknowledgebase.com/documentation/main-page-widt' ]
					] );    ?>
				</span>

				<div class="epkb-radio-buttons-container" id="<?php echo esc_attr( $setting_name ); ?>">              <?php

					foreach( $this->kb_config_specs[$setting_name]['options'] as $key => $label ) { ?>
						<div class="epkb-input-container">
							<label class="epkb-label" for="<?php echo esc_attr( $setting_name ) . $ix ?>">
								<span class="epkb-label__text"><?php echo esc_html( $label ); ?></span>
								<input class="epkb-input" type="radio"
								       name="<?php echo esc_attr( $setting_name ); ?>"
								       id="<?php echo esc_attr( $setting_name . $ix ); ?>"
								       value="<?php echo esc_attr( $key ); ?>"
								       autocomplete="off"   <?php
								checked( $key,  $this->kb_config[$setting_name] )   ?>
								/>
								<span class="epkbfa epkbfa-font epkbfa-<?php echo esc_attr( $key ); ?> epkbfa-input-icon"></span>
							</label>
						</div>  <?php
						$ix++;
					}

					if ( ! $this->elay_enabled ) {
						EPKB_HTML_Forms::dialog_pro_feature_ad( [
							'id' => 'epkb-dialog-pro-feature-ad-kb_main_page_layout',
							'title' => sprintf( __( "Unlock %sGrid and Sidebar Layout%s By Upgrading to PRO ", 'echo-knowledge-base' ), '<strong>', '</strong>' ),
							'list' => array( __( 'Simplify your Main Page with our intuitive Grid Layout', 'echo-knowledge-base' ), __( 'Streamline your navigation with the Sidebar Layout on the Main Page', 'echo-knowledge-base' ), __( 'Access essential resources with our Main Page\'s Resource Links feature', 'echo-knowledge-base' ) ),
							'btn_text' => __( 'Upgrade Now', 'echo-knowledge-base' ),
							'btn_url' => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
							'show_close_btn' => 'yes',
							'return_html' => true
						] );
					}   ?>
				</div>
			</div>
			<p>				<?php
				esc_html_e( 'Switch Between KB and Current Template', 'echo-knowledge-base' ); ?>
				<a class="epkb-admin__form-tab-content__to-settings-link" href="#" target="_blank"><?php esc_html_e( 'here', 'echo-knowledge-base' );  ?></a>
			</p> <?php
		}

		if ( $setting_name == 'ml_resource_links_settings_pro_description' ) {
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-admin__input-field <?php echo esc_attr( $input_group_class ); ?>" <?php echo $group_data_escaped; ?>> <?php
				echo EPKB_HTML_Forms::pro_feature_ad_box( array(
					'title'             => sprintf( __( "Unlock %sResource Links Feature%s By Upgrading to PRO ", 'echo-knowledge-base' ), '<strong>', '</strong>' ),
					'list'              => array( __('Grid Layout for the Main Page', 'echo-knowledge-base'), __('Sidebar Layout for the Main Page', 'echo-knowledge-base'), __('Resource Links feature for the Main Page', 'echo-knowledge-base') ),
					'btn_text'          => __( 'Upgrade Now', 'echo-knowledge-base' ),
					'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/',
					'return_html'       => true,
				) );    ?>
			</div>  <?php
		}

		if ( $setting_name == 'epkb_ml_custom_css' ) {
			EPKB_HTML_Elements::textarea( [
				'name'              => 'epkb_ml_custom_css',
				'label'             => __( 'Custom CSS for Modular Main Page', 'echo-knowledge-base' ),
				'value'             => EPKB_Utilities::get_wp_option( 'epkb_ml_custom_css_' . $this->kb_config['id'], '' ),
				'main_tag'          => 'div',
				'input_group_class' => 'epkb-input-group epkb-admin__input-field epkb-admin__textarea-field' . ' ' . $input_group_class . ' ',
			] );
		}

		if ( in_array( $setting_name, [ 'ml_row_1_desktop_width', 'ml_row_2_desktop_width', 'ml_row_3_desktop_width', 'ml_row_4_desktop_width',  'ml_row_5_desktop_width' ] ) ) {   ?>
			<div class="epkb-input-group-combined-units">   <?php
				EPKB_HTML_Elements::text( [
					'specs'             => $setting_name,
					'value'             => $this->kb_config[$setting_name],
					'group_data'        => $group_data,
					'input_group_class' => $input_group_class . ' ',
					'tooltip_external_links' => [
						[ 'link_text' => __( 'Learn More', 'echo-knowledge-base' ), 'link_url' => 'https://www.echoknowledgebase.com/documentation/main-page-width' ]
					]
				] );
				EPKB_HTML_Elements::radio_buttons_horizontal( [
					'specs'             => $field_spec['units_setting_name'],
					'value'             => $this->kb_config[$field_spec['units_setting_name']],
					'group_data'        => $group_data,
					'input_group_class' => 'epkb-radio-horizontal-button-group-container--small-btn epkb-module-row-width-units-selection' . ' ' . $input_group_class . ' ',
				] );?>
			</div>  <?php
		}

		if ( $setting_name == 'ml_faqs_category_ids' ) {
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
			$faqs_category_ids = EPKB_Utilities::get_kb_option( $this->kb_config['id'], EPKB_ML_FAQs::FAQS_CATEGORY_IDS, array() );

			// if FAQs KB was deleted, archived, or not defined, then use current KB id and unselect FAQs Categories
			$faqs_kb_id = EPKB_Utilities::get_kb_option( $this->kb_config['id'], EPKB_ML_FAQs::FAQS_KB_ID, $this->kb_config['id'] );
			$faqs_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $faqs_kb_id, true );
			if ( is_wp_error( $faqs_kb_config ) || EPKB_Core_Utilities::is_kb_archived( $faqs_kb_config['status'] ) ) {
				$faqs_kb_id = $this->kb_config['id'];
				$faqs_category_ids = [];
			}

			$ix = 0;

			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>

			<div class="epkb-input-group epkb-admin__input-field epkb-admin__checkboxes-multiselect <?php echo esc_attr( $input_group_class ); ?>"
			     id="<?php echo esc_attr( $setting_name ); ?>_group" <?php echo $group_data_escaped; ?>>

				<div class="epkb-main_label"><?php esc_html_e( 'Categories', 'echo-knowledge-base' ); ?></div>

				<div class="epkb-checkboxes-horizontal" id="<?php echo esc_attr( $setting_name ); ?>"> <?php

					foreach ( $all_kb_configs as $one_kb_config ) {

						$one_kb_id = $one_kb_config['id'];

						// Do not show archived KBs
						if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
							continue;
						}

						// Do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
						$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
						if ( ! current_user_can( $required_capability ) ) {
							continue;
						}

						$category_seq_data = EPKB_Utilities::get_kb_option( $one_kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );
						$articles_seq_data = EPKB_Utilities::get_kb_option( $one_kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true );

						if ( EPKB_Utilities::is_wpml_enabled( $one_kb_config ) ) {
							$category_seq_data = EPKB_WPML::apply_category_language_filter( $category_seq_data );
							$articles_seq_data = EPKB_WPML::apply_article_language_filter( $articles_seq_data );
						}

						$stored_ids_obj = new EPKB_Categories_Array( $category_seq_data ); // normalizes the array as well
						$allowed_categories_ids = $stored_ids_obj->get_all_keys();
						$categories = array_keys( $allowed_categories_ids );

						$categories_data = array();
						foreach ( $categories as $category_id ) {

							if ( empty( $articles_seq_data[$category_id] ) || empty( $articles_seq_data[$category_id][0] ) ) {
								continue;
							}

							$categories_data[$category_id] = $articles_seq_data[$category_id][0];
						}   ?>

						<div class="epkb-ml-faqs-kb-categories epkb-ml-faqs-kb-categories--<?php echo esc_attr( $one_kb_id ); echo $one_kb_id == $faqs_kb_id ? '' : ' epkb-hide-elem'; ?>">   <?php

							foreach( $categories_data as $category_id => $label ) {

								$checked = in_array( $category_id, $faqs_category_ids );
								$label = str_replace( ',', '', $label );
								$input_id = $setting_name . '-' . $ix;  ?>

								<div class="epkb-input-group">
									<label for="<?php echo esc_attr( $input_id ); ?>">										<?php
										echo esc_html( $label ); ?>
									</label>
									<div class="input_container">
										<input type="checkbox" name="<?php echo esc_attr( $setting_name ); ?>" id="<?php echo esc_attr( $input_id ); ?>" autocomplete="off" value="<?php echo esc_attr( $category_id ); ?>" <?php checked( true, $checked ); ?> />
									</div>
								</div>   	<?php

								$ix++;
							}   ?>

						</div>  <?php
					}   ?>

				</div>
			</div>  <?php
		}

		if ( $setting_name == 'ml_faqs_kb_id' ) {
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();

			// if FAQs KB was deleted, archived, or not defined, then use current KB id
			$faqs_kb_id = EPKB_Utilities::get_kb_option( $this->kb_config['id'], EPKB_ML_FAQs::FAQS_KB_ID, $this->kb_config['id'] );
			$faqs_kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $faqs_kb_id, true );
			if ( is_wp_error( $faqs_kb_config ) || EPKB_Core_Utilities::is_kb_archived( $faqs_kb_config['status'] ) ) {
				$faqs_kb_id = $this->kb_config['id'];
			}

			$filtered_kbs = [];
			foreach ( $all_kb_configs as $one_kb_config ) {

				$one_kb_id = $one_kb_config['id'];

				// Do not show archived KBs
				if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
					continue;
				}

				// Do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
				$required_capability = EPKB_Admin_UI_Access::get_contributor_capability( $one_kb_id );
				if ( ! current_user_can( $required_capability ) ) {
					continue;
				}

				$filtered_kbs[$one_kb_id] = $one_kb_config['kb_name'];
			}

			EPKB_HTML_Elements::dropdown( array(
				'input_group_class' => 'epkb-admin__input-field' . ' ' . $input_group_class . ' ',
				'group_data'        => $group_data,
				'label'             => __( 'Knowledge Base', 'echo-knowledge-base' ),
				'label_class'       => 'epkb-main_label',
				'name'              => $setting_name,
				'value'             => $faqs_kb_id,
				'options'           => $filtered_kbs,
			) );
		}

		if ( in_array( $setting_name, ['ml_articles_list_column_1', 'ml_articles_list_column_2', 'ml_articles_list_column_3'] ) ) {

			if ( $this->kb_config[$setting_name] == 'popular_articles' && $this->kb_config['article_views_counter_enable'] == 'off' ) {
				EPKB_HTML_Forms::notification_box_middle( array(
					'type' => 'error',
					'desc' => '<p>' . esc_html__( 'Enable the Article Views Counter in the Article Page settings if you are using the Popular Articles feature.', 'echo-knowledge-base' ) . '</p>',
				) );
			}

			$group_data['custom-selection-group'] = 'ml_articles_list_column_group';
			$group_data['custom-unselection-group'] = 'ml_articles_list_column_group';
			EPKB_HTML_Elements::custom_dropdown( array(
				'input_group_class' => 'epkb-admin__input-field' . ' ' . $input_group_class . ' ',
				'group_data'        => $group_data,
				'label'             => $field_spec['label'],
				'label_class'       => 'epkb-main_label',
				'name'              => $setting_name,
				'value'             => $this->kb_config[$setting_name],
				'options'           => $field_spec['options'],
				'tooltip_body'      => __( 'Enable the Article Views Counter in the Article Page settings if you are using the Popular Articles feature.', 'echo-knowledge-base' ),
			) );
		}

		// Font Icons
		if ( in_array( $setting_name, [ 'ml_resource_links_1_icon_font', 'ml_resource_links_2_icon_font', 'ml_resource_links_3_icon_font', 'ml_resource_links_4_icon_font',
			'ml_resource_links_5_icon_font', 'ml_resource_links_6_icon_font', 'ml_resource_links_7_icon_font', 'ml_resource_links_8_icon_font' ] ) ) {
			$active_icon_name = $this->kb_config[$setting_name];
			$group_data['setting-name'] = $setting_name;
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-input-group epkb-admin__input-field epkb-admin__icon-font-selection <?php echo esc_attr( $input_group_class ); ?>"
					id="<?php echo esc_attr( $setting_name ); ?>" <?php echo $group_data_escaped; ?>>
				<span class="epkb-main_label"></span>  <?php
				EPKB_Icons::get_icons_pack_html( true, $active_icon_name ); ?>
				<input type="hidden" name="<?php echo esc_attr( $setting_name ); ?>" id="<?php echo esc_attr( $setting_name ); ?>" value="<?php echo esc_attr( $active_icon_name ); ?>">
			</div>  <?php
		}

		// Resource links tabs
		if ( $setting_name == 'ml_resource_links_settings_tabs' ) {
			EPKB_HTML_Elements::radio_buttons_horizontal( [
				'name'      => $setting_name,
				'input_group_class' => 'epkb-radio-horizontal-button-group-container--tabs ' . $input_group_class,
				'value'     => 'resource-1',
				'options'   => [
					'resource-1' => '#1',
					'resource-2' => '#2',
					'resource-3' => '#3',
					'resource-4' => '#4',
					'resource-5' => '#5',
					'resource-6' => '#6',
					'resource-7' => '#7',
					'resource-8' => '#8',
				],
			] );
		}

		// Image Icons
		if ( in_array( $setting_name, [ 'ml_resource_links_1_icon_image', 'ml_resource_links_2_icon_image', 'ml_resource_links_3_icon_image', 'ml_resource_links_4_icon_image',
			'ml_resource_links_5_icon_image', 'ml_resource_links_6_icon_image', 'ml_resource_links_7_icon_image', 'ml_resource_links_8_icon_image' ] ) ) {
			$image_size = empty( $this->kb_config['ml_resource_links_icon_image_size'] ) ? 'full' : $this->kb_config['ml_resource_links_icon_image_size'];
			$active_image_id = $this->kb_config[$setting_name];
			$image_url = wp_get_attachment_image_url( $active_image_id, $image_size );
			$group_data['setting-name'] = $setting_name;
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-input-group epkb-admin__input-field epkb-admin__icon-image-selection <?php echo esc_attr( $input_group_class ); ?>"
					id="<?php echo esc_attr( $setting_name ); ?>_group" <?php echo $group_data_escaped; ?>>
				<span class="epkb-main_label"></span>
				<div class="epkb-input-icon-image" id="<?php echo esc_attr( $setting_name ); ?>">
					<div class="epkb-input-icon-image__button <?php echo $image_url ? 'epkb-input-icon-image__button--have-image' : 'epkb-input-icon-image__button--no-image'; ?>"
						style="<?php echo $image_url ? 'background-image: url(' . $image_url . ');' : ''; ?>"
						data-title="<?php esc_attr_e( 'Choose Icon', 'echo-knowledge-base' ); ?>">
						<i class="epkbfa ep_font_icon_plus"></i>
						<i class="epkbfa epkbfa-pencil"></i>
					</div>
				</div>
				<input type="hidden" name="<?php echo esc_attr( $setting_name ); ?>" value="<?php echo esc_attr( $active_image_id ); ?>">
			</div>  <?php
		}

		if ( $setting_name == 'eprf_pro_description' ) {
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-admin__input-field <?php echo esc_attr( $input_group_class ); ?>" <?php echo $group_data_escaped; ?>>  <?php
				echo EPKB_HTML_Forms::pro_feature_ad_box( array(
					'title'             => sprintf( __( "Unlock %sArticle Ratings and Feedback%s By Upgrading to PRO ", 'echo-knowledge-base' ), '<strong>', '</strong>' ),
					'list'              => array( __('Let your readers rate the quality of your articles ', 'echo-knowledge-base'), __('Enable users to provide valuable feedback on your articles', 'echo-knowledge-base'),
												__('Gain insightful analytics on your most and least rated articles to fine-tune your content strategy', 'echo-knowledge-base') ),
					'btn_text'          => __( 'Upgrade Now', 'echo-knowledge-base' ),
					'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/',
					'return_html'       => true,
				) );    ?>
				<br />
			</div>  <?php
		}

		if ( in_array( $setting_name, [ 'ml_categories_articles_kblk_pro', 'ml_articles_list_kblk_pro' ] ) ) {
			EPKB_HTML_Elements::display_pro_description_field( array(
				'input_group_class'     => $input_group_class,
				'desc'                  => __( 'On the KB Main Page, display links to PDFs, documents, and pages.', 'echo-knowledge-base' ),
				'more_info_url'         => 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/',
				'more_info_text'        => __( 'Learn More', 'echo-knowledge-base' ),
				'group_data'            => $group_data,
			) );
		}

		if ( in_array( $setting_name, [ 'advanced_search_mp_filter_toggle_pro', 'advanced_search_ap_filter_toggle_pro' ] ) ) {
			EPKB_HTML_Elements::checkbox_toggle( [
				'text'        => __( 'Category Filter', 'echo-knowledge-base' ),
				'input_class' => 'epkb-admin__input-disabled',
				'is_pro_feature_ad'      => true,
				'pro_tooltip_args' => [
					'name'     => __( 'Category Filter', 'echo-knowledge-base' ),
					'title'    => sprintf( __("Unlock %sCategory Filter Feature%s", 'echo-knowledge-base'), '<strong>', '</strong>' ),
					'body'     => __( 'This PRO feature is part of the Advanced Search add-on.', 'echo-knowledge-base' ),
					'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
					'btn_url'  => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search',
				],
			] );
		}
		if ( in_array( $setting_name, [ 'advanced_search_mp_description_below_input_pro', 'advanced_search_ap_description_below_input_pro' ] ) ) {
			EPKB_HTML_Elements::textarea( [
				'label'       => __( 'Description Under Search Input', 'echo-knowledge-base' ),
				'input_class' => 'epkb-admin__input-disabled',
				'value'       => __( 'If you cannot find an answer, please contact us.', 'echo-knowledge-base' ),
				'is_pro_feature_ad'      => true,
				'pro_tooltip_args' => [
					'name'     => __( 'Description Under Search Input', 'echo-knowledge-base' ),
					'title'    => sprintf( __("Unlock %sDescription Under Search Input Feature%s", 'echo-knowledge-base'), '<strong>', '</strong>' ),
					'body'     => __( 'This PRO feature is part of the Advanced Search add-on..', 'echo-knowledge-base' ),
					'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
					'btn_url'  => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search',
				],
			] );
		}
		if ( in_array( $setting_name, [ 'advanced_search_mp_background_color_pro', 'advanced_search_ap_background_color_pro' ] ) ) {
			EPKB_HTML_Elements::color( [
				'label'       => __( 'Search Gradient Background', 'echo-knowledge-base' ),
				'value'       => '#f7941d',
				'input_class' => 'epkb-admin__input-disabled',
				'is_pro_feature_ad'      => true,
				'pro_tooltip_args' => [
					'name'     => __( 'Search Background', 'echo-knowledge-base' ),
					'title'    => sprintf(__("Unlock %sSearch Gradient Background Feature%s", 'echo-knowledge-base'), '<strong>', '</strong>'),
					'body'     => __( 'This PRO feature is part of the Advanced Search add-on..', 'echo-knowledge-base' ),
					'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
					'btn_url'  => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search',
				],
			] );
		}

		if ( in_array( $setting_name, [ 'ml_resource_links_1_settings_tab_content', 'ml_resource_links_2_settings_tab_content', 'ml_resource_links_3_settings_tab_content', 'ml_resource_links_4_settings_tab_content',
			'ml_resource_links_5_settings_tab_content', 'ml_resource_links_6_settings_tab_content', 'ml_resource_links_7_settings_tab_content', 'ml_resource_links_8_settings_tab_content', ] ) ) {

			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-admin__tab-content-field <?php echo esc_attr( $input_group_class ); ?>" <?php echo $group_data_escaped; ?>>    <?php
				foreach ( $field_spec['included_settings'] as $included_setting_name ) {
					$this->show_kb_setting_html( $included_setting_name, true );
				}   ?>
			</div>  <?php
		}

		if ( $setting_name == 'general_typography' ) {
			EPKB_HTML_Elements::custom_dropdown( [
				'label' => __( 'Font Family', 'echo-knowledge-base' ),
				'name' => 'general_typography_font_family',
				'specs' => '',
				'value' => $this->kb_config[$setting_name]['font-family'],
				'input_group_class' => $input_group_class,
				'options' => array_merge( array( 'Inherit' => 'Inherit' ) , array_combine( EPKB_Typography::get_google_fonts_family_list(), EPKB_Typography::get_google_fonts_family_list() ) ),
			] );
		}

		if ( $setting_name == 'ml_resource_links_settings_old_elay_warning' ) {
			$group_data_escaped = EPKB_HTML_Elements::get_data_escaped( $group_data );  ?>
			<div class="epkb-admin__input-field <?php echo esc_attr( $input_group_class ); ?>" <?php echo $group_data_escaped; ?>> <?php
				EPKB_HTML_Forms::notification_box_middle( array(
					'type' => 'error',
					'desc' => '<p>' . esc_html__( 'The Resource Links feature is supported in "KB - Elegant Layouts" add-on versions higher than 2.14.1.', 'echo-knowledge-base' ) .
						'<br>' . sprintf( esc_html__( 'Please %supgrade%s your "KB - Elegant Layouts" add-on to unlock this feature.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/" target="_blank">', '</a>' ) . '</p>',
				) );    ?>
			</div>  <?php
		}

		if ( $setting_name == 'sidebar_main_page_intro_text_link' ) {   ?>
			<div class="epkb-admin__input-field">
				<p>
					<?php esc_html_e( 'To Edit Sidebar Layout Introduction Text', 'echo-knowledge-base' ); ?>
					<a class="epkb-admin__form-tab-content-desc__link" href="<?php echo admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&page=epkb-kb-configuration#settings__labels' ); ?>"><?php esc_html_e( 'Click here', 'echo-knowledge-base' ); ?></a>
				</p>
			</div>  <?php
		}

		if ( $setting_name == 'article_search_sidebar_layout_msg' ) {   ?>
			<div class="epkb-admin__input-field ">
				<p>
					<?php echo sprintf( esc_html__( 'You have chosen the Sidebar layout. The Sidebar layout search box is controlled by the %ssearch settings on the Main Page%s.', 'echo-knowledge-base' ), '<a class="epkb-admin__form-tab-content-desc__link" href="#">', '</a>' ); ?>
				</p>
			</div>  <?php
		}

		if ( $setting_name == 'article_list_spacing' ) {
			$spacing_range = array(
				'4' => __( 'Compact', 'echo-knowledge-base' ),
				'6' => __( 'Standard', 'echo-knowledge-base' ) . ' ' . '#1',
				'8' => __( 'Standard', 'echo-knowledge-base' ) . ' ' . '#2',
				'10' => __( 'Standard', 'echo-knowledge-base' ) . ' ' . '#3',
				'12' => __( 'Spacious', 'echo-knowledge-base' ),
				'14' => __( 'Large', 'echo-knowledge-base' ),
			);
			$tooltip_body = esc_html__( 'Spacing between articles in a list', 'echo-knowledge-base' ) . ':<br>' .
				__( 'Compact', 'echo-knowledge-base' ) . ' 8px' . '<br>' .
				__( 'Standard', 'echo-knowledge-base' ) . ' 12/16/20px' . '<br>' .
				__( 'Spacious', 'echo-knowledge-base' ) . ' 24px' . '<br>' .
				__( 'Large', 'echo-knowledge-base' ) . ' 28px' . '<br>';
			if ( ! key_exists( $this->kb_config[$setting_name], $spacing_range ) ) {
				$spacing_range[$this->kb_config[$setting_name]] = __( 'Custom', 'echo-knowledge-base' ) . ' ' . ( 2 * $this->kb_config[$setting_name] );
			}
			EPKB_HTML_Elements::radio_buttons_horizontal( [
				'name'              => $setting_name,
				'specs'             => $setting_name,
				'group_data'        => $group_data,
				'input_group_class' => 'epkb-radio-horizontal-button-group-container' . ' ' . $input_group_class . ' ',
				'value'             => $this->kb_config[$setting_name],
				'options'           => $spacing_range,
				'tooltip_body'      => $tooltip_body,
			] );
		}
	}

	/**
	 * Get Quick Links box
	 *
	 * @return false|string
	 */
	private function get_quick_links_box() {

		ob_start(); ?>

		<div class="epkb-kb__btn-wrap"> <?php
			echo EPKB_Core_Utilities::get_current_kb_main_page_link( $this->kb_config, __( 'View My Knowledge Base', 'echo-knowledge-base' ), '' ); ?>
			<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
		</div>

		<div class="epkb-kb__btn-wrap"> <?php
			echo EPKB_Core_Utilities::get_kb_admin_page_link( '', __( 'Edit Articles', 'echo-knowledge-base' ), false ); ?>
			<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
		</div>

		<div class="epkb-kb__btn-wrap">
			<a href="<?php echo esc_url( admin_url( '/edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_config['id'] ) . '&post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) ) ); ?>">
				<?php echo __( 'Edit Categories', 'echo-knowledge-base' ); ?>
			</a>
			<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
		</div>  <?php

		if ( current_user_can( EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ) ) ) {   ?>
			<div class="epkb-kb__btn-wrap"> <?php
				echo EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-configuration&setup-wizard-on', __( 'Setup Wizard', 'echo-knowledge-base' ), false );  ?>
			</div>  <?php
		}

		return ob_get_clean();
	}

	/**
	 * Get Helpful Information box
	 *
	 * @param $helpful_info_box_config
	 * @return false|string
	 */
	private function get_helpful_info_box( $helpful_info_box_config ) {

		ob_start(); ?>

		<div class="epkb-admin__helpful-info-wrap"> <?php
			foreach ( $helpful_info_box_config as $item ) {    ?>
				<div class="epkb-admin__helpful-info-box">
					<div class="epkb-admin__helpful-info-box__title"><?php echo esc_html( $item['title'] ); ?></div>
					<div class="epkb-admin__helpful-info-box__icon-container">
						<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . esc_attr( $item['icon'] ) ); ?>">
					</div>
					<div class="epkb-admin__helpful-info-box__desc"><?php echo esc_html( $item['desc'] ); ?></div>
					<div class="epkb-admin__helpful-info-box__link-container">
						<a href="<?php echo esc_attr( $item['btn_url'] ); ?>" target="_blank"><?php echo esc_html( $item['btn_text'] ); ?></a>
					</div>
				</div>  <?php
			}   ?>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * KB Design: Box Editors List
	 *
	 * @return false|string
	 */
	private function show_frontend_editor_links() {

		$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $this->kb_config, '', '', '', false );

		ob_start();

		// Main page link to the Visual Editor
		if ( empty( $editor_urls['main_page_url'] ) ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title'         => __( 'Main Page', 'echo-knowledge-base' ),
				'content'       => __( 'No Main Page Found', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Add Shortcode', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . "&page=epkb-kb-configuration&wizard-global" ),
				'btn_target'    => "_blank",
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title'         => __( 'Main Page', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Change Style', 'echo-knowledge-base' ),
				'btn_url'       => $editor_urls['main_page_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-main-page-editor-link',
			) );
		}

		// Article page link to the Visual Editor
		if ( empty( $editor_urls['article_page_url'] ) ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/article-page.jpg',
				'title'         => __( 'Article Page', 'echo-knowledge-base' ),
				'content'       => __( 'All articles have no Category. Please assign your article to categories.', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Add New Article', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "post-new.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) ),
				'btn_target'    => "_blank",
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/article-page.jpg',
				'title'         => __( 'Article Page', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Change Style', 'echo-knowledge-base' ),
				'btn_url'       => $editor_urls['article_page_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-article-page-editor-link'
			) );
		}

		// Archive page link to the Visual Editor
		if ( $this->kb_config['templates_for_kb'] == 'current_theme_templates' ) {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title'         => __( 'Category Archive Page', 'echo-knowledge-base' ),
				'content'       => sprintf(  __( 'The KB template option is set to the Current Theme. You need to configure your Archive Page template in ' .
				                           'your theme settings. For details about the KB template option see %s', 'echo-knowledge-base' ),
					' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank">' . esc_html__( 'here', 'echo-knowledge-base' ) . '.' . '</a> ' )
			) );
		} else if ( $editor_urls['archive_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title'         => __('Category Archive Page', 'echo-knowledge-base'),
				'btn_text'      => __('Change Style', 'echo-knowledge-base'),
				'btn_url'       => $editor_urls['archive_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-archive-page-editor-link'
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title'         => __('Category Archive Page', 'echo-knowledge-base'),
				'content'       => __('No Categories Found', 'echo-knowledge-base'),
				'btn_text'      => __('Add New Category', 'echo-knowledge-base'),
				'btn_url'       => admin_url( 'edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_config['id'] ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) ),
				'btn_target'    => "_blank",
			) );
		}

		// Advanced Search Page
		if ( EPKB_Utilities::is_advanced_search_enabled() && $editor_urls['search_page_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/search-result-page.png',
				'title'         => __( 'Search Results Page', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Change Style', 'echo-knowledge-base' ),
				'btn_url'       => $editor_urls['search_page_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-search-page-editor-link'
			) );
		} else if ( EPKB_Utilities::is_advanced_search_enabled() ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style'         => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title'         => __( 'Search Results Page', 'echo-knowledge-base' ),
				'content'       => __( 'To edit the Search Results page, be sure you have a KB Main Page.', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Configure KB Main Page', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . "&page=epkb-kb-configuration#kb-url" ),
				'btn_target'    => "_blank",
			) );
		}

		return ob_get_clean();
	}

	/**
	 * Return configuration array of settings fields
	 *
	 * @return array
	 */
	private function get_contents_configs() {

		// Main Page
		if ( ! EPKB_Utilities::is_new_user( '11.30.0' ) || ! $this->is_modular_main_page ) {
			$contents_configs['main-page'] = array(
				array(
					'title'         => __( 'Modular Main Page', 'echo-knowledge-base' ),
					'desc'          => __( 'Modular Main page is composed of five rows. Each row can be search, categories and articles, list of popular articles and so on.', 'echo-knowledge-base' ),
					'read_more_url' => '#', // TODO: update link to relevant documentation page
					'read_more_text'=> __( 'Learn More', 'echo-knowledge-base' ),
					'css_class'     => 'epkb-admin__form-tab-content--modular_main_page_toggle-settings epkb-admin__form-tab-content--pro-tag',
					'fields'        => [
						'modular_main_page_toggle'   => '',
					],
				),
			);
		}

		// Archive Page
		$contents_configs['archive-page'] = array(
			array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'fields'    => [
					'template_category_archive_page_style' => '',
					'archive-content-width-v2' => '',
					'archive-show-sub-categories' => '',
					'archive-container-width-v2' => '',
					'archive-container-width-units-v2' => '',
				],
				'learn_more_links' => [ // title => url
					__( 'Category Archive Page', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/category-archive-page/',
					__( 'Additional Styling of Category Page', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/additional-styling-for-category-page/',
				],
			),
		);

		// Labels
		$contents_configs['labels'] = array(
			array(
				'title'         => __( 'Search Title - Main Page', 'echo-knowledge-base' ),
				'fields'        => [
					'search_title' => 'not_asea',
					'search_title_html_tag' => 'not_asea',
					'search_button_name' => 'not_asea',
				],
			),
			array(
				'title'     => __( 'Search Input Box - Main Page', 'echo-knowledge-base' ),
				'fields'    => [
					'search_box_hint' => '',
					'search_results_msg' => '',
					'no_results_found' => '',
					'min_search_word_size_msg' => '',
					'advanced_search_mp_title' => 'asea',
					'advanced_search_mp_description_below_title' => 'asea',
					'advanced_search_mp_description_below_input' => 'asea',
				],
			),
			array(
				'title'         => __( 'Search Title - Article Page', 'echo-knowledge-base' ),
				'fields'        => [
					'article_search_title' => 'not_asea',
					'article_search_title_html_tag' => 'not_asea',
					'article_search_button_name' => 'not_asea',
				],
			),
			array(
				'title'     => __( 'Search Input Box - Article Page', 'echo-knowledge-base' ),
				'fields'    => [
					'article_search_box_hint' => '',
					'article_search_results_msg' => '',
					'advanced_search_ap_title' => 'asea',
					'advanced_search_ap_description_below_title' => 'asea',
					'advanced_search_ap_description_below_input' => 'asea',
				],
			),
			array(
				'title'         => __( 'Tabs Layout Drop Down', 'echo-knowledge-base' ),
				'fields'    => [
					'choose_main_topic' => 'only_tabs',
				],
			),
			array(
				'title'     => __( 'Category Body', 'echo-knowledge-base' ),
				'fields'    => [
					'category_empty_msg' => 'not_grid',
					'grid_category_empty_msg' => [ 'elay', 'only_grid' ],
					'grid_category_link_text' => [ 'elay', 'only_grid' ],
					'grid_article_count_text' => [ 'elay', 'only_grid' ],
					'grid_article_count_plural_text' => [ 'elay', 'only_grid' ],
				],
			),
			array(
				'title'     => __( 'Articles', 'echo-knowledge-base' ),
				'fields'    => [
					'collapse_articles_msg' => '',
					'show_all_articles_msg' => '',
					'ml_categories_articles_back_button_text'   => [ [ 'only_classic', 'only_drill_down' ] ],
					'ml_categories_articles_show_more_text'     => [ [ 'only_classic', 'only_drill_down' ] ],
					'ml_categories_articles_article_text'       => [ [ 'only_classic', 'only_drill_down' ] ],
					'ml_categories_articles_articles_text'      => [ [ 'only_classic', 'only_drill_down' ] ],
				],
			),
			array(
				'title'     => __( 'Sidebar Articles', 'echo-knowledge-base' ),
				'fields'    => [
					'sidebar_collapse_articles_msg' => '',
					'sidebar_show_all_articles_msg' => '',
				],
			),
			array(
				'title'     => __( 'Navigation', 'echo-knowledge-base' ),
				'fields'    => [
					'sidebar_category_empty_msg' => '',
				],
			),
			array(
				'title'     => __( 'Categories List', 'echo-knowledge-base' ),
				'fields'    => [
					'category_focused_menu_heading_text' => 'only_categories',
				],
			),
			array(
				'title'     => __( 'TOC', 'echo-knowledge-base' ),
				'fields'    => [
					'article_toc_title' => '',
				],
			),
			array(
				'title'     => __( 'Breadcrumb', 'echo-knowledge-base' ),
				'fields'    => [
					'breadcrumb_description_text' => '',
					'breadcrumb_home_text' => '',
				],
			),
			array(
				'title'     => __( 'Back Navigation', 'echo-knowledge-base' ),
				'fields'    => [
					'back_navigation_text' => '',
				],
			),
			array(
				'title'     => __( 'Print Button', 'echo-knowledge-base' ),
				'fields'    => [
					'print_button_text' => '',
				],
				'data'      => [ 'target' => 'print_button' ]
			),
			array(
				'title'     => __( 'Created Date', 'echo-knowledge-base' ),
				'fields'    => [
					'created_on_text' => '',
				],
				'data'      => [ 'target' => 'created_date' ]
			),
			array(
				'title'     => __( 'Last Updated Date', 'echo-knowledge-base' ),
				'fields'    => [
					'last_updated_on_text' => '',
				],
				'data'      => [ 'target' => 'updated_date' ]
			),
			array(
				'title'     => __( 'Author', 'echo-knowledge-base' ),
				'fields'    => [
					'author_text' => '',
				],
				'data'      => [ 'target' => 'author' ]
			),
			array(
				'title'     => __( 'Article Views Counter', 'echo-knowledge-base' ),
				'fields'    => [
					'article_views_counter_text' => '',
				],
				'data'      => [ 'target' => 'views_counter' ]
			),
			array(
				'title'     => __( 'Prev/Next Navigation', 'echo-knowledge-base' ),
				'fields'    => [
					'prev_navigation_text' => '',
					'next_navigation_text' => '',
				],
			),
			array(
				'title'     => __( 'Archive', 'echo-knowledge-base' ),
				'fields'    => [
					'template_category_archive_page_heading_description' => '',
					'template_category_archive_read_more' => '',
				],
			),
			array(
				'title'     => __( 'Archive Meta Data', 'echo-knowledge-base' ),
				'fields'    => [
					'template_category_archive_date' => '',
					'template_category_archive_author' => '',
					'template_category_archive_categories' => '',
				],
			),
			array(
				'title'     => __( 'Resource Links Feature', 'echo-knowledge-base' ),
				'fields'    => [
					'ml_resource_links_container_title_text' => [ 'elay', 'only_modular_main_page' ],
					'ml_resource_links_container_description_text' => [ 'elay', 'only_modular_main_page' ],
				],
			),
			array(
				'title'     => __( 'Articles List Feature', 'echo-knowledge-base' ),
				'fields'    => [
					'ml_articles_list_title_text' => [ 'only_modular_main_page' ],
					'ml_articles_list_newest_articles_msg' => '',
					'ml_articles_list_popular_articles_msg' => '',
					'ml_articles_list_recent_articles_msg' => '',
				],
			),
			array(
				'title'     => __( 'FAQs Feature', 'echo-knowledge-base' ),
				'fields'    => [
					'ml_faqs_title_text' => [ 'only_modular_main_page' ],
				],
			),
			array(
				'title'         => __( 'Rating and Feedback', 'echo-knowledge-base' ),
				'fields'        => [
					'rating_text_value' => 'eprf',
					'rating_stars_text' => 'eprf',
					'rating_stars_text_1' => 'eprf',
					'rating_stars_text_2' => 'eprf',
					'rating_stars_text_3' => 'eprf',
					'rating_stars_text_4' => 'eprf',
					'rating_stars_text_5' => 'eprf',
					'rating_out_of_stars_text' => 'eprf',
					'rating_confirmation_positive' => 'eprf',
					'rating_confirmation_negative' => 'eprf',
					'rating_feedback_title' => 'eprf',
					'rating_feedback_required_title' => 'eprf',
					'rating_feedback_name' => 'eprf',
					'rating_feedback_email' => 'eprf',
					'rating_feedback_description' => 'eprf',
					'rating_feedback_support_link_text' => 'eprf',
					'rating_feedback_support_link_url' => 'eprf',
					'rating_feedback_button_text' => 'eprf',
					'rating_open_form_button_text' => 'eprf',
				],
			),
			array(
				'title'         => __( 'Sidebar Layout Introduction Page', 'echo-knowledge-base' ),
				'fields'        => [
					'sidebar_main_page_intro_text' => [ 'elay', 'only_sidebar' ],
				],
				'data'          => [ 'target' => 'sidebar_main_page_intro_text' ],
			),
		);

		return $contents_configs;
	}

	/**
	 * Return configuration array of sub contents settings fields
	 *
	 * @return array
	 */
	private function get_sub_contents_configs() {

		$sub_contents_configs = array(

			// KB Main Page
			'main-page-ml-row-1' => [],
			'main-page-ml-row-2' => [],
			'main-page-ml-row-3' => [],
			'main-page-ml-row-4' => [],
			'main-page-ml-row-5' => [],

			// KB Article Page
			'article-page-settings' => [
				array(
					'title'     => __( 'Article Features - Top', 'echo-knowledge-base' ),
					'fields'    => [
						'article_content_enable_article_title' => '',
						'article_content_enable_author' => '',
						'article_content_enable_last_updated_date' => '',
						'article_content_enable_created_date' => '',
						'print_button_enable' => '',
						'article_content_enable_views_counter' => '',
					]
				),
				array(
					'title'     => __( 'Article Features - Bottom', 'echo-knowledge-base' ),
					'fields'    => [
						'meta-data-footer-toggle' => '',
						'last_updated_on_footer_toggle' => '',
						'created_on_footer_toggle' => '',
						'author_footer_toggle' => '',
						'article_views_counter_footer_toggle' => '',
						'articles_comments_global' => '',
					]
				),
				array(
					'title'     => __( 'Breadcrumbs', 'echo-knowledge-base' ),
					'fields'    => [
						'breadcrumb_enable' => '',
						'breadcrumb_icon_separator' => '',
					],
					'data'      => [ 'target' => 'breadcrumb' ],
				),
				array(
					'title'     => __( 'Back Navigation', 'echo-knowledge-base' ),
					'fields'    => [
						'article_content_enable_back_navigation' => '',
						'back_navigation_mode' => '',
					],
					'data'      => [ 'target' => 'back_navigation' ]
				),
				array(
					'title'     => __( 'Prev/Next Navigation', 'echo-knowledge-base' ),
					'fields'    => [
						'prev_next_navigation_enable' => '',
						'prev_next_navigation_bg_color' => '',
						'prev_next_navigation_hover_text_color' => '',
						'prev_next_navigation_hover_bg_color' => '',
					],
					'data'      => [ 'target' => 'prev_next_navigation' ]
				),
				array(
					'title'     => __( 'Article Views Counter', 'echo-knowledge-base' ),
					'fields'    => [
						'article_views_counter_enable' => '',
						'article_views_counter_method' => '',
					],
					'data'      => [ 'target' => 'article_views_counter' ],
					'desc' => __( 'The counter provides an approximate measure of user views, but bot-generated views cannot be fully filtered out. ' .
						'Comparing view counts between articles can be more practical than relying on absolute view counts.',  'echo-knowledge-base' ),
				),
				array(
					'title'         => __( 'Widgets/Shortcodes', 'echo-knowledge-base' ),
					'fields'        => [
						'widg_search_results_limit' => 'widg',
					],
					'requirement'     => 'widg',
				)
			],
			'article-page-sidebar' => [
				array(
					'title'     => __( 'Left Sidebar', 'echo-knowledge-base' ),
					'fields'    => [
						'article-left-sidebar-toggle' => '',
						'article_nav_sidebar_type_left' => '',
						'nav_sidebar_left' => '',
						'kb_sidebar_left_toggler' => '',
						'kb_sidebar_left' => '',
					],
					'data'      => [ 'target' => 'left_sidebar' ],
				),
				array(
					'title'     => __( 'Right Sidebar', 'echo-knowledge-base' ),
					'fields'    => [
						'article-right-sidebar-toggle' => '',
						'article_nav_sidebar_type_right' => '',
						'nav_sidebar_right' => '',
						'kb_sidebar_right_toggler' => '',
						'kb_sidebar_right' => '',
					],
					'data'      => [ 'target' => 'right_sidebar' ],
				),
				array(
					'title'     => __( 'Categories and Articles Navigation', 'echo-knowledge-base' ),
					'fields'    => [
						'sidebar_top_categories_collapsed' => '',
						'sidebar_expand_articles_icon' => '',
						'sidebar_section_head_font_color' => '',
						'sidebar_section_category_icon_color' => '',
						'sidebar_section_category_font_color' => '',
						'sidebar_side_bar_height_mode' => '',
						'sidebar_side_bar_height' => '',
					],
					'dependency'    => [ 'article_nav_sidebar_type_left', 'article_nav_sidebar_type_right' ],
					'enable_on'     => [ 'eckb-nav-sidebar-v1' ],
				),
				array(
					'title'     => __( 'Top Categories Navigation', 'echo-knowledge-base' ),
					'fields'    => [
						'category_box_title_text_color' => 'only_categories',
						'category_box_container_background_color' => '',
						'category_box_category_text_color' => 'only_categories',
						'category_box_count_background_color' => '',
						'category_box_count_text_color' => '',
						'category_box_count_border_color' => '',
					],
					'dependency'    => [ 'article_nav_sidebar_type_left', 'article_nav_sidebar_type_right' ],
					'enable_on'     => [ 'eckb-nav-sidebar-categories' ],
				),
				array(
					'title'         => __( 'Categories List', 'echo-knowledge-base' ),
					'fields'        => [
						'categories_layout_list_mode' => 'only_categories',
					],
					'dependency'    => [ 'article_nav_sidebar_type_left', 'article_nav_sidebar_type_right' ],
					'enable_on'     => [ 'eckb-nav-sidebar-categories' ],
				),
			],
			'article-page-toc' => [
				array(
					'title'     => __( 'Location', 'echo-knowledge-base' ),
					'desc'      => '',
					'fields'    => [
						'toc_toggler'   => '',
						'toc_locations' => '',  // is internally using by Settings UI
						'toc_left'      => '',
						'toc_content'   => '',
						'toc_right'     => '',
					]
				),
				array(
					'title'     => __( 'Header Range', 'echo-knowledge-base' ),
					'fields'    => [
						'article_toc_hx_level' => '',
						'article_toc_hy_level' => '',
					],
				),
				array(
					'title'     => __( 'Title', 'echo-knowledge-base' ),
					'fields'    => [
						'article_toc_title_color' => '',
						'article_toc_background_color' => '',
						'article_toc_border_color' => '',
					],
				),
				array(
					'title'     => __( 'Headings', 'echo-knowledge-base' ),
					'fields'    => [
						'article_toc_text_color' => '',
						'article_toc_active_bg_color' => '',
						'article_toc_active_text_color' => '',
						'article_toc_cursor_hover_bg_color' => '',
						'article_toc_cursor_hover_text_color' => '',
					],
				),
			],
			'article-page-ratings' => [
				array(
					'title'     => __( 'User Rating and Feedback', 'echo-knowledge-base' ),
					'fields'    => [
						'eprf_pro_description' => 'not_eprf',
						'article_content_enable_rating_element' => 'eprf',
						'rating_mode' => 'eprf',
						'rating_feedback_name_prompt' => 'eprf',
						'rating_feedback_email_prompt' => 'eprf',
						'rating_text_color' => 'eprf',
						'rating_feedback_button_color' => 'eprf',
					]
				),
				array(
					'title'     => __( 'User Rating - Stars Mode', 'echo-knowledge-base' ),
					'fields'    => [
						'rating_layout' => 'eprf',
						'rating_feedback_trigger_stars' => 'eprf',
						'rating_feedback_required_stars' => 'eprf',
						'rating_element_size' => 'eprf',
						'rating_element_color' => 'eprf',
					],
					'dependency'    => [ 'rating_mode' ],
					'enable_on'     => [ 'eprf-rating-mode-five-stars' ],
				),
				array(
					'title'     => __( 'User Rating - Like/Dislike Mode', 'echo-knowledge-base' ),
					'fields'    => [
						'rating_like_style' => 'eprf',
						'rating_feedback_trigger_like' => 'eprf',
						'rating_feedback_required_like' => 'eprf',
						'rating_like_color' => 'eprf',
						'rating_dislike_color' => 'eprf',
					],
					'dependency'    => [ 'rating_mode' ],
					'enable_on'     => [ 'eprf-rating-mode-like-dislike' ],
				),
				array(
					'title'     => __( 'Top Statistics', 'echo-knowledge-base' ),
					'fields'    => [
						'article_content_enable_rating_stats' => 'eprf',
					],
				),
				array(
					'title'     => __( 'Bottom Statistics', 'echo-knowledge-base' ),
					'fields'    => [
						'rating_stats_footer_toggle' => 'eprf',
					],
					'dependency'    => [ 'meta-data-footer-toggle' ],
					'enable_on'     => [ 'on' ]
				),
				array(
					'title'     => __( 'Open Feedback Button', 'echo-knowledge-base' ),
					'fields'    => [
						'rating_open_form_button_enable' => 'eprf',
						'rating_open_form_button_color' => 'eprf',
						'rating_open_form_button_color_hover' => 'eprf',
						'rating_open_form_button_background_color' => 'eprf',
						'rating_open_form_button_background_color_hover' => 'eprf',
						'rating_open_form_button_border_color' => 'eprf',
						'rating_open_form_button_border_color_hover' => 'eprf',
						'rating_open_form_button_border_radius' => 'eprf',
						'rating_open_form_button_border_width' => 'eprf',
					],
				),
			],

			// General
			'general-settings' => [
				array(
					'title'     => __( 'Theme Compatibility Mode', 'echo-knowledge-base' ),
					'desc'      => '',
					'fields'    => [
						'theme_compatibility_mode' => '',   // is internally using by Settings UI
						'templates_for_kb' => '',
					],
					'learn_more_links' => [ // title => url
						__( 'Current Theme Template vs KB Template', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/',
					],
				),
				array(
					'title'     => __( 'Typography', 'echo-knowledge-base' ),
					'desc'      => '',
					'fields'    => [
						'general_typography' => '',
						'typography_message' => '',   // is internally using by Settings UI
					],
					'learn_more_links' => [ // title => url
						__( 'Typography - Font Family, Size, Weight', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/typography-font-family-size-weight/',
					],
				),
				array(
					'title'     => __( 'FAQs Shortcode', 'echo-knowledge-base' ),
					'desc'      => '',
					'fields'    => [
						'faq_shortcode_content_mode' => '',
					],
				),
				array(
					'title'     => __( 'KB Nickname', 'echo-knowledge-base' ),
					'desc'      => __( 'Give your Knowledge Base a name. The name will show when we refer to it or when you see a list of post types.', 'echo-knowledge-base' ),
					'fields'    => [
						'kb_name' => '',
					],
				),
				array(
					'title'             => __( 'Custom CSS', 'echo-knowledge-base' ),
					'fields'            => [
						'epkb_ml_custom_css' => 'only_modular_main_page',
					],
				),
			],
			'general-full-editor' => [
				array(
					'title'     => __( 'Visual Editor Launch Mode', 'echo-knowledge-base' ),
					'desc'      => __( 'This toggle controls how the buttons above open the Editor. The Editor can be shown either on the frontend or backend. ' .
						'If you experience compatibility issues on the frontend, switch the Editor to the backend and vice versa.', 'echo-knowledge-base' ),
					'fields'    => [
						'editor_backend_mode' => '',    // is storing in KB flags
					],
					'tooltip_external_links' => [
						[ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/kb-visual-editor/' ]
					]
				),
			],
		);

		// Modular Main Page
		if ( $this->is_modular_main_page ) {
			return $this->add_modular_sub_contents_configs( $sub_contents_configs );
		}

		// NOT Modular Main Page version of Search Box for Main Page
		$sub_contents_configs['main-page-ml-row-1'] = array(
			array(
				'title'         => __( 'Search Box Presets', 'echo-knowledge-base' ),
				'fields'        => [
					'advanced_search_mp_presets' => 'asea',
				],
			),
			array(
				'title'         => __( 'Main Page Search Box', 'echo-knowledge-base' ),
				'fields'        => [

					'width' => 'not_modular_main_page',

					'advanced_search_mp_box_visibility' => 'asea',
					'advanced_search_mp_input_box_search_icon_placement' => 'asea',
					'advanced_search_mp_filter_toggle' => 'asea',

					'search_box_input_width' => [ 'not_asea', 'not_modular_main_page' ],
					'search_layout' => [ 'not_asea', 'not_modular_main_page' ],
					'search_title_font_color' => [ 'not_asea', 'not_modular_main_page' ],
					'search_background_color' => [ 'not_asea', 'not_modular_main_page' ],
					'search_text_input_background_color' => [ 'not_asea', 'not_modular_main_page' ],
					'search_btn_background_color' => [ 'not_asea', 'not_modular_main_page' ],

					'advanced_search_mp_filter_toggle_pro' => 'not_asea',
					'advanced_search_mp_description_below_input_pro' => 'not_asea',
					'advanced_search_mp_background_color_pro' => 'not_asea',

					'advanced_search_mp_title_toggle' => 'asea',
					'advanced_search_mp_title' => 'asea',
					'advanced_search_mp_title_font_color' => 'asea',
					'advanced_search_mp_description_below_title_toggle' => 'asea',
					'advanced_search_mp_description_below_title' => 'asea',
					'advanced_search_mp_description_below_input_toggle' => 'asea',
					'advanced_search_mp_description_below_input' => 'asea',
					'advanced_search_mp_filter_category_level' => 'asea',
					'advanced_search_mp_show_top_category' => 'asea',
					'advanced_search_mp_results_list_size' => 'asea',
					'advanced_search_mp_link_font_color' => 'asea',
					'advanced_search_mp_background_color' => 'asea',

					'advanced_search_mp_background_gradient_toggle' => 'asea',
					'advanced_search_mp_background_gradient_from_color' => 'asea',
					'advanced_search_mp_background_gradient_to_color' => 'asea',
					'advanced_search_mp_background_gradient_degree' => 'asea',
					'advanced_search_mp_background_gradient_opacity' => 'asea',
				],
				'data'          => [ 'target' => 'advanced_search_mp' ],
			),
			array(
				'title'         => __( 'Search Results Page', 'echo-knowledge-base' ),
				'fields'        => [
					'advanced_search_mp_auto_complete_wait' => 'asea',
					'advanced_search_mp_results_page_size' => 'asea',
					'advanced_search_results_meta_created_on_toggle' => 'asea',
					'advanced_search_results_meta_author_toggle' => 'asea',
					'advanced_search_results_meta_categories_toggle' => 'asea'
				],
				'data'          => [ 'target' => 'advanced_search_results_page' ],
			),
		);

		// NOT Modular Main Page version of Settings boxes for 'old' layouts: Basic, Tabs, Category, Grid, Sidebar
		$sub_contents_configs['main-page-ml-row-2'] = array(
			array(
				'title'         => __( 'Layout', 'echo-knowledge-base' ),
				'fields'        => [
					'kb_main_page_layout' => '',
				],
				'css_class'     => 'epkb-admin__form-tab-content--layout',
			),
			array(
				'title'         => __( 'Page', 'echo-knowledge-base' ),
				'fields'        => [
					'template_main_page_display_title' => [ 'only_kb_templates', 'not_classic', 'not_drill_down' ],
					'grid_nof_columns' => [ 'elay', 'only_grid' ],
					'nof_columns' => [ 'not_grid', 'not_sidebar' ],
					'background_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],   // OR condition
					'ml_categories_articles_kblk_pro' => 'not_kblk',
				],
			),
			array(
				'title'         => __( 'Tabs', 'echo-knowledge-base' ),
				'fields'        => [
					'tab_nav_font_color' => 'only_tabs',
					'tab_nav_active_font_color' => 'only_tabs',
					'tab_nav_active_background_color' => 'only_tabs',
					'tab_nav_background_color' => 'only_tabs',
				],
				'learn_more_links' => [ // title => url
					__( 'Using Tabs Layout', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/using-tabs-layout/',
				]
			),
			array(
				'title'         => __( 'Categories Box', 'echo-knowledge-base' ),
				'fields'        => [
					'grid_section_box_height_mode' => [ 'elay', 'only_grid' ], // AND condition
					'grid_section_body_height' => [ 'elay', 'only_grid' ],
					'grid_section_box_shadow' => [ 'elay', 'only_grid' ],
					'section_box_height_mode' => [ 'not_grid', 'not_sidebar' ],
					'section_body_height' => [ 'not_grid', 'not_sidebar' ],
					'section_border_color' => [ 'not_grid', 'not_sidebar' ],
					'section_border_radius' => [ 'not_grid', 'not_sidebar' ],
					'section_border_width' => [ 'not_grid', 'not_sidebar' ],
				],
				'learn_more_links' => [ // title => url
					__( 'Categories additional styles', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/additional-customization-of-kb-main-page/#articleTOC_0',
				]
			),
			array(
				'title'     => __( 'Category Header', 'echo-knowledge-base' ),
				'fields'    => [
					'grid_section_head_alignment' => [ 'elay', 'only_grid' ],
					'section_hyperlink_text_on' => [ 'elay', 'only_grid' ],     // if Grid category click goes to archive page or first article in the category
					'grid_section_head_font_color' => [ 'elay', 'only_grid' ],  // category name color
					'grid_section_desc_text_on' => [ 'elay', 'only_grid' ],     // category description on/off
					'grid_section_head_description_font_color' => [ 'elay', 'only_grid' ],  // category description color
					'grid_section_divider' => [ 'elay', 'only_grid' ],
					'grid_section_divider_color' => [ 'elay', 'only_grid' ],
					'grid_section_head_background_color' => [ 'elay', 'only_grid' ],

					// core layouts
					'section_head_alignment' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_hyperlink_on' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],     // link to category archive page
					'section_head_font_color' => [ 'not_grid', 'not_sidebar' ],     // Category Name Color
					'section_desc_text_on' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_head_description_font_color' => [ 'not_grid', 'not_sidebar' ],
					'section_divider' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_divider_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_head_background_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
				],
			),
			array(
				'title'     => __( 'Category Body', 'echo-knowledge-base' ),
				'fields'    => [
					'grid_section_article_count' => [ 'elay', 'only_grid' ],
					'grid_section_body_background_color' => [ 'elay', 'only_grid' ],
					'section_body_background_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
				],
			),
			array(
				'title'         => __( 'Category Icons', 'echo-knowledge-base' ),
				'fields'        => [
					'grid_category_icon_location' => [ 'elay', 'only_grid' ],
					'grid_section_icon_size' => [ 'elay', 'only_grid' ],
					'grid_section_head_icon_color' => [ 'elay', 'only_grid' ],
					'section_head_category_icon_location' => [ 'not_grid', 'not_sidebar' ],
					'section_head_category_icon_size' => [ 'not_grid', 'not_sidebar' ],
					'section_head_category_icon_color' => [ 'not_grid', 'not_sidebar' ],    // Category Icon
					'section_category_icon_color' => [ 'not_grid', 'not_sidebar' ], // Subcategory Expand Icon
					'section_category_font_color' => [ 'not_grid', 'not_sidebar' ], // Subcategory Text
				],
				'learn_more_links' => [ // title => url
					__( 'Set Image and Font Icons for Categories', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/how-do-you-change-icons-for-the-categories/',
				],
				'data'      => [ 'target' => 'category_icon' ]
			),
			array(
				'title'         => __( 'List of Articles', 'echo-knowledge-base' ),
				'fields'        => [
					'nof_articles_displayed' => [ 'not_grid', 'not_sidebar' ],
					'section_box_shadow' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'expand_articles_icon' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'article_icon_color' => [ 'not_grid', 'not_sidebar' ],                          // article icon color
					'sidebar_article_icon_color' => [ 'elay', 'only_sidebar' ],                     // article icon color
					'article_font_color' => [ 'not_grid', 'not_sidebar' ],                          // article titles color
					'grid_section_body_text_color' => [ 'elay', 'only_grid' ],                      // article titles color
					'sidebar_article_font_color' => [ 'elay', 'only_sidebar' ],                     // article titles color
				],
			),
			array(
				'title'         => __( 'Sidebar Layout Introduction Page', 'echo-knowledge-base' ),
				'fields'        => [
					'sidebar_main_page_intro_text_link' => [ 'elay', 'only_sidebar' ],
				],
				'css_class'     => 'epkb-admin__form-tab-content--sidebar_main_page_intro_text',
			),
		);

		// NOT Modular Main Page version of Settings boxes for 'old' layouts: Grid, Sidebar ONLY if Elegant Layouts is disabled (overrides the above config for 'main-page-ml-row-1')
		if ( ! $this->elay_enabled && $this->is_elay_layout ) {
			$sub_contents_configs['main-page-ml-row-2'] = array(
				array(
					'title'         => __( 'Layout', 'echo-knowledge-base' ),
					'fields'        => [
						'kb_main_page_layout'   => '',
					],
					'css_class'     => 'epkb-admin__form-tab-content--layout',
				),
				array(
					'title'     => __( 'Settings', 'echo-knowledge-base' ),
					'fields'    => [
						'ml_categories_articles_kblk_pro' => 'not_kblk',
					],
					'css_class' => 'epkb-admin__form-tab-content--module-settings',
				),
			);
		}

		// for Sidebar layout show message that Article Page search is controlled by Main Page search settings
		if ( $this->is_sidebar_layout ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title' => __( 'Article Page Search Box', 'echo-knowledge-base' ),
					'fields' => [
						'article_search_sidebar_layout_msg' => '',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
			];
		}
		// if Article Page search is synced with Main Page then show only the toggle; Sidebar Layout is always synced
		if ( ! $this->is_sidebar_layout && $this->kb_config['article_search_sync_toggle'] == 'on' ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title' => __( 'Article Page Search Box', 'echo-knowledge-base' ),
					'fields' => [
						'article_search_sync_toggle' => '',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
			];
		}
		// if Article Page search is NOT synced with Main Page then show all Article Page settings; Sidebar Layout is always synced
		if ( ! $this->is_sidebar_layout && $this->kb_config['article_search_sync_toggle'] != 'on' ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title'         => __( 'Search Box Presets', 'echo-knowledge-base' ),
					'fields'        => [
						'advanced_search_ap_presets' => 'asea',
					],
				),
				array(
					'title' => __('Article Page Search Box', 'echo-knowledge-base'),
					'fields' => [
						'article_search_sync_toggle' => '',

						'advanced_search_ap_box_visibility' => 'asea',
						'advanced_search_ap_input_box_search_icon_placement' => 'asea',
						'advanced_search_ap_filter_toggle' => 'asea',

						'article_search_box_input_width' => ['not_asea'],
						'article_search_layout' => ['not_asea'],
						'article_search_title_font_color' => ['not_asea'],
						'article_search_background_color' => ['not_asea'],
						'article_search_text_input_background_color' => ['not_asea'],
						'article_search_btn_background_color' => ['not_asea'],
						'article_search_text_input_border_color' => ['not_asea'],
						'article_search_btn_border_color' => ['not_asea'],

						'advanced_search_ap_filter_category_level' => 'asea',
						'advanced_search_ap_show_top_category' => 'asea',
						'advanced_search_ap_results_list_size' => 'asea',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
				array(
					'title' => __('Article Page Search Labels Box', 'echo-knowledge-base'),
					'fields' => [
						'advanced_search_ap_title_toggle' => 'asea',
						'advanced_search_ap_title' => 'asea',
						'advanced_search_ap_title_font_color' => 'asea',
						'advanced_search_ap_description_below_title_toggle' => 'asea',
						'advanced_search_ap_description_below_title' => 'asea',
						'advanced_search_ap_description_below_input_toggle' => 'asea',
						'advanced_search_ap_description_below_input' => 'asea',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
				array(
					'title' => __('Article Page Search Style Box', 'echo-knowledge-base'),
					'fields' => [
						'advanced_search_ap_link_font_color' => 'asea',
						'advanced_search_ap_background_color' => 'asea',
						'advanced_search_ap_background_gradient_toggle' => 'asea',
						'advanced_search_ap_background_gradient_from_color' => 'asea',
						'advanced_search_ap_background_gradient_to_color' => 'asea',
						'advanced_search_ap_background_gradient_degree' => 'asea',
						'advanced_search_ap_background_gradient_opacity' => 'asea',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
			];
		}

		return $sub_contents_configs;
	}

	/**
	 * Return configuration array of helpful information box
	 *
	 * @return array
	 */
	private function get_helpful_info_box_config() {

		$list_configs = array();

		$list_configs[] = array(
			'title'    => __( 'Getting Started', 'echo-knowledge-base' ),
			'desc'     => __( 'Set up your Knowledge Base name, url, and design', 'echo-knowledge-base' ),
			'icon'     => 'img/need-help/rocket-2.jpg',
			'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'btn_url'  => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&page=epkb-kb-need-help' ),
		);
		$list_configs[] = array(
			'title'    => __( 'Explore Features', 'echo-knowledge-base' ),
			'desc'     => __( 'Get familiar with features and how they function', 'echo-knowledge-base' ),
			'icon'     => 'img/need-help/mountain-flag.jpg',
			'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'btn_url'  => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&page=epkb-kb-need-help#features__design' ),
		);
		$list_configs[] = array(
			'title'    => __( 'Online Documentation', 'echo-knowledge-base' ),
			'desc'     => __( 'Read our detailed documentation about all KB features.', 'echo-knowledge-base' ),
			'icon'     => 'img/need-help/education-hat.jpg',
			'btn_text' => __( 'Learn More', 'echo-knowledge-base' ),
			'btn_url'  => 'https://www.echoknowledgebase.com/documentation/',
		);
		$list_configs[] = array(
			'title'    => __( 'Contact Us', 'echo-knowledge-base' ),
			'desc'     => __( 'Support question for something that is not working correctly', 'echo-knowledge-base' ),
			'icon'     => 'img/need-help/mail.jpg',
			'btn_text' => __( 'Ask a Question', 'echo-knowledge-base' ),
			'btn_url'  => admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&page=epkb-kb-need-help#contact-us' ),
		);

		return $list_configs;
	}

	/**
	 * Adjust field specification when display it on Settings
	 *
	 * @param $field_specs
	 *
	 * @return array
	 * @noinspection PhpUnusedParameterInspection*/
	private function set_custom_field_specs( $field_specs ) {

		// overwrite existing or add additional data
		$current_kb_id = EPKB_KB_Handler::get_current_kb_id();
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $current_kb_id );

		switch ( $field_specs['name'] ) {

			case 'section_head_category_icon_size':
				$field_specs['dependency'] = ['section_head_category_icon_location'];
				$field_specs['enable_on'] = ['top', 'left', 'right'];
				$field_specs['desc'] = '<a class="epkb-admin__input-field-desc" href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_config['id'] ) .
						'&post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) ) ) . '" target="_blank">' . esc_html__( 'Edit Categories Icons', 'echo-knowledge-base' ) . '</a>';
				break;

			case 'width':
				if ( $kb_config['templates_for_kb'] ==  'current_theme_templates' ) {
					$field_specs['desc'] = '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' .
						esc_html__( 'We have detected that you are using the Current Theme Template option. If your width is not expanding the way you want, it is because the theme is controlling the total width. ' .
							'You have two options: either switch to the KB Template option or check your theme settings to expand the width.', 'echo-knowledge-base' ) .
						' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank"><span class="epkbfa epkbfa-external-link"></span></a></div>';
				}
				break;

			case 'section_head_category_icon_color':
				$field_specs['dependency'] = ['section_head_category_icon_location'];
				$field_specs['enable_on'] = ['top', 'left', 'right'];
				break;

			case 'section_head_description_font_color':
				$field_specs['dependency'] = ['section_desc_text_on'];
				$field_specs['enable_on'] = ['on'];
				break;

			case 'article_nav_sidebar_type_left':
			case 'article_nav_sidebar_type_right':
			case 'rating_mode':
			case 'section_head_category_icon_location':
			case 'section_desc_text_on':
				$field_specs['input_group_class'] = 'eckb-conditional-setting-input' . ' ';
				break;

			case 'rating_stats_footer_toggle':
				$field_specs['label'] = esc_html__( 'Bottom Rating Statistics', 'echo-knowledge-base' );
				break;

			case 'modular_main_page_toggle':
				if ( $this->is_old_elay && $this->is_elay_layout ) {
					$field_specs['input_group_class'] = 'epkb-input-group--disabled';
				}
				break;

			default:
				break;
		}

		$field_specs = $this->set_custom_modular_field_specs( $field_specs );

		// set divider line after specified setting fields
		/* if ( in_array( $field_specs['name'], [ 'section_border_color',
				'ml_resource_links_icon_color',
				'ml_resource_links_container_text_alignment',
        ] ) ) {
			$field_specs['input_group_class'] = empty( $field_specs['input_group_class'] )
				? 'epkb-admin__field-divider-after' . ' '
				: $field_specs['input_group_class'] . 'epkb-admin__field-divider-after' . ' ';
		} */

		// Optionally (for certain Layouts only) set divider line after specified setting fields
		/* if ( in_array( $this->kb_config['kb_main_page_layout'], [ 'Classic', 'Drill-Down' ] ) && in_array( $field_specs['name'], [ 'section_head_description_font_color' ] ) ) {
			$field_specs['input_group_class'] = empty( $field_specs['input_group_class'] )
				? 'epkb-admin__field-divider-after' . ' '
				: $field_specs['input_group_class'] . 'epkb-admin__field-divider-after' . ' ';
		} */

		// set divider line before specified setting fields
		if ( in_array( $field_specs['name'], [ 'ml_resource_links_description_text_alignment' ] ) ) {
			$field_specs['input_group_class'] = empty( $field_specs['input_group_class'] )
				? 'epkb-admin__field-divider-before' . ' '
				: $field_specs['input_group_class'] . 'epkb-admin__field-divider-before' . ' ';
		}

		return $field_specs;
	}

	/**
	 * Set tooltip for input field
	 *
	 * @param $input_args
	 *
	 * @return array
	 * @noinspection PhpUnusedParameterInspection*/
	private function set_input_tooltip( $input_args ) {

		switch ( $input_args['specs'] ) {

			case 'ml_categories_articles_sidebar_desktop_width':
				$input_args['tooltip_body'] =  __( 'The width of the sidebar is determined by the Row Width setting. If the Row Width is set to 100%, the Sidebar Width can be assigned, for example, as 30%,
						 leaving the remaining 70% for the rest of the row. <br/><br/>Similarly, if the Row Width is set to 1000px, the Sidebar Width is set in pixels.<br/>
						<a target="_blank" href="https://www.echoknowledgebase.com/documentation/modular-layout/">Learn More</a>', 'echo-knowledge-base' );
				break;

			case 'ml_categories_articles_sidebar_toggle':
				$input_args['tooltip_body'] =  __( 'If the sidebar is enabled and it causes the categories to become visually compressed, consider setting the number of columns to two.', 'echo-knowledge-base' );
				break;

			// Article Features - Top
			case 'article_content_enable_article_title': // Article Title
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-title/' ] ];
				break;
			case 'article_content_enable_author': // Author
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'article_content_enable_last_updated_date': // Last Updated Date
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'article_content_enable_created_date': // Created Date
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'print_button_enable': // Print Button
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/print-button/' ] ];
				break;
			case 'article_content_enable_views_counter': // Article Views Counter
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-views-counter/' ] ];
				break;

			// Article Features - Bottom
//			case 'meta-data-footer-toggle': // Meta Data at the Bottom
//				$input_args['tooltip_body'] =  __( '<div class="epkb__option-tooltip__body__external_link">
//						<a target="_blank" href="">Learn More</a><span class="epkbfa epkbfa-external-link"></span>
//						</div>', 'echo-knowledge-base' );
//				break;
			case 'last_updated_on_footer_toggle': // Last Updated On
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'created_on_footer_toggle': // Created On
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'author_footer_toggle': // Author
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/created-on-updated-on-author-meta/' ] ];
				break;
			case 'article_views_counter_footer_toggle': // Article Views Counter
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-views-counter/' ] ];
				break;
			case 'articles_comments_global': // Comments
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/wordpress-article-comments/' ] ];
				break;

			//Breadcrumbs
			case 'breadcrumb_enable': // Breadcrumbs
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-breadcrumb/' ] ];
				break;

			//Prev/Next Navigation
			case 'prev_next_navigation_enable': // Prev/Next Navigation
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/previous-next-page-navigation/' ] ];
				break;

			//Article Views Counter
			case 'article_views_counter_enable': // Count Article Views
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-views-counter/' ] ];
				break;

			//Left Sidebar
			case 'article-left-sidebar-toggle': // Left Sidebar
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-sidebars/' ] ];
				break;

			//Right Sidebar
			case 'article-right-sidebar-toggle': // Right Sidebar
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-sidebars/' ] ];
				break;

			//FAQs Shortcode
			case 'faq_shortcode_content_mode': // Content Mode
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/faqs-shortcode/' ] ];
				break;

			//User Rating and Feedback
			case 'article_content_enable_rating_element': // User Rating and Feedback
				$input_args['tooltip_external_links'] = [ [ 'link_text' => __('Learn More', 'echo-knowledge-base'), 'link_url' => 'https://www.echoknowledgebase.com/documentation/article-rating-feedback-overview/' ] ];
				break;

			case 'ml_resource_links_container_title_text':
			case 'ml_articles_list_title_text':
			case 'ml_faqs_title_text':
				$input_args['tooltip_body'] =  __( 'Leave empty to hide the title.', 'echo-knowledge-base' );
				break;

			case 'article_search_sync_toggle':
				$input_args['tooltip_body'] =  __( 'When the toggle is ON, then Article Page Search uses Main Page Search settings.', 'echo-knowledge-base' );
				break;

			default:
				break;
		}

		return $input_args;
	}

	/**
	 * Return only corresponding settings in the given list
	 *
	 * @param $settings_list
	 * @return mixed
	 */
	private function filter_settings( $settings_list ) {

		foreach ( $settings_list as $setting_name => $requirements ) {

			// always include field even if it does not have any requirement
			if ( empty( $requirements ) ) {
				continue;
			}

			// normalize requirement
			$requirements = is_array( $requirements ) ? $requirements : [ $requirements ];

			// check 'AND' requirements
			if ( $this->any_of_requirements_missed( $requirements ) ) {
				unset( $settings_list[$setting_name] );
				continue;
			}

			// check 'OR' requirements
			foreach ( $requirements as $or_requirements ) {
				if ( is_array( $or_requirements ) && ! $this->any_of_requirements_met( $or_requirements ) ) {
					unset( $settings_list[$setting_name] );
					break;
				}
			}
		}

		return $settings_list;
	}

	/**
	 * Generate html for more information drop down for the block
	 * @param array $links
	 * @return string
	 */
	private function learn_more_block( $links = [] ) {
		ob_start(); ?>

		<div class="epkb-admin__form-tab-content-learn-more">
		<div class="epkb-admin__form-tab-content-lm__header">
			<button class="epkb-admin__form-tab-content-lm__toggler"><?php esc_html_e( 'Learn More', 'echo-knowledge-base' ); ?></button>
		</div>
		<div class="epkb-admin__form-tab-content-lm__body">
			<div class="epkb-admin__form-tab-content-lm__description">
				<?php echo esc_html__( 'Learn more about these settings in the following articles', 'echo-knowledge-base' ) . ':'; ?>
			</div>
			<div class="epkb-admin__form-tab-content-lm__links"><?php
				foreach ( $links as $link_title => $link_url ) { ?>
					<div class="epkb-admin__form-tab-content-lm__link">
					<span class="eckb-article-title__icon ep_font_icon_document" style="color: #b3b3b3;"></span>
					<a href="<?php echo $link_url; ?>" target="_blank"><?php echo $link_title; ?></a>
					</div><?php
				} ?>
			</div>
			<div class="epkb-admin__form-tab-content-lm__footer">
				<a href="https://www.echoknowledgebase.com/documentation/" target="_blank"><?php esc_html_e( 'For all other Articles Browser Our Documentation.', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * Add sub-contents configs for Modular Main Page
	 *
	 * @param $sub_contents_configs
	 * @return mixed
	 */
	private function add_modular_sub_contents_configs( $sub_contents_configs ) {

		// Modules settings
		$modules_list = [

			'search' => [
				'module-settings' => [
					'ml_search_layout'                                      => 'not_asea',
					'search_title_font_color'                               => 'not_asea',
					'search_background_color'                               => 'not_asea',
				],
				'main-page-search-box-presets' => [
					'advanced_search_mp_presets' => 'asea',
				],
				'main-page-search-box' => [
					'advanced_search_mp_box_visibility' => 'asea',
					'advanced_search_mp_box_padding_top' => 'asea',
					'advanced_search_mp_box_padding_bottom' => 'asea',
					'advanced_search_mp_input_box_search_icon_placement' => 'asea',
					'advanced_search_mp_filter_toggle' => 'asea',

					'search_box_padding_top' => 'not_asea',
					'search_box_padding_bottom' => 'not_asea',
					'advanced_search_mp_filter_toggle_pro' => 'not_asea',
					'advanced_search_mp_description_below_input_pro' => 'not_asea',
					'advanced_search_mp_background_color_pro' => 'not_asea',

					'advanced_search_mp_filter_category_level' => 'asea',
					'advanced_search_mp_show_top_category' => 'asea',
					'advanced_search_mp_results_list_size' => 'asea',
				],
				'main-page-search-labels-box' => [
					'advanced_search_mp_title_toggle' => 'asea',
					'advanced_search_mp_title' => 'asea',
					'advanced_search_mp_title_font_color' => 'asea',
					'advanced_search_mp_description_below_title_toggle' => 'asea',
					'advanced_search_mp_description_below_title' => 'asea',
					'advanced_search_mp_description_below_input_toggle' => 'asea',
					'advanced_search_mp_description_below_input' => 'asea',
				],
				'main-page-search-style-box' => [
					'advanced_search_mp_link_font_color' => 'asea',
					'advanced_search_mp_background_color' => 'asea',
					'advanced_search_mp_background_gradient_toggle' => 'asea',
					'advanced_search_mp_background_gradient_from_color' => 'asea',
					'advanced_search_mp_background_gradient_to_color' => 'asea',
					'advanced_search_mp_background_gradient_degree' => 'asea',
					'advanced_search_mp_background_gradient_opacity' => 'asea',
				],
				'main-page-search-results-page' => [
					'advanced_search_mp_auto_complete_wait' => 'asea',
					'advanced_search_mp_results_page_size' => 'asea',
					'advanced_search_results_meta_created_on_toggle' => 'asea',
					'advanced_search_results_meta_author_toggle' => 'asea',
					'advanced_search_results_meta_categories_toggle' => 'asea',
				],
			],

			'categories_articles' => [
				'module-settings' => [
					'template_main_page_display_title'                      => 'only_kb_templates',
					'grid_nof_columns'                                      => [ 'elay', 'only_grid' ],
					// 'ml_categories_columns'                                 => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with nof_columns
					'nof_columns'                                           => [ 'not_grid', 'not_sidebar' ],
					'background_color'                                      => '',

					// Modular only settings
					//'ml_categories_articles_category_title_html_tag'        => [ [ 'only_classic', 'only_drill_down' ] ],
					//'ml_categories_articles_top_category_icon_bg_color_toggle'   => [ [ 'only_classic', 'only_drill_down' ] ],
					//'ml_categories_articles_top_category_icon_bg_color'          => [ [ 'only_classic', 'only_drill_down' ] ],
					//'section_border_color'                   => [ [ 'only_classic', 'only_drill_down' ] ],

					// Classic Layout
					//'ml_categories_articles_collapse_categories'            => 'only_classic',

					// Drill Down Layout
					'ml_categories_articles_back_button_bg_color'           => 'only_drill_down',

					// Other
					'ml_categories_articles_kblk_pro'                       => 'not_kblk',

					// 'ml_categories_articles_icon_size'                      => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_head_category_icon_size
					//	'section_head_category_icon_size'                       => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_icon_color'                     => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_head_category_icon_color
					//	'section_head_category_icon_color'                      => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_top_category_title_color'       => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_head_font_color
					//	'section_head_font_color'                               => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_cat_desc_color'                 => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_head_description_font_color
					//	'section_head_description_font_color'                   => [ [ 'only_classic', 'only_drill_down' ] ],

					// 'ml_categories_articles_height_mode'                    => [ 'only_classic' ],     TODO: remove; replaced with section_box_height_mode
					//	'section_box_height_mode'                               => 'only_classic',
					// 'ml_categories_articles_fixed_height'                   => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_body_height
					//	'section_body_height'                                   => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_nof_articles_displayed'         => [ 'only_drill_down' ],  TODO: remove; replaced with nof_articles_displayed

					// 'ml_categories_articles_article_color'                  => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with article_font_color and article_icon_color
					//	'article_font_color'                                    => [ [ 'only_classic', 'only_drill_down' ] ],
					//	'article_icon_color'                                    => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_sub_category_color'             => [ [ 'only_classic', 'only_drill_down' ] ], TODO: remove; replaced with section_category_font_color and section_category_icon_color
					//	'section_category_font_color'                           => [ [ 'only_classic', 'only_drill_down' ] ],
					//	'section_category_icon_color'                           => [ [ 'only_classic', 'only_drill_down' ] ],
					// 'ml_categories_articles_article_bg_color'               => [ 'only_drill_down' ],  TODO: remove; replaced with section_border_color
					//	'section_border_color'                                  => 'only_drill_down',
					//	'nof_articles_displayed'                                => 'only_drill_down',
				],
				'sidebar-settings' => [
					// Modular Sidebar settings
					'ml_categories_articles_sidebar_toggle'                 => '',
					'ml_categories_articles_sidebar_desktop_width'          => '',
					'ml_categories_articles_sidebar_location'               => '',
					'ml_categories_articles_sidebar_position_1'             => '',
					'ml_categories_articles_sidebar_position_2'             => '',
				],
				'sidebar-layout-introduction' => [
					'sidebar_main_page_intro_text_link' => [ 'elay', 'only_sidebar' ],
				],
			],

			'articles_list' => [
				'module-settings' => [
					//'ml_articles_list_layout'                             => '',
					'ml_articles_list_column_1'                             => '',
					'ml_articles_list_column_2'                             => '',
					'ml_articles_list_column_3'                             => '',
					'ml_articles_list_nof_articles_displayed'               => '',
					'ml_articles_list_kblk_pro'                             => 'not_kblk',
				],
			],

			'faqs' => [
				'module-settings' => [
					//'ml_faqs_layout'                                      => '',
					'ml_faqs_content_mode'                                  => '',
					'ml_faqs_custom_css_class'                              => '',
					'ml_faqs_kb_id'                                         => '',
					'ml_faqs_category_ids'                                  => '',
					'faq_schema_toggle'                                     => '',
				],
			],

			'resource_links' => [
				'module-settings'   => [
					'ml_resource_links_settings_pro_description'                     => 'not_elay',     // show only if Elegant Layouts is disabled
					//'ml_resource_links_layout'                            => 'elay',
					'ml_resource_links_columns'                             => 'elay',
					'ml_resource_links_container_background_color'          => 'elay',
				],
				'content-above-resources' => [

					// Container Text
					'ml_resource_links_container_text_color'                => 'elay',
					'ml_resource_links_container_text_alignment'            => 'elay',

					// Container Title
					'ml_resource_links_container_title_html_tag'            => 'elay',
				],
				'all-resources-boxes-styling' => [
					'ml_resource_links_border_color'                        => 'elay',
					'ml_resource_links_background_color'                    => 'elay',
					'ml_resource_links_background_hover_color'              => 'elay',
					'ml_resource_links_description_text_alignment'          => 'elay',
					'ml_resource_links_description_text_color'              => 'elay',
					'ml_resource_links_border_width'                        => 'elay',
					'ml_resource_links_shadow'                              => 'elay',
				],
				'all-resources-icons' => [
					'ml_resource_links_icon_location'                       => 'elay',
					'ml_resource_links_icon_color'                          => 'elay',
					'ml_resource_links_icon_type'                           => 'elay',
					'ml_resource_links_icon_image_size'                     => 'elay',
				],
				'all-resources-buttons-links' => [
					'ml_resource_links_option'                              => 'elay',
					'ml_resource_links_button_text_color'                   => 'elay',
					'ml_resource_links_button_background_color'             => 'elay',
					'ml_resource_links_button_location'                     => 'elay',
				],
				'individual-resource-settings' => [
					'ml_resource_links_settings_tabs'                       => 'elay',
					'ml_resource_links_1_settings_tab_content'              => 'elay',
					'ml_resource_links_1'                                   => 'elay',
					'ml_resource_links_1_title_text'                        => 'elay',
					'ml_resource_links_1_description_text'                  => 'elay',
					'ml_resource_links_1_button_text'                       => 'elay',
					'ml_resource_links_1_button_url'                        => 'elay',
					'ml_resource_links_1_icon_font'                         => 'elay',
					'ml_resource_links_1_icon_image'                        => 'elay',
					'ml_resource_links_2_settings_tab_content'              => 'elay',
					'ml_resource_links_2'                                   => 'elay',
					'ml_resource_links_2_title_text'                        => 'elay',
					'ml_resource_links_2_description_text'                  => 'elay',
					'ml_resource_links_2_button_text'                       => 'elay',
					'ml_resource_links_2_button_url'                        => 'elay',
					'ml_resource_links_2_icon_font'                         => 'elay',
					'ml_resource_links_2_icon_image'                        => 'elay',
					'ml_resource_links_3_settings_tab_content'              => 'elay',
					'ml_resource_links_3'                                   => 'elay',
					'ml_resource_links_3_title_text'                        => 'elay',
					'ml_resource_links_3_description_text'                  => 'elay',
					'ml_resource_links_3_button_text'                       => 'elay',
					'ml_resource_links_3_button_url'                        => 'elay',
					'ml_resource_links_3_icon_font'                         => 'elay',
					'ml_resource_links_3_icon_image'                        => 'elay',
					'ml_resource_links_4_settings_tab_content'              => 'elay',
					'ml_resource_links_4'                                   => 'elay',
					'ml_resource_links_4_title_text'                        => 'elay',
					'ml_resource_links_4_description_text'                  => 'elay',
					'ml_resource_links_4_button_text'                       => 'elay',
					'ml_resource_links_4_button_url'                        => 'elay',
					'ml_resource_links_4_icon_font'                         => 'elay',
					'ml_resource_links_4_icon_image'                        => 'elay',
					'ml_resource_links_5_settings_tab_content'              => 'elay',
					'ml_resource_links_5'                                   => 'elay',
					'ml_resource_links_5_title_text'                        => 'elay',
					'ml_resource_links_5_description_text'                  => 'elay',
					'ml_resource_links_5_button_text'                       => 'elay',
					'ml_resource_links_5_button_url'                        => 'elay',
					'ml_resource_links_5_icon_font'                         => 'elay',
					'ml_resource_links_5_icon_image'                        => 'elay',
					'ml_resource_links_6_settings_tab_content'              => 'elay',
					'ml_resource_links_6'                                   => 'elay',
					'ml_resource_links_6_title_text'                        => 'elay',
					'ml_resource_links_6_description_text'                  => 'elay',
					'ml_resource_links_6_button_text'                       => 'elay',
					'ml_resource_links_6_button_url'                        => 'elay',
					'ml_resource_links_6_icon_font'                         => 'elay',
					'ml_resource_links_6_icon_image'                        => 'elay',
					'ml_resource_links_7_settings_tab_content'              => 'elay',
					'ml_resource_links_7'                                   => 'elay',
					'ml_resource_links_7_title_text'                        => 'elay',
					'ml_resource_links_7_description_text'                  => 'elay',
					'ml_resource_links_7_button_text'                       => 'elay',
					'ml_resource_links_7_button_url'                        => 'elay',
					'ml_resource_links_7_icon_font'                         => 'elay',
					'ml_resource_links_7_icon_image'                        => 'elay',
					'ml_resource_links_8_settings_tab_content'              => 'elay',
					'ml_resource_links_8'                                   => 'elay',
					'ml_resource_links_8_title_text'                        => 'elay',
					'ml_resource_links_8_description_text'                  => 'elay',
					'ml_resource_links_8_button_text'                       => 'elay',
					'ml_resource_links_8_button_url'                        => 'elay',
					'ml_resource_links_8_icon_font'                         => 'elay',
					'ml_resource_links_8_icon_image'                        => 'elay',
				],
			],
		];

		if ( $this->is_old_elay ) {
			$modules_list['resource_links']['module-settings']['ml_resource_links_settings_old_elay_warning'] = 'elay';
		}

		// 1st Row
		$sub_contents_configs['main-page-ml-row-1'] = array(
			'module-selection' => array(
				'title'     => __( 'Feature Selection', 'echo-knowledge-base' ),
				'icon'      => 'epkb-admin__form-tab-content-icon-text',
				'icon_text' => __( 'Row 1', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-selection',
				'fields'    => [
					'ml_row_1_module'   => '',
				],
			),
			'layout' => array(
				'title'             => __( 'Layout', 'echo-knowledge-base' ),
				'css_class'         => 'epkb-admin__form-tab-content--layout epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'            => [
					'kb_main_page_layout' => '',
				],
				'data'              => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-selection' ],
			),
			'module-settings' => array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-settings' . ( $this->kb_config['ml_row_1_module'] == 'none' ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
				'fields'    => [
					'ml_row_1_desktop_width'            => '',
				],
			),
		);

		// 2nd Row
		$sub_contents_configs['main-page-ml-row-2'] = array(
			'module-selection' => array(
				'title'     => __( 'Feature Selection', 'echo-knowledge-base' ),
				'icon'      => 'epkb-admin__form-tab-content-icon-text',
				'icon_text' => __( 'Row 2', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-selection',
				'fields'    => [
					'ml_row_2_module'   => '',
				],
			),
			'module-settings' => array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-settings' . ( $this->kb_config['ml_row_2_module'] == 'none' ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
				'fields'    => [
					'ml_row_2_desktop_width'            => '',
				],
			),
		);

		// 3rd Row
		$sub_contents_configs['main-page-ml-row-3'] = array(
			'module-selection' => array(
				'title'     => __( 'Feature Selection', 'echo-knowledge-base' ),
				'icon'      => 'epkb-admin__form-tab-content-icon-text',
				'icon_text' => __( 'Row 3', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-selection',
				'fields'    => [
					'ml_row_3_module'   => '',
				],
			),
			'module-settings' => array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-settings' . ( $this->kb_config['ml_row_3_module'] == 'none' ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
				'fields'    => [
					'ml_row_3_desktop_width'            => '',
				],
			),
		);

		// 4th Row
		$sub_contents_configs['main-page-ml-row-4'] = array(
			'module-selection' => array(
				'title'     => __( 'Feature Selection', 'echo-knowledge-base' ),
				'icon'      => 'epkb-admin__form-tab-content-icon-text',
				'icon_text' => __( 'Row 4', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-selection',
				'fields'    => [
					'ml_row_4_module'   => '',
				],
			),
			'module-settings' => array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-settings' . ( $this->kb_config['ml_row_4_module'] == 'none' ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
				'fields'    => [
					'ml_row_4_desktop_width'            => '',
				],
			),
		);

		// 5th Row
		$sub_contents_configs['main-page-ml-row-5'] = array(
			'module-selection' => array(
				'title'     => __( 'Feature Selection', 'echo-knowledge-base' ),
				'icon'      => 'epkb-admin__form-tab-content-icon-text',
				'icon_text' => __( 'Row 5', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-selection',
				'fields'    => [
					'ml_row_5_module'   => '',
				],
			),
			'module-settings' => array(
				'title'     => __( 'Settings', 'echo-knowledge-base' ),
				'css_class' => 'epkb-admin__form-tab-content--module-settings' . ( $this->kb_config['ml_row_5_module'] == 'none' ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
				'fields'    => [
					'ml_row_5_desktop_width'            => '',
				],
			),
		);

		// Modular Main Page version of Settings boxes for 'old' layouts: Basic, Tabs, Category, Grid, Sidebar
		$sub_contents_configs['main-page-ml-row-1'] = array_merge_recursive( $sub_contents_configs['main-page-ml-row-1'], array(
			array(
				'title'         => __( 'Tabs', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'tab_nav_font_color' => 'only_tabs',
					'tab_nav_active_font_color' => 'only_tabs',
					'tab_nav_active_background_color' => 'only_tabs',
					'tab_nav_background_color' => 'only_tabs',
				],
				'learn_more_links' => [ // title => url
					__( 'Using Tabs Layout', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/using-tabs-layout/',
				],
				'data'          => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ],
			),
			array(
				'title'         => __( 'Categories Box', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'grid_section_box_height_mode' => [ 'elay', 'only_grid' ], // AND condition
					'grid_section_body_height' => [ 'elay', 'only_grid' ],
					'grid_section_box_shadow' => [ 'elay', 'only_grid' ],
					'ml_categories_articles_collapse_categories' => [ 'only_classic' ],
					'section_box_height_mode' => [ 'not_grid', 'not_sidebar' ],
					'section_body_height' => [ 'not_grid', 'not_sidebar' ],
					'section_border_color' => [ 'not_grid', 'not_sidebar' ],
					'section_border_radius' => [ 'not_grid', 'not_sidebar' ],
					'section_border_width' => [ 'not_grid', 'not_sidebar' ],
					'grid_section_border_color' => [ 'elay', 'only_grid' ],
					'grid_section_border_width' => [ 'elay', 'only_grid' ],
					'grid_section_border_radius' => [ 'elay', 'only_grid' ],
				],
				'learn_more_links' => [ // title => url
					__( 'Categories additional styles', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/additional-customization-of-kb-main-page/#articleTOC_0',
				],
				'data'          => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ],
			),
			array(
				'title'         => __( 'Category Header', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'grid_section_head_alignment' => [ 'elay', 'only_grid' ],
					'section_hyperlink_text_on' => [ 'elay', 'only_grid' ],     // if Grid category click goes to archive page or first article in the category
					'grid_section_head_font_color' => [ 'elay', 'only_grid' ],  // category name color
					'grid_section_desc_text_on' => [ 'elay', 'only_grid' ],     // category description on/off
					'grid_section_head_description_font_color' => [ 'elay', 'only_grid' ],  // category description color
					'grid_section_divider' => [ 'elay', 'only_grid' ],
					'grid_section_divider_color' => [ 'elay', 'only_grid' ],
					'grid_section_head_background_color' => [ 'elay', 'only_grid' ],

					// core layouts
					'section_head_alignment' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_hyperlink_on' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],     // link to category archive page
					'section_head_font_color' => [ 'not_grid', 'not_sidebar' ],     // Category Name Color
					'section_desc_text_on' => [ 'not_grid', 'not_sidebar' ],
					'section_head_description_font_color' => [ 'not_grid', 'not_sidebar' ],
					'section_divider' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_divider_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'section_head_background_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'ml_categories_articles_category_title_html_tag' => [ [ 'only_classic', 'only_drill_down' ] ],
				],
				'data'          => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ],
			),
			array(
				'title'         => __( 'Category Body', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'grid_section_article_count' => [ 'elay', 'only_grid' ],
					'grid_section_body_background_color' => [ 'elay', 'only_grid' ],
					'section_body_background_color' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
				],
				'data'          => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ],
			),
			array(
				'title'         => __( 'Category Icons', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'grid_category_icon_location' => [ 'elay', 'only_grid' ],
					'grid_section_icon_size' => [ 'elay', 'only_grid' ],
					'grid_section_head_icon_color' => [ 'elay', 'only_grid' ],
					'section_head_category_icon_location' => [ 'not_grid', 'not_sidebar' ],
					'section_head_category_icon_size' => [ 'not_grid', 'not_sidebar' ],
					'section_head_category_icon_color' => [ 'not_grid', 'not_sidebar' ],    // Category Icon
					'section_category_icon_color' => [ 'not_grid', 'not_sidebar' ], // Subcategory Expand Icon
					'section_category_font_color' => [ 'not_grid', 'not_sidebar' ], // Subcategory Text
					'ml_categories_articles_top_category_icon_bg_color_toggle' => [ [ 'only_classic', 'only_drill_down' ] ],
					'ml_categories_articles_top_category_icon_bg_color' => [ [ 'only_classic', 'only_drill_down' ] ],
				],
				'learn_more_links' => [ // title => url
					__( 'Set Image and Font Icons for Categories', 'echo-knowledge-base' ) => 'https://www.echoknowledgebase.com/documentation/how-do-you-change-icons-for-the-categories/',
				],
				'data'          => [ 'target' => 'category_icon', 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ]
			),
			array(
				'title'         => __( 'List of Articles', 'echo-knowledge-base' ),
				'css_class'     => 'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--categories_articles-box epkb-admin__form-tab-content--hide',
				'fields'        => [
					'nof_articles_displayed' => [ 'not_grid', 'not_sidebar' ],
					'section_box_shadow' => [ [ 'only_basic', 'only_tabs', 'only_categories' ] ],
					'expand_articles_icon' => [ [ 'only_basic', 'only_tabs' ] ],
					'article_icon_color' => [ 'not_sidebar' ],                                      // article icon color
					'sidebar_article_icon_color' => [ 'elay', 'only_sidebar' ],                     // article icon color
					'article_font_color' => [ 'not_grid', 'not_sidebar' ],                          // article titles color
					'grid_section_body_text_color' => [ 'elay', 'only_grid' ],                      // article titles color
					'sidebar_article_font_color' => [ 'elay', 'only_sidebar' ],                     // article titles color
					'ml_categories_articles_article_bg_color' => [ 'only_drill_down' ],
					'article_list_spacing' => [], // article spacing
				],
				'data'          => [ 'insert-box-after' => '.epkb-admin__form-tab-content--module-settings' ],
			),
		) );

		// Module settings boxes which will be moved via JS to row where the module is selected
		$dedicated_module_boxes_config = [];

		// Elegant Layouts Resource Links module
		if ( $this->elay_enabled && ! $this->is_old_elay ) {
			$dedicated_module_boxes_config['content-above-resources'] = [
				'module'    => 'resource_links',
				'title'     => __( 'Content Above the Resources', 'echo-knowledge-base' ),
			];
			$dedicated_module_boxes_config['all-resources-boxes-styling'] = [
				'module'    => 'resource_links',
				'title'     => __( 'All Resources - Boxes Styling', 'echo-knowledge-base' ),
			];
			$dedicated_module_boxes_config['all-resources-icons'] = [
				'module'    => 'resource_links',
				'title'     => __( 'All Resources - Icons', 'echo-knowledge-base' ),
			];
			$dedicated_module_boxes_config['all-resources-buttons-links'] = [
				'module'    => 'resource_links',
				'title'     => __( 'All Resources - Buttons / Links', 'echo-knowledge-base' ),
			];
			$dedicated_module_boxes_config['individual-resource-settings'] = [
				'module'    => 'resource_links',
				'title'     => __( 'Resource Link Individual Settings', 'echo-knowledge-base' ),
			];
		}

		// Search Box Presets
		if ( $this->asea_enabled ) {
			$dedicated_module_boxes_config['main-page-search-box-presets'] = [
				'module'    => 'search',
				'title'     => __( 'Search Box Presets', 'echo-knowledge-base' ),
			];
		}

		// Main Page Search Box
		$dedicated_module_boxes_config['main-page-search-box'] = [
			'module'    => 'search',
			'title'     => __( 'Main Page Search Box', 'echo-knowledge-base' ),
			'data'      => [ 'target' => 'advanced_search_mp' ],
		];

		// Main Page Search Labels Box
		$dedicated_module_boxes_config['main-page-search-labels-box'] = [
			'module'    => 'search',
			'title'     => __( 'Search Labels', 'echo-knowledge-base' ),
		];

		// Main Page Search Style Box
		$dedicated_module_boxes_config['main-page-search-style-box'] = [
			'module'    => 'search',
			'title'     => __( 'Search Style', 'echo-knowledge-base' ),
		];

		// Search Results Page
		if ( $this->asea_enabled ) {
			$dedicated_module_boxes_config['main-page-search-results-page'] = [
				'module'    => 'search',
				'title'     => __( 'Search Results Page', 'echo-knowledge-base' ),
				'data'      => [ 'target' => 'advanced_search_results_page' ],
			];
		}

		// Sidebar
		$dedicated_module_boxes_config['sidebar-settings'] = [
			'module'    => 'categories_articles',
			'title'     => __( 'Sidebar', 'echo-knowledge-base' ),
		];

		// Sidebar Layout Introduction Page
		$dedicated_module_boxes_config['sidebar-layout-introduction'] = [
			'module'    => 'categories_articles',
			'title'     => __( 'Sidebar Layout Introduction Page', 'echo-knowledge-base' ),
			'css_class' => 'epkb-admin__form-tab-content--sidebar_main_page_intro_text',
		];

		// Assign Modules to Rows
		for ( $row_number = 1; $row_number <= 5; $row_number ++ ) {

			$current_row_module = $this->kb_config['ml_row_' . $row_number . '_module'];
			if ( ! isset( $modules_list[$current_row_module] ) ) {
				continue;
			}

			// Layout Settings box
			$sub_contents_configs['main-page-ml-row-' . $row_number]['module-settings']['fields'] = array_merge( $sub_contents_configs['main-page-ml-row-' . $row_number]['module-settings']['fields'], $modules_list[$current_row_module]['module-settings'] );

			foreach ( $dedicated_module_boxes_config as $box_key => $box_config ) {
				if ( isset( $modules_list[$current_row_module][$box_key] ) ) {
					$sub_contents_configs['main-page-ml-row-' . $row_number][] = [
						'title'             => $box_config['title'],
						'css_class'         => ( isset( $box_config['css_class'] ) ? $box_config['css_class'] : '' ) . ' ' .
							'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--' . $box_config['module'] . '-box' . ( $this->kb_config['ml_row_' . $row_number . '_module'] != $box_config['module'] ? ' ' . 'epkb-admin__form-tab-content--hide' : '' ),
						'fields'            => $modules_list[$current_row_module][$box_key],
						'data'              => empty( $box_config['data'] )
							? [ 'insert-box-after'  => '.epkb-admin__form-tab-content--module-settings' ]
							: array_merge_recursive( $box_config['data'], [ 'insert-box-after'  => '.epkb-admin__form-tab-content--module-settings' ] ),
					];
				}
			}

			unset( $modules_list[$current_row_module] );
		}

		// Render unassigned modules to the first row (they will be hidden due to conditional settings, and are required for JS insertions)
		foreach ( $modules_list as $module => $fields ) {
			$sub_contents_configs['main-page-ml-row-1']['module-settings']['fields'] = array_merge( $sub_contents_configs['main-page-ml-row-1']['module-settings']['fields'], $fields['module-settings'] );

			foreach ( $dedicated_module_boxes_config as $box_key => $box_config ) {
				if ( isset( $fields[$box_key] ) ) {
					$sub_contents_configs['main-page-ml-row-1'][] = [
						'title'             => $box_config['title'],
						'css_class'         => ( isset( $box_config['css_class'] ) ? $box_config['css_class'] : '' )  . ' ' .
							'epkb-admin__form-tab-content--module-box epkb-admin__form-tab-content--' . $box_config['module'] . '-box epkb-admin__form-tab-content--hide',
						'fields'            => $fields[$box_key],
						'data'              => empty( $box_config['data'] )
							? [ 'insert-box-after'  => '.epkb-admin__form-tab-content--module-settings' ]
							: array_merge_recursive( $box_config['data'], [ 'insert-box-after'  => '.epkb-admin__form-tab-content--module-settings' ] ),
					];
				}
			}
		}

		// for Sidebar layout show message that Article Page search is controlled by Main Page search settings
		if ( $this->is_sidebar_layout ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title' => __( 'Article Page Search Box', 'echo-knowledge-base' ),
					'fields' => [
						'article_search_sidebar_layout_msg' => '',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
			];
		}
		// Article Page Search Box - Sidebar layout uses Main Page Search for both Main Page and Article Page
		if ( ! $this->is_sidebar_layout && $this->kb_config['article_search_sync_toggle'] == 'on' ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title' => __( 'Article Page Search Box', 'echo-knowledge-base' ),
					'fields' => [
						'article_search_sync_toggle' => '',
					],
					'data' => ['target' => 'advanced_search_ap'],
				),
			];
		}
		if ( ! $this->is_sidebar_layout && $this->kb_config['article_search_sync_toggle'] != 'on' ) {
			$sub_contents_configs['article-page-search-box'] = [
				array(
					'title'     => __( 'Settings', 'echo-knowledge-base' ),
					'fields'    => [
						'article_search_sync_toggle' => '',
						'article_search_toggle' => 'not_asea',
						'ml_article_search_layout' => 'not_asea',
						'article_search_title_font_color' => 'not_asea',
						'article_search_background_color' => 'not_asea',
					],
				),
				array(
					'title'         => __( 'Search Box Presets', 'echo-knowledge-base' ),
					'fields'        => [
						'advanced_search_ap_presets' => 'asea',
					],
				),
				array(
					'title'     => __( 'Article Page Search Box', 'echo-knowledge-base' ),
					'fields'    => [
						'advanced_search_ap_box_visibility' => 'asea',
						'advanced_search_ap_box_padding_top' => 'asea',
						'advanced_search_ap_box_padding_bottom' => 'asea',
						'advanced_search_ap_input_box_search_icon_placement' => 'asea',
						'advanced_search_ap_filter_toggle' => 'asea',

						'article_search_box_padding_top' => 'not_asea',
						'article_search_box_padding_bottom' => 'not_asea',
						'advanced_search_ap_filter_toggle_pro' => 'not_asea',
						'advanced_search_ap_description_below_input_pro' => 'not_asea',
						'advanced_search_ap_background_color_pro' => 'not_asea',

						'advanced_search_ap_filter_category_level' => 'asea',
						'advanced_search_ap_show_top_category' => 'asea',
						'advanced_search_ap_results_list_size' => 'asea',
					],
					'data'      => [ 'target' => 'advanced_search_ap' ],
				),
				array(
					'title'     => __( 'Search Labels', 'echo-knowledge-base' ),
					'fields'    => [
						'advanced_search_ap_title_toggle' => 'asea',
						'advanced_search_ap_title' => 'asea',
						'advanced_search_ap_title_font_color' => 'asea',
						'advanced_search_ap_description_below_title_toggle' => 'asea',
						'advanced_search_ap_description_below_title' => 'asea',
						'advanced_search_ap_description_below_input_toggle' => 'asea',
						'advanced_search_ap_description_below_input' => 'asea',
					],
				),
				array(
					'title'     => __( 'Search Style', 'echo-knowledge-base' ),
					'fields'    => [
						'advanced_search_ap_link_font_color' => 'asea',
						'advanced_search_ap_background_color' => 'asea',
						'advanced_search_ap_background_gradient_toggle' => 'asea',
						'advanced_search_ap_background_gradient_from_color' => 'asea',
						'advanced_search_ap_background_gradient_to_color' => 'asea',
						'advanced_search_ap_background_gradient_degree' => 'asea',
						'advanced_search_ap_background_gradient_opacity' => 'asea',
					],
				),
			];
		}

		return $sub_contents_configs;
	}

	/**
	 * Adjust Modular Main Page field specification when display it on Settings
	 *
	 * @param $field_specs
	 * @return array
	 */
	private function set_custom_modular_field_specs( $field_specs ) {

		switch ( $field_specs['name'] ) {

			// Module: Categories & Articles
			case 'kb_main_page_layout':
				$field_specs['input_group_class'] = 'eckb-conditional-setting-input' . ' ';
				if ( ! $this->elay_enabled ) {
					$field_specs['input_group_class'] .= 'eckb-mp-layout-elay-disabled' . ' ';
				}
				break;
			/* case 'ml_categories_articles_collapse_categories':
				$field_specs['dependency'] = ['kb_main_page_layout'];
				$field_specs['enable_on'] = ['Classic'];
				$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles eckb-conditional-setting-input' . ' ';
				break; */
			// case 'ml_categories_articles_height_mode':   TODO: remove; replaced with section_box_height_mode
			/* case 'section_box_height_mode':
				if ( $this->kb_config['kb_main_page_layout'] == 'Classic' ) {
					$field_specs['dependency'] = ['kb_main_page_layout'];
					$field_specs['enable_on'] = ['Classic'];
					$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles eckb-conditional-setting-input' . ' ';
				}
				break; */
			// case 'ml_categories_articles_fixed_height':  TODO: remove; replaced with section_body_height
			/* case 'section_body_height':
				if ( in_array( $this->kb_config['kb_main_page_layout'], [ 'Classic', 'Drill-Down' ] ) ) {
					$field_specs['dependency'] = ['section_box_height_mode'];
					$field_specs['enable_on'] = ['section_min_height', 'section_fixed_height'];
					$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles' . ' ';
				}
				break; */
			// case 'ml_categories_articles_nof_articles_displayed':    TODO: remove; replaced with nof_articles_displayed
			// case 'ml_categories_articles_article_bg_color':          TODO: remove; replaced with section_border_color
			// case 'nof_articles_displayed':
			case 'section_border_color':
				if ( $this->kb_config['kb_main_page_layout'] == 'Drill-Down' ) {
					$field_specs['dependency'] = ['kb_main_page_layout'];
					$field_specs['enable_on'] = ['Drill-Down'];
					$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles' . ' ';
				}
				break;
			// case 'ml_categories_articles_top_category_icon_bg_color':
			// case 'ml_categories_articles_top_category_icon_bg_color_toggle':
			case 'ml_categories_articles_article_show_more_color':
				// case 'section_border_color':
				// case 'ml_categories_articles_category_title_html_tag':
			case 'ml_categories_articles_kblk_pro':
				// case 'ml_categories_articles_icon_size':                 TODO: remove; replaced with section_head_category_icon_size
				// case 'ml_categories_articles_icon_color':                TODO: remove; replaced with section_head_category_icon_color
				// case 'ml_categories_articles_top_category_title_color':  TODO: remove; replaced with section_head_font_color
				// case 'ml_categories_articles_sub_category_color':        TODO: remove; replaced with section_category_font_color and section_category_icon_color
				// case 'ml_categories_articles_article_color':             TODO: remove; replaced with article_font_color and article_icon_color
				// case 'ml_categories_articles_cat_desc_color':            TODO: remove; replaced with section_head_description_font_color
				// case 'ml_categories_columns':                            TODO: remove; replaced with nof_columns
			case 'section_head_category_icon_size':
			case 'section_head_category_icon_color':
			case 'section_head_font_color':
			case 'section_category_font_color':
			case 'section_category_icon_color':
			case 'section_head_description_font_color':
			case 'grid_nof_columns':
			case 'nof_columns':
			case 'background_color':
			case 'ml_categories_articles_back_button_bg_color':
			case 'template_main_page_display_title':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['categories_articles'];
				$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles' . ' ';
				break;
			case 'ml_categories_articles_sidebar_toggle':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['categories_articles'];
				$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles eckb-conditional-setting-input' . ' ';
				// adjust better 'ml_categories_articles_sidebar_desktop_width' when user toggle Modular Sidebar 'on'
				$field_specs['group_data'] = [ 'default-value-px' => '250', 'default-value-pc' => '9' ];
				break;
			case 'ml_categories_articles_sidebar_desktop_width':
			case 'ml_categories_articles_sidebar_location':
			case 'ml_categories_articles_sidebar_position_1':
			case 'ml_categories_articles_sidebar_position_2':
				$field_specs['dependency'] = ['ml_categories_articles_sidebar_toggle'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'eckb-ml-module__categories_articles' . ' ';
				break;

			// Module: Search
			case 'ml_search_layout':
			case 'search_title_font_color':
			case 'search_background_color':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['search'];
				$field_specs['input_group_class'] = 'eckb-ml-module__search' . ' ';
				break;

			// Module: Articles List
			//case 'ml_articles_list_layout':
			case 'ml_articles_list_column_1':
			case 'ml_articles_list_column_2':
			case 'ml_articles_list_column_3':
			case 'ml_articles_list_nof_articles_displayed':
			case 'ml_articles_list_kblk_pro':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['articles_list'];
				$field_specs['input_group_class'] = 'eckb-ml-module__articles_list' . ' ';
				break;

			// Module: FAQs
			//case 'ml_faqs_layout':
			case 'ml_faqs_content_mode':
			case 'ml_faqs_custom_css_class':
			case 'ml_faqs_kb_id':
			case 'ml_faqs_category_ids':
			case 'faq_schema_toggle':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['faqs'];
				$field_specs['input_group_class'] = 'eckb-ml-module__faqs' . ' ';
				break;

			// Module: Resource Links (Elegant Layouts)
			case 'ml_resource_links_settings_pro_description':
			case 'ml_resource_links_settings_old_elay_warning':
			//case 'ml_resource_links_layout':
			case 'ml_resource_links_columns':
			case 'ml_resource_links_container_background_color':
				$field_specs['dependency'] = ['ml_row_1_module', 'ml_row_2_module', 'ml_row_3_module', 'ml_row_4_module', 'ml_row_5_module'];
				$field_specs['enable_on'] = ['resource_links'];
				$field_specs['input_group_class'] = 'eckb-ml-module__resource_links' . ' ';
				break;
			case 'ml_resource_links_1_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-1'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_1', 'ml_resource_links_1_title_text', 'ml_resource_links_1_button_url',
					'ml_resource_links_1_description_text', 'ml_resource_links_1_button_text', 'ml_resource_links_1_icon_font', 'ml_resource_links_1_icon_image'];
				break;
			case 'ml_resource_links_2_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-2'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_2', 'ml_resource_links_2_title_text', 'ml_resource_links_2_button_url',
					'ml_resource_links_2_description_text', 'ml_resource_links_2_button_text', 'ml_resource_links_2_icon_font', 'ml_resource_links_2_icon_image'];
				break;
			case 'ml_resource_links_3_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-3'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_3', 'ml_resource_links_3_title_text', 'ml_resource_links_3_button_url',
					'ml_resource_links_3_description_text', 'ml_resource_links_3_button_text', 'ml_resource_links_3_icon_font', 'ml_resource_links_3_icon_image'];
				break;
			case 'ml_resource_links_4_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-4'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_4', 'ml_resource_links_4_title_text', 'ml_resource_links_4_button_url',
					'ml_resource_links_4_description_text', 'ml_resource_links_4_button_text', 'ml_resource_links_4_icon_font', 'ml_resource_links_4_icon_image'];
				break;
			case 'ml_resource_links_5_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-5'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_5', 'ml_resource_links_5_title_text', 'ml_resource_links_5_button_url',
					'ml_resource_links_5_description_text', 'ml_resource_links_5_button_text', 'ml_resource_links_5_icon_font', 'ml_resource_links_5_icon_image'];
				break;
			case 'ml_resource_links_6_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-6'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_6', 'ml_resource_links_6_title_text', 'ml_resource_links_6_button_url',
					'ml_resource_links_6_description_text', 'ml_resource_links_6_button_text', 'ml_resource_links_6_icon_font', 'ml_resource_links_6_icon_image'];
				break;
			case 'ml_resource_links_7_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-7'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_7', 'ml_resource_links_7_title_text', 'ml_resource_links_7_button_url',
					'ml_resource_links_7_description_text', 'ml_resource_links_7_button_text', 'ml_resource_links_7_icon_font', 'ml_resource_links_7_icon_image'];
				break;
			case 'ml_resource_links_8_settings_tab_content':
				$field_specs['dependency'] = ['ml_resource_links_settings_tabs'];
				$field_specs['enable_on'] = ['resource-8'];
				$field_specs['input_group_class'] = 'epkb-admin__tab-content-field-resource-links' . ' ';
				$field_specs['included_settings'] = ['ml_resource_links_8', 'ml_resource_links_8_title_text', 'ml_resource_links_8_button_url',
					'ml_resource_links_8_description_text', 'ml_resource_links_8_button_text', 'ml_resource_links_8_icon_font', 'ml_resource_links_8_icon_image'];
				break;
			case 'ml_resource_links_container_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				break;
			case 'ml_resource_links_background_hover_color':
				$field_specs['dependency'] = ['ml_resource_links_option' ];
				$field_specs['enable_on'] = ['link'];
				break;
			case 'ml_resource_links_button_location':
			case 'ml_resource_links_button_text_color':
			case 'ml_resource_links_button_background_color':
				$field_specs['dependency'] = ['ml_resource_links_option'];
				$field_specs['enable_on'] = ['button'];
				break;
			case 'ml_resource_links_1':
			case 'ml_resource_links_2':
			case 'ml_resource_links_3':
			case 'ml_resource_links_4':
			case 'ml_resource_links_5':
			case 'ml_resource_links_6':
			case 'ml_resource_links_7':
			case 'ml_resource_links_8':
				$field_specs['input_group_class'] = 'eckb-conditional-setting-input' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_1_title_text':
			case 'ml_resource_links_1_button_url':
				$field_specs['dependency'] = ['ml_resource_links_1'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_1_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_1'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_1_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_1' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_1_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_1' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_1_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_1' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_2_title_text':
			case 'ml_resource_links_2_button_url':
				$field_specs['dependency'] = ['ml_resource_links_2'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_2_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_2'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_2_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_2' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_2_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_2' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_2_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_2' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_3_title_text':
			case 'ml_resource_links_3_button_url':
				$field_specs['dependency'] = ['ml_resource_links_3'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_3_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_3'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_3_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_3' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_3_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_3' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_3_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_3' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_4_title_text':
			case 'ml_resource_links_4_button_url':
				$field_specs['dependency'] = ['ml_resource_links_4'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_4_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_4'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_4_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_4' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_4_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_4' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_4_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_4' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_5_title_text':
			case 'ml_resource_links_5_button_url':
				$field_specs['dependency'] = ['ml_resource_links_5'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_5_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_5'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_5_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_5' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_5_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_5' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_5_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_5' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_6_title_text':
			case 'ml_resource_links_6_button_url':
				$field_specs['dependency'] = ['ml_resource_links_6'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_6_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_6'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_6_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_6' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_6_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_6' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_6_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_6' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_7_title_text':
			case 'ml_resource_links_7_button_url':
				$field_specs['dependency'] = ['ml_resource_links_7'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_7_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_7'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_7_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_7' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_7_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_7' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_7_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_7' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_8_title_text':
			case 'ml_resource_links_8_button_url':
				$field_specs['dependency'] = ['ml_resource_links_8'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_8_description_text':
				$field_specs['type'] = 'textarea';
				$field_specs['dependency'] = ['ml_resource_links_8'];
				$field_specs['enable_on'] = ['on'];
				$field_specs['input_group_class'] = 'epkb-admin__input-field--disallow-new-lines' . ' ';
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_8_button_text':
				$field_specs['dependency_and'] = ['ml_resource_links_8' => 'on', 'ml_resource_links_option' => 'button'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_8_icon_font':
				$field_specs['dependency_and'] = ['ml_resource_links_8' => 'on', 'ml_resource_links_icon_type' => 'font'];
				$field_specs['is_included'] = true;
				break;
			case 'ml_resource_links_8_icon_image':
				$field_specs['dependency_and'] = ['ml_resource_links_8' => 'on', 'ml_resource_links_icon_type' => 'image'];
				$field_specs['is_included'] = true;
				break;

			case 'ml_row_1_desktop_width':
				$field_specs['units_setting_name'] = 'ml_row_1_desktop_width_units';
				$field_specs['dependency'] = ['ml_row_1_module'];
				$field_specs['enable_on'] = ['categories_articles', 'articles_list', 'faqs', 'search'];

				// exclude row width setting for categories_articles module if a layout from Elegant Layouts is disabled so that we can show its ad
				if ( ! $this->elay_enabled && $this->is_elay_layout ) {
					$field_specs['enable_on'] = [ 'articles_list', 'faqs', 'search'];
				}

				if ( $this->elay_enabled && ! $this->is_old_elay ) {
					$field_specs['enable_on'][] = 'resource_links';
				}
				break;

			case 'ml_row_2_desktop_width':
				$field_specs['units_setting_name'] = 'ml_row_2_desktop_width_units';
				$field_specs['dependency'] = ['ml_row_2_module'];
				$field_specs['enable_on'] = ['categories_articles', 'articles_list', 'faqs', 'search'];

				// exclude row width setting for categories_articles module if a layout from Elegant Layouts is disabled so that we can show its ad
				if ( ! $this->elay_enabled && $this->is_elay_layout ) {
					$field_specs['enable_on'] = [ 'articles_list', 'faqs', 'search'];
				}

				if ( $this->elay_enabled && ! $this->is_old_elay ) {
					$field_specs['enable_on'][] = 'resource_links';
				}
				break;

			case 'ml_row_3_desktop_width':
				$field_specs['units_setting_name'] = 'ml_row_3_desktop_width_units';
				$field_specs['dependency'] = ['ml_row_3_module'];
				$field_specs['enable_on'] = ['categories_articles', 'articles_list', 'faqs', 'search'];

				// exclude row width setting for categories_articles module if a layout from Elegant Layouts is disabled so that we can show its ad
				if ( ! $this->elay_enabled && $this->is_elay_layout ) {
					$field_specs['enable_on'] = [ 'articles_list', 'faqs', 'search'];
				}

				if ( $this->elay_enabled && ! $this->is_old_elay ) {
					$field_specs['enable_on'][] = 'resource_links';
				}
				break;

			case 'ml_row_4_desktop_width':
				$field_specs['units_setting_name'] = 'ml_row_4_desktop_width_units';
				$field_specs['dependency'] = ['ml_row_4_module'];
				$field_specs['enable_on'] = ['categories_articles', 'articles_list', 'faqs', 'search'];

				// exclude row width setting for categories_articles module if a layout from Elegant Layouts is disabled so that we can show its ad
				if ( ! $this->elay_enabled && $this->is_elay_layout ) {
					$field_specs['enable_on'] = [ 'articles_list', 'faqs', 'search'];
				}

				if ( $this->elay_enabled && ! $this->is_old_elay ) {
					$field_specs['enable_on'][] = 'resource_links';
				}
				break;

			case 'ml_row_5_desktop_width':
				$field_specs['units_setting_name'] = 'ml_row_5_desktop_width_units';
				$field_specs['dependency'] = ['ml_row_5_module'];
				$field_specs['enable_on'] = ['categories_articles', 'articles_list', 'faqs', 'search'];

				// exclude row width setting for categories_articles module if a layout from Elegant Layouts is disabled so that we can show its ad
				if ( ! $this->elay_enabled && $this->is_elay_layout ) {
					$field_specs['enable_on'] = [ 'articles_list', 'faqs', 'search'];
				}

				if ( $this->elay_enabled && ! $this->is_old_elay ) {
					$field_specs['enable_on'][] = 'resource_links';
				}
				break;

			case 'ml_row_1_module':
			case 'ml_row_2_module':
			case 'ml_row_3_module':
			case 'ml_row_4_module':
			case 'ml_row_5_module':
			case 'ml_resource_links_option':
			case 'ml_resource_links_icon_type':
			case 'ml_resource_links_settings_tabs':
				$field_specs['input_group_class'] = 'eckb-conditional-setting-input' . ' ';
				break;

			case 'search_box_padding_top':
			case 'article_search_box_padding_top':
			case 'advanced_search_mp_box_padding_top':
			case 'advanced_search_ap_box_padding_top':
				$field_specs['label'] = esc_html__( 'Padding Top ( px )', 'echo-knowledge-base' );  // change labels here instead of specs to leave FE Editor UI unchanged
				break;

			case 'search_box_padding_bottom':
			case 'article_search_box_padding_bottom':
			case 'advanced_search_mp_box_padding_bottom':
			case 'advanced_search_ap_box_padding_bottom':
				$field_specs['label'] = esc_html__( 'Padding Bottom ( px )', 'echo-knowledge-base' );   // change labels here instead of specs to leave FE Editor UI unchanged
				break;

			default:
				break;
		}

		return $field_specs;
	}

	/**
	 * Check if any of the given requirements missed
	 *
	 * @param $requirements
	 * @return bool
	 */
	private function any_of_requirements_missed( $requirements ) {

		// a field can be disabled at certain times
		if ( in_array( 'none', $requirements ) ) {
			return true;
		}

		// a field might require an add-on plugins, layout, Modular Main page, KB Templates enabled
		if ( ( ( in_array( 'not_elay', $requirements ) && $this->elay_enabled ) || ( in_array( 'elay', $requirements ) && ! $this->elay_enabled ) )
			|| ( ( in_array( 'not_asea', $requirements ) && $this->asea_enabled ) || ( in_array( 'asea', $requirements ) && ! $this->asea_enabled ) )
			|| ( ( in_array( 'not_eprf', $requirements ) && $this->eprf_enabled ) || ( in_array( 'eprf', $requirements ) && ! $this->eprf_enabled ) )
			|| ( ( in_array( 'not_widg', $requirements ) && $this->widg_enabled ) || ( in_array( 'widg', $requirements ) && ! $this->widg_enabled ) )
			|| ( ( in_array( 'not_kblk', $requirements ) && $this->kblk_enabled ) || ( in_array( 'kblk', $requirements ) && ! $this->kblk_enabled ) )
			|| ( ( in_array( 'not_basic', $requirements ) && $this->is_basic_layout ) || ( in_array( 'only_basic', $requirements ) && ! $this->is_basic_layout ) )
			|| ( ( in_array( 'not_tabs', $requirements ) && $this->is_tabs_layout ) || ( in_array( 'only_tabs', $requirements ) && ! $this->is_tabs_layout ) )
			|| ( ( in_array( 'not_categories', $requirements ) && $this->is_categories_layout ) || ( in_array( 'only_categories', $requirements ) && ! $this->is_categories_layout ) )
			|| ( ( in_array( 'not_grid', $requirements ) && $this->is_grid_layout ) || ( in_array( 'only_grid', $requirements ) && ! $this->is_grid_layout ) )
			|| ( ( in_array( 'not_sidebar', $requirements ) && $this->is_sidebar_layout ) || ( in_array( 'only_sidebar', $requirements ) && ! $this->is_sidebar_layout ) )
			|| ( ( in_array( 'not_classic', $requirements ) && $this->is_classic_layout ) || ( in_array( 'only_classic', $requirements ) && ! $this->is_classic_layout ) )
			|| ( ( in_array( 'not_drill_down', $requirements ) && $this->is_drill_down_layout ) || ( in_array( 'only_drill_down', $requirements ) && ! $this->is_drill_down_layout ) )
			|| ( ( in_array( 'not_modular_main_page', $requirements ) && $this->is_modular_main_page ) || ( in_array( 'only_modular_main_page', $requirements ) && ! $this->is_modular_main_page ) )
			|| ( ( in_array( 'not_kb_templates', $requirements ) && $this->is_kb_templates ) || ( in_array( 'only_kb_templates', $requirements ) && ! $this->is_kb_templates ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if any of the given requirements met
	 *
	 * @param $requirementsResource Link Individual Settings
	 * @return bool
	 */
	private function any_of_requirements_met( $requirements ) {

		// a field might require an add-on plugins, layout, Modular Main page, KB Templates enabled
		if ( ( ( in_array( 'not_elay', $requirements ) && ! $this->elay_enabled ) || ( in_array( 'elay', $requirements ) && $this->elay_enabled ) )
			|| ( ( in_array( 'not_asea', $requirements ) && ! $this->asea_enabled ) || ( in_array( 'asea', $requirements ) && $this->asea_enabled ) )
			|| ( ( in_array( 'not_eprf', $requirements ) && ! $this->eprf_enabled ) || ( in_array( 'eprf', $requirements ) && $this->eprf_enabled ) )
			|| ( ( in_array( 'not_widg', $requirements ) && ! $this->widg_enabled ) || ( in_array( 'widg', $requirements ) && $this->widg_enabled ) )
			|| ( ( in_array( 'not_kblk', $requirements ) && ! $this->kblk_enabled ) || ( in_array( 'kblk', $requirements ) && $this->kblk_enabled ) )
			|| ( ( in_array( 'not_basic', $requirements ) && ! $this->is_basic_layout ) || ( in_array( 'only_basic', $requirements ) && $this->is_basic_layout ) )
			|| ( ( in_array( 'not_tabs', $requirements ) && ! $this->is_tabs_layout ) || ( in_array( 'only_tabs', $requirements ) && $this->is_tabs_layout ) )
			|| ( ( in_array( 'not_categories', $requirements ) && ! $this->is_categories_layout ) || ( in_array( 'only_categories', $requirements ) && $this->is_categories_layout ) )
			|| ( ( in_array( 'not_grid', $requirements ) && ! $this->is_grid_layout ) || ( in_array( 'only_grid', $requirements ) && $this->is_grid_layout ) )
			|| ( ( in_array( 'not_sidebar', $requirements ) && ! $this->is_sidebar_layout ) || ( in_array( 'only_sidebar', $requirements ) && $this->is_sidebar_layout ) )
			|| ( ( in_array( 'not_classic', $requirements ) && ! $this->is_classic_layout ) || ( in_array( 'only_classic', $requirements ) && $this->is_classic_layout ) )
			|| ( ( in_array( 'not_drill_down', $requirements ) && ! $this->is_drill_down_layout ) || ( in_array( 'only_drill_down', $requirements ) && $this->is_drill_down_layout ) )
			|| ( ( in_array( 'not_modular_main_page', $requirements ) && ! $this->is_modular_main_page ) || ( in_array( 'only_modular_main_page', $requirements ) && $this->is_modular_main_page ) )
			|| ( ( in_array( 'not_kb_templates', $requirements ) && ! $this->is_kb_templates ) || ( in_array( 'only_kb_templates', $requirements ) && $this->is_kb_templates ) ) ) {
			return true;
		}

		return false;
	}
}