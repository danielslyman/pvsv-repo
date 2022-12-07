<?php

namespace eRecht24\LegalTexts\Inc\admin\Widgets;

/**
 * Elementor Widget.
 *
 * Elementor widget that inserts an eRecht24 texts.
 *
 * @since 2.0.0
 */
class ElementorWidget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @return string Widget name.
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_name() {
		return 'erecht24';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @return string Widget title.
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __( 'eRecht24', 'erecht24' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @return string Widget icon.
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'fa fa-book eicon-post-content';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @return array Widget categories.
	 * @since 2.0.0
	 * @access public
	 *
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'erecht24' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'type',
			[
				'label'   => __( 'Type', 'erecht24' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'imprint'                     => __( 'Imprint', 'erecht24' ),
					'privacy_policy'              => __( 'Privacy policy', 'erecht24' ),
					'privacy_policy_social_media' => __( 'Privacy policy for social media profile', 'erecht24' ),
				],
				'default' => 'imprint',
			]
		);

		$this->add_control(
			'lang',
			[
				'label'   => __( 'Language', 'erecht24' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'de' => __( 'German', 'erecht24' ),
					'en' => __( 'English', 'erecht24' )
				],
				'default' => 'de',
			]
		);
		$this->add_control(
			'strip_title',
			[
				'label'   => __( 'Remove title', 'erecht24' ),
				'type'    => \Elementor\Controls_Manager::SWITCHER,
				'default' => 0,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.0.0
	 * @access protected
	 */
	protected function render() {

		$settings    = $this->get_settings_for_display();
		$strip_title = ( isset( $settings['strip_title'] ) && ! empty( $settings['strip_title'] ) );
		$html        = do_shortcode( '[erecht24 type="' . $settings['type'] . '" lang="' . $settings['lang'] . '" strip_title="' . $strip_title . '"]' );
		echo '<div class="erecht24-elementor-widget">';

		echo $html ?: $settings['type'];

		echo '</div>';

	}

}
