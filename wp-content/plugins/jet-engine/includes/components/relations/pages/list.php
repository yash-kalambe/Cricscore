<?php
/**
 * Relations list page
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Relations_Page_List' ) ) {

	/**
	 * Define Jet_Engine_Relations_Page_List class
	 */
	class Jet_Engine_Relations_Page_List extends Jet_Engine_CPT_Page_Base {

		public $is_default = true;

		/**
		 * Class constructor
		 */
		public function __construct( $manager ) {

			parent::__construct( $manager );

			add_action( 'jet-engine/relations/page/after-title', array( $this, 'add_new_btn' ) );
		}

		/**
		 * Add new  post type button
		 */
		public function add_new_btn( $page ) {

			if ( $page->get_slug() !== $this->get_slug() ) {
				return;
			}

			?>
			<a class="page-title-action" href="<?php echo $this->manager->get_page_link( 'add' ); ?>"><?php
				_e( 'Add New', 'jet-engine' );
			?></a>
			<?php

			jet_engine()->get_video_help_popup( array(
				'popup_title' => __( 'JetEngine relations overview', 'jet-engine' ),
				'embed' => 'https://www.youtube.com/embed/4ZNF_BTBQ8M',
			) )->wp_page_popup();

		}

		/**
		 * Page slug
		 *
		 * @return string
		 */
		public function get_slug() {
			return 'list';
		}

		/**
		 * Page name
		 *
		 * @return string
		 */
		public function get_name() {
			return esc_html__( 'Relations List', 'jet-engine' );
		}

		/**
		 * Register add controls
		 * @return [type] [description]
		 */
		public function page_specific_assets() {

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets();

			wp_register_script(
				'jet-engine-cpt-delete-dialog',
				jet_engine()->plugin_url( 'includes/components/relations/assets/js/delete-dialog.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-delete-dialog',
				'JetEngineRelationDeleteDialog',
				array(
					'api_path' => jet_engine()->api->get_route( 'delete-relation' ),
					'redirect' => $this->manager->get_page_link( 'list' ),
				)
			);

			$this->manager->enqueue_reindex();

			wp_enqueue_script(
				'jet-engine-cpt-list',
				jet_engine()->plugin_url( 'includes/components/relations/assets/js/list.js' ),
				array( 'cx-vue-ui', 'wp-api-fetch', 'jet-engine-cpt-delete-dialog' ),
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-cpt-list',
				'JetEngineCPTListConfig',
				array(
					'api_path'        => jet_engine()->api->get_route( 'get-relations' ),
					'api_path_add'   => jet_engine()->api->get_route( 'add-relation' ),
					'edit_link'       => $this->manager->get_edit_item_link( '%id%' ),
					'relations_types' => $this->manager->get_relations_types(),
					'notices'         => array(
						'copied' => __( 'Copied!', 'jet-engine' ),
					),
				)
			);

			add_action( 'admin_footer', array( $this, 'add_page_template' ) );

		}

		/**
		 * Print add/edit page template
		 */
		public function add_page_template() {

			ob_start();
			include jet_engine()->relations->component_path( 'templates/list.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-list">%s</script>', $content );

			ob_start();
			include jet_engine()->relations->component_path( 'templates/delete-dialog.php' );
			$content = ob_get_clean();
			printf( '<script type="text/x-template" id="jet-cpt-delete-dialog">%s</script>', $content );

		}

		/**
		 * Renderer callback
		 *
		 * @return void
		 */
		public function render_page() {

			?>
			<br>
			<div id="jet_cpt_list"></div>
			<?php if ( jet_engine()->relations->legacy->convert->has_legacy_data() ) : ?>
			<div class="hidden cx-vui-panel cx-vui-component" style="margin: 30px 0 0 0; justify-content:space-between;align-items: center;">
				<div><?php
					_e( 'We still store the legacy relation\'s data created by the JetEngine versions lower than 2.11.0. It is saved in case you roll back the plugin\'s version (to lower than 2.11.0). You can delete the legacy relations data if the relations were succesfully transferred.', 'jet-engine' );
				?></div>
				<a href="<?php echo jet_engine()->relations->legacy->convert->clear_legacy_data_url(); ?>" class="cx-vui-button cx-vui-button--style-accent cx-vui-button--size-mini">
					<span class="cx-vui-button__content">
						<span><?php _e( 'Clear legacy data', 'jet-engine' ); ?></span>
					</span>
				</a>
			</div>
			<?php
			endif;

		}

	}

}
