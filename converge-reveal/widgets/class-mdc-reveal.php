<?php
/**
 * Scroll Reveal Collection widget.
 *
 * @package Converge_Reveal
 */

namespace Converge_Reveal\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

defined( 'ABSPATH' ) || exit;

/**
 * Cards slide in from both sides while the centre title fades out on scroll.
 */
class Reveal extends Widget_Base {

	/* ---------------------------------------------------------------------
	 * Identity (hard-coded values only — never read settings here)
	 * ------------------------------------------------------------------ */

	public function get_name() {
		return 'mdc-reveal';
	}

	public function get_title() {
		return esc_html__( 'Scroll Reveal Collection', 'converge-reveal' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'converge-reveal' ];
	}

	public function get_keywords() {
		return [ 'reveal', 'scroll', 'collection', 'cards', 'gallery', 'sticky', 'converge' ];
	}

	public function get_style_depends() {
		return [ 'mdc-reveal' ];
	}

	public function get_script_depends() {
		return [ 'mdc-reveal' ];
	}

	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	/* ---------------------------------------------------------------------
	 * Controls
	 * ------------------------------------------------------------------ */

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_style_controls();
		$this->register_title_style_controls();
		$this->register_card_style_controls();
		$this->register_image_style_controls();
		$this->register_card_title_style_controls();
	}

	/**
	 * HTML tag options for title controls.
	 *
	 * @return array
	 */
	private function get_tag_options() {
		return [
			'h1'   => 'H1',
			'h2'   => 'H2',
			'h3'   => 'H3',
			'h4'   => 'H4',
			'h5'   => 'H5',
			'h6'   => 'H6',
			'div'  => 'div',
			'span' => 'span',
			'p'    => 'p',
		];
	}

	/**
	 * Content tab.
	 *
	 * @return void
	 */
	private function register_content_controls() {

		/* ---- Cards ---- */
		$this->start_controls_section(
			'section_cards',
			[
				'label' => esc_html__( 'Cards', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__( 'Image', 'converge-reveal' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [ 'url' => Utils::get_placeholder_image_src() ],
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'default'   => 'large',
				'separator' => 'none',
			]
		);

		$repeater->add_control(
			'image_alt',
			[
				'label'       => esc_html__( 'Alt Text', 'converge-reveal' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Empty = media library alt text', 'converge-reveal' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'converge-reveal' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Floor', 'converge-reveal' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'link',
			[
				'label'       => esc_html__( 'Link', 'converge-reveal' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'converge-reveal' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$defaults = [];
		foreach ( [ 'Floor', 'Wall', 'Kitchen', 'Outdoor' ] as $label ) {
			$defaults[] = [
				'title' => $label,
				'image' => [ 'url' => Utils::get_placeholder_image_src() ],
			];
		}

		$this->add_control(
			'cards',
			[
				'label'         => esc_html__( 'Cards', 'converge-reveal' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $repeater->get_controls(),
				'default'       => $defaults,
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
			]
		);

		$this->end_controls_section();

		/* ---- Card titles ---- */
		$this->start_controls_section(
			'section_card_titles',
			[
				'label' => esc_html__( 'Card Titles', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_card_titles',
			[
				'label'        => esc_html__( 'Show Card Titles', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'converge-reveal' ),
				'label_off'    => esc_html__( 'Hide', 'converge-reveal' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'card_title_tag',
			[
				'label'     => esc_html__( 'HTML Tag', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_tag_options(),
				'default'   => 'h3',
				'condition' => [ 'show_card_titles' => 'yes' ],
			]
		);

		$this->end_controls_section();

		/* ---- Centre title ---- */
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Centre Title', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Show Title', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'converge-reveal' ),
				'label_off'    => esc_html__( 'Hide', 'converge-reveal' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Without the title the cards are shown as a static grid (no scroll animation).', 'converge-reveal' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'converge-reveal' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'New Collection', 'converge-reveal' ),
				'label_block' => true,
				'dynamic'     => [ 'active' => true ],
				'condition'   => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__( 'HTML Tag', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $this->get_tag_options(),
				'default'   => 'h2',
				'condition' => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_control(
			'mobile_effect',
			[
				'label'        => esc_html__( 'Mobile Converge Effect', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'converge-reveal' ),
				'label_off'    => esc_html__( 'Off', 'converge-reveal' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'On mobile the title shows over a sticky stage, then the cards fly in one after another from both sides. Best with up to 6 cards. Off = plain grid.', 'converge-reveal' ),
				'condition'    => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_control(
			'show_title_mobile',
			[
				'label'        => esc_html__( 'Show Title on Mobile', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'converge-reveal' ),
				'label_off'    => esc_html__( 'Hide', 'converge-reveal' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => [
					'show_title'    => 'yes',
					'mobile_effect' => '',
				],
			]
		);

		$this->end_controls_section();

		/* ---- Behaviour & performance ---- */
		$this->start_controls_section(
			'section_behavior',
			[
				'label' => esc_html__( 'Behavior & Performance', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'image_loading',
			[
				'label'       => esc_html__( 'Image Loading', 'converge-reveal' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'lazy',
				'options'     => [
					'lazy'     => esc_html__( 'Lazy', 'converge-reveal' ),
					'eager'    => esc_html__( 'Eager', 'converge-reveal' ),
					'priority' => esc_html__( 'Eager + High Priority (LCP)', 'converge-reveal' ),
				],
				'description' => esc_html__( 'Use "Eager + High Priority" only when the widget is at the top of the page.', 'converge-reveal' ),
			]
		);

		$this->add_control(
			'clip_overflow',
			[
				'label'        => esc_html__( 'Prevent Horizontal Page Overflow', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Sets overflow-x: clip on html/body (sticky breaks with overflow hidden).', 'converge-reveal' ),
			]
		);

		$this->add_control(
			'editor_anim',
			[
				'label'        => esc_html__( 'Preview Animation in Editor', 'converge-reveal' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style: layout & stage.
	 *
	 * @return void
	 */
	private function register_layout_style_controls() {
		$this->start_controls_section(
			'section_style_layout',
			[
				'label' => esc_html__( 'Layout & Stage', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'stage_height',
			[
				'label'       => esc_html__( 'Stage Height', 'converge-reveal' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'vh', 'px' ],
				'range'       => [
					'vh' => [ 'min' => 30, 'max' => 100 ],
					'px' => [ 'min' => 300, 'max' => 1600 ],
				],
				'description' => esc_html__( 'Empty = 100svh (full screen).', 'converge-reveal' ),
				'selectors'   => [
					'{{WRAPPER}} .mdc-reveal-wrap' => '--mdc-stage-h: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_distance',
			[
				'label'      => esc_html__( 'Scroll Distance', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'vh', 'px' ],
				'range'      => [
					'vh' => [ 'min' => 10, 'max' => 400 ],
					'px' => [ 'min' => 100, 'max' => 4000 ],
				],
				'default'        => [ 'unit' => 'vh', 'size' => 70 ],
				'mobile_default' => [ 'unit' => 'vh', 'size' => 45 ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal-wrap' => '--mdc-scroll: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cards_gap',
			[
				'label'          => esc_html__( 'Gap Between Cards', 'converge-reveal' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 58 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'      => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'side_spacing',
			[
				'label'      => esc_html__( 'Side Spacing (desktop/tablet)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 41 ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-pad: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_width',
			[
				'label'      => esc_html__( 'Centre Title Width', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'vw', 'px', 'em', 'rem' ],
				'range'      => [
					'vw'  => [ 'min' => 5, 'max' => 60 ],
					'px'  => [ 'min' => 100, 'max' => 1000 ],
					'em'  => [ 'min' => 5, 'max' => 60 ],
					'rem' => [ 'min' => 5, 'max' => 60 ],
				],
				'default'    => [ 'unit' => 'vw', 'size' => 27 ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-title-w: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'align_items',
			[
				'label'     => esc_html__( 'Vertical Alignment', 'converge-reveal' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'converge-reveal' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'Middle', 'converge-reveal' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bottom', 'converge-reveal' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'center',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal' => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_columns',
			[
				'label'     => esc_html__( 'Columns on Mobile', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '2',
				'options'   => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
				],
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-m-cols: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'mobile_offset',
			[
				'label'      => esc_html__( 'Mobile Effect: Fly-in Distance', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'vw' ],
				'range'      => [ 'vw' => [ 'min' => 20, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'vw', 'size' => 55 ],
				'condition'  => [ 'mobile_effect' => 'yes' ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-m-offset: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'mobile_stagger',
			[
				'label'       => esc_html__( 'Mobile Effect: Stagger', 'converge-reveal' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ '%' ],
				'range'       => [ '%' => [ 'min' => 0, 'max' => 20 ] ],
				'default'     => [ 'unit' => '%', 'size' => 7 ],
				'description' => esc_html__( 'Delay between cards, as a share of the scroll distance.', 'converge-reveal' ),
				'condition'   => [ 'mobile_effect' => 'yes' ],
				'selectors'   => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-m-step: calc({{SIZE}} / 100);',
				],
			]
		);

		$this->add_responsive_control(
			'stage_padding_block',
			[
				'label'          => esc_html__( 'Stage Padding (Top/Bottom)', 'converge-reveal' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', 'em', 'vh' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 0 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'      => [
					'{{WRAPPER}} .mdc-reveal' => 'padding-block: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'stage_padding_inline',
			[
				'label'          => esc_html__( 'Stage Padding (Sides)', 'converge-reveal' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ 'px', 'em', 'vw' ],
				'range'          => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'        => [ 'unit' => 'px', 'size' => 0 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors'      => [
					'{{WRAPPER}} .mdc-reveal' => 'padding-inline: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'stage_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mdc-reveal',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style: centre title.
	 *
	 * @return void
	 */
	private function register_title_style_controls() {
		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__( 'Centre Title', 'converge-reveal' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_title' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .mdc-reveal__title',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'converge-reveal' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .mdc-reveal__title',
			]
		);

		$this->add_responsive_control(
			'title_align',
			[
				'label'     => esc_html__( 'Alignment', 'converge-reveal' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start'  => [
						'title' => esc_html__( 'Start', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'    => [
						'title' => esc_html__( 'End', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => false,
				'classes'   => 'elementor-control-start-end',
				'selectors_dictionary' => [
					'start'  => 'flex-start',
					'center' => 'center',
					'end'    => 'flex-end',
				],
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal__title' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'              => esc_html__( 'Margin', 'converge-reveal' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => [ 'px', 'em', '%' ],
				'selectors'          => [
					'{{WRAPPER}} .mdc-reveal__title' => 'margin-block-start: {{TOP}}{{UNIT}}; margin-block-end: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_fade_end',
			[
				'label'       => esc_html__( 'Fully Faded At Scroll Progress', 'converge-reveal' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ '%' ],
				'range'       => [ '%' => [ 'min' => 5, 'max' => 100 ] ],
				'default'     => [ 'unit' => '%', 'size' => 35 ],
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .mdc-reveal' => '--mdc-fade: calc({{SIZE}} / 100);',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style: cards (containers).
	 *
	 * @return void
	 */
	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Cards', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'card_gap',
			[
				'label'      => esc_html__( 'Gap (Image / Title)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 14 ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal__item' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding_block',
			[
				'label'      => esc_html__( 'Padding (Top/Bottom)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal__item' => 'padding-block: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding_inline',
			[
				'label'      => esc_html__( 'Padding (Sides)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal__item' => 'padding-inline: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'converge-reveal' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'card_transition',
			[
				'label'      => esc_html__( 'Transition Duration (s)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range'      => [ 's' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ] ],
				'default'    => [ 'unit' => 's', 'size' => 0.3 ],
				'selectors'  => [
					'{{WRAPPER}} .mdc-reveal__item' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$this->add_control(
			'card_focus_color',
			[
				'label'     => esc_html__( 'Focus Outline Color', 'converge-reveal' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal__item:focus-visible' => 'outline-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'card_tabs' );

		$this->start_controls_tab(
			'card_tab_normal',
			[ 'label' => esc_html__( 'Normal', 'converge-reveal' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mdc-reveal__item',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .mdc-reveal__item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .mdc-reveal__item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'card_tab_hover',
			[ 'label' => esc_html__( 'Hover', 'converge-reveal' ) ]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'card_background_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .mdc-reveal__item:hover, {{WRAPPER}} .mdc-reveal__item:focus-visible',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'card_border_hover',
				'selector' => '{{WRAPPER}} .mdc-reveal__item:hover, {{WRAPPER}} .mdc-reveal__item:focus-visible',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'card_shadow_hover',
				'selector' => '{{WRAPPER}} .mdc-reveal__item:hover, {{WRAPPER}} .mdc-reveal__item:focus-visible',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style: card image.
	 *
	 * @return void
	 */
	private function register_image_style_controls() {
		$image = '{{WRAPPER}} .mdc-reveal__image';
		$hover = '{{WRAPPER}} .mdc-reveal__item:hover .mdc-reveal__image, {{WRAPPER}} .mdc-reveal__item:focus-visible .mdc-reveal__image';

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Card Image', 'converge-reveal' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => esc_html__( 'Width', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'vw' ],
				'range'      => [
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'px' => [ 'min' => 50, 'max' => 1000 ],
					'vw' => [ 'min' => 5, 'max' => 100 ],
				],
				'default'    => [ 'unit' => '%', 'size' => 100 ],
				'selectors'  => [ $image => 'width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'image_max_width',
			[
				'label'      => esc_html__( 'Max Width', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'vw' ],
				'range'      => [
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'px' => [ 'min' => 50, 'max' => 1000 ],
					'vw' => [ 'min' => 5, 'max' => 100 ],
				],
				'selectors'  => [ $image => 'max-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'       => esc_html__( 'Height', 'converge-reveal' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'vh', 'em' ],
				'range'       => [
					'px' => [ 'min' => 50, 'max' => 1200 ],
					'vh' => [ 'min' => 10, 'max' => 100 ],
					'em' => [ 'min' => 5, 'max' => 80 ],
				],
				'description' => esc_html__( 'Empty = height follows the aspect ratio below.', 'converge-reveal' ),
				'selectors'   => [ $image => 'height: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'image_aspect_ratio',
			[
				'label'     => esc_html__( 'Aspect Ratio', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '41 / 64',
				'options'   => [
					'41 / 64' => esc_html__( 'Design (41:64)', 'converge-reveal' ),
					'2 / 3'   => '2:3',
					'3 / 4'   => '3:4',
					'4 / 5'   => '4:5',
					'1 / 1'   => '1:1',
					'16 / 9'  => '16:9',
					'auto'    => esc_html__( 'Original', 'converge-reveal' ),
				],
				'selectors' => [ $image => 'aspect-ratio: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'image_object_fit',
			[
				'label'     => esc_html__( 'Object Fit', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'      => esc_html__( 'Cover', 'converge-reveal' ),
					'contain'    => esc_html__( 'Contain', 'converge-reveal' ),
					'fill'       => esc_html__( 'Fill', 'converge-reveal' ),
					'scale-down' => esc_html__( 'Scale Down', 'converge-reveal' ),
					'none'       => esc_html__( 'None', 'converge-reveal' ),
				],
				'selectors' => [ $image => 'object-fit: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'image_object_position',
			[
				'label'     => esc_html__( 'Object Position', 'converge-reveal' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'Center', 'converge-reveal' ),
					'center top'    => esc_html__( 'Top', 'converge-reveal' ),
					'center bottom' => esc_html__( 'Bottom', 'converge-reveal' ),
					'left center'   => esc_html__( 'Left', 'converge-reveal' ),
					'right center'  => esc_html__( 'Right', 'converge-reveal' ),
				],
				'selectors' => [ $image => 'object-position: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'converge-reveal' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'separator'  => 'before',
				'selectors'  => [
					$image => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_transition',
			[
				'label'      => esc_html__( 'Transition Duration (s)', 'converge-reveal' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range'      => [ 's' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ] ],
				'default'    => [ 'unit' => 's', 'size' => 0.3 ],
				'selectors'  => [ $image => 'transition-duration: {{SIZE}}s;' ],
			]
		);

		$this->start_controls_tabs( 'image_tabs' );

		$this->start_controls_tab(
			'image_tab_normal',
			[ 'label' => esc_html__( 'Normal', 'converge-reveal' ) ]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'     => esc_html__( 'Opacity', 'converge-reveal' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'selectors' => [ $image => 'opacity: {{SIZE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters',
				'selector' => $image,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => $image,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => $image,
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'image_tab_hover',
			[ 'label' => esc_html__( 'Hover', 'converge-reveal' ) ]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label'     => esc_html__( 'Opacity', 'converge-reveal' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'selectors' => [ $hover => 'opacity: {{SIZE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'image_filters_hover',
				'selector' => $hover,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border_hover',
				'selector' => $hover,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow_hover',
				'selector' => $hover,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style: card title.
	 *
	 * @return void
	 */
	private function register_card_title_style_controls() {
		$title = '{{WRAPPER}} .mdc-reveal__item-title';

		$this->start_controls_section(
			'section_style_card_title',
			[
				'label'     => esc_html__( 'Card Title', 'converge-reveal' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_card_titles' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'card_title_typography',
				'selector' => $title,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'card_title_text_shadow',
				'selector' => $title,
			]
		);

		$this->add_responsive_control(
			'card_title_align',
			[
				'label'     => esc_html__( 'Alignment', 'converge-reveal' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start'  => [
						'title' => esc_html__( 'Start', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'    => [
						'title' => esc_html__( 'End', 'converge-reveal' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'start',
				'toggle'    => false,
				'classes'   => 'elementor-control-start-end',
				'selectors' => [ $title => 'text-align: {{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'card_title_margin',
			[
				'label'              => esc_html__( 'Margin', 'converge-reveal' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'vertical',
				'size_units'         => [ 'px', 'em' ],
				'selectors'          => [
					$title => 'margin-block-start: {{TOP}}{{UNIT}}; margin-block-end: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'card_title_tabs' );

		$this->start_controls_tab(
			'card_title_tab_normal',
			[ 'label' => esc_html__( 'Normal', 'converge-reveal' ) ]
		);

		$this->add_control(
			'card_title_color',
			[
				'label'     => esc_html__( 'Color', 'converge-reveal' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ $title => 'color: {{VALUE}};' ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'card_title_tab_hover',
			[ 'label' => esc_html__( 'Hover', 'converge-reveal' ) ]
		);

		$this->add_control(
			'card_title_color_hover',
			[
				'label'     => esc_html__( 'Color', 'converge-reveal' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .mdc-reveal__item:hover .mdc-reveal__item-title, {{WRAPPER}} .mdc-reveal__item:focus-visible .mdc-reveal__item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * Output
	 * ------------------------------------------------------------------ */

	/**
	 * Resolve the data needed to print one card image.
	 *
	 * @param array $card             Repeater item.
	 * @param bool  $show_card_titles Whether the card title is visible.
	 * @return array|null
	 */
	private function get_card_image( array $card, $show_card_titles ) {
		$media = ( isset( $card['image'] ) && is_array( $card['image'] ) ) ? $card['image'] : [];
		$id    = ! empty( $media['id'] ) ? (int) $media['id'] : 0;
		$url   = ! empty( $media['url'] ) ? (string) $media['url'] : '';

		if ( ! $id && '' === $url ) {
			return null;
		}

		$width  = 0;
		$height = 0;
		$srcset = '';

		if ( $id ) {
			$size_key = ! empty( $card['image_size'] ) ? (string) $card['image_size'] : 'large';
			$src      = Group_Control_Image_Size::get_attachment_image_src( $id, 'image', $card );

			if ( $src ) {
				$url = $src;
			}

			if ( 'custom' === $size_key ) {
				$dimension = ( isset( $card['image_custom_dimension'] ) && is_array( $card['image_custom_dimension'] ) ) ? $card['image_custom_dimension'] : [];
				$width     = ! empty( $dimension['width'] ) ? (int) $dimension['width'] : 0;
				$height    = ! empty( $dimension['height'] ) ? (int) $dimension['height'] : 0;
			} else {
				$data = wp_get_attachment_image_src( $id, $size_key );
				if ( $data ) {
					$width  = (int) $data[1];
					$height = (int) $data[2];
				}
				$srcset = (string) wp_get_attachment_image_srcset( $id, $size_key );
			}
		}

		if ( '' === $url ) {
			return null;
		}

		$title = isset( $card['title'] ) ? (string) $card['title'] : '';
		$alt   = ! empty( $card['image_alt'] ) ? (string) $card['image_alt'] : '';

		if ( '' === $alt && $id ) {
			$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		}

		// A visible caption already names the card, so an empty alt avoids a double read-out.
		if ( '' === $alt && ! $show_card_titles ) {
			$alt = $title;
		}

		return [
			'src'    => $url,
			'alt'    => $alt,
			'width'  => $width,
			'height' => $height,
			'srcset' => $srcset,
		];
	}

	/**
	 * Print one card.
	 *
	 * @param array $card   Repeater item.
	 * @param int   $index  Item index.
	 * @param bool  $is_end Whether the card belongs to the second group.
	 * @param array $opts   Pre-resolved options from render().
	 * @return void
	 */
	private function render_card( array $card, $index, $is_end, array $opts ) {
		$key        = 'card-' . $index;
		$card_title = isset( $card['title'] ) ? (string) $card['title'] : '';
		$has_link   = ! empty( $card['link']['url'] );
		$tag        = $has_link ? 'a' : 'div';

		$this->add_render_attribute(
			$key,
			[
				'class' => [
					'mdc-reveal__item',
					$is_end ? 'mdc-reveal__item--end' : 'mdc-reveal__item--start',
				],
				// Index and side drive the mobile stagger / fly-in direction.
				'style' => '--mdc-i:' . (int) $index . ';--mdc-s:' . ( 0 === $index % 2 ? '-1' : '1' ) . ';',
			]
		);

		if ( $has_link ) {
			$this->add_link_attributes( $key, $card['link'] );

			if ( ! $opts['show_card_titles'] && '' !== $card_title ) {
				$this->add_render_attribute( $key, 'aria-label', $card_title );
			}
		}

		echo '<' . $tag . ' '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'a' or 'div'.
		$this->print_render_attribute_string( $key );
		echo '>';

		$image = $this->get_card_image( $card, $opts['show_card_titles'] );

		if ( $image ) {
			$img_key = 'image-' . $index;

			$this->add_render_attribute(
				$img_key,
				[
					'class'    => 'mdc-reveal__image',
					'src'      => $image['src'],
					'alt'      => $image['alt'],
					'loading'  => 'lazy' === $opts['loading'] ? 'lazy' : 'eager',
					'decoding' => 'async',
				]
			);

			if ( $image['width'] && $image['height'] ) {
				$this->add_render_attribute( $img_key, [ 'width' => $image['width'], 'height' => $image['height'] ] );
			}

			if ( '' !== $image['srcset'] ) {
				$this->add_render_attribute( $img_key, [ 'srcset' => $image['srcset'], 'sizes' => $opts['sizes'] ] );
			}

			if ( 'priority' === $opts['loading'] ) {
				$this->add_render_attribute( $img_key, 'fetchpriority', 'high' );
			}

			echo '<img ';
			$this->print_render_attribute_string( $img_key );
			echo '>';
		}

		if ( $opts['show_card_titles'] && '' !== $card_title ) {
			echo '<' . esc_html( $opts['card_title_tag'] ) . ' class="mdc-reveal__item-title">' . esc_html( $card_title ) . '</' . esc_html( $opts['card_title_tag'] ) . '>';
		}

		echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed 'a' or 'div'.
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$cards    = ( ! empty( $settings['cards'] ) && is_array( $settings['cards'] ) ) ? array_values( $settings['cards'] ) : [];

		if ( empty( $cards ) ) {
			return;
		}

		$count       = count( $cards );
		$split       = (int) ceil( $count / 2 );
		$title_text  = isset( $settings['title'] ) ? (string) $settings['title'] : '';
		$show_title  = ( 'yes' === ( $settings['show_title'] ?? '' ) && '' !== $title_text );
		$title_tag   = Utils::validate_html_tag( $settings['title_tag'] ?? 'h2' );
		$loading     = in_array( $settings['image_loading'] ?? 'lazy', [ 'lazy', 'eager', 'priority' ], true ) ? $settings['image_loading'] : 'lazy';
		$wrap_class  = [ 'mdc-reveal-wrap' ];

		$wrap_class[] = $show_title ? 'mdc-reveal-wrap--animated' : 'mdc-reveal-wrap--static';

		if ( 'yes' === ( $settings['clip_overflow'] ?? '' ) ) {
			$wrap_class[] = 'mdc-reveal-wrap--clip';
		}

		if ( 'yes' === ( $settings['editor_anim'] ?? '' ) ) {
			$wrap_class[] = 'mdc-reveal-wrap--editor-anim';
		}

		if ( $show_title && 'yes' === ( $settings['mobile_effect'] ?? '' ) ) {
			$wrap_class[] = 'mdc-reveal-wrap--m-fx';
		} elseif ( $show_title && 'yes' === ( $settings['show_title_mobile'] ?? '' ) ) {
			$wrap_class[] = 'mdc-reveal-wrap--title-mobile';
		}

		$opts = [
			'show_card_titles' => 'yes' === ( $settings['show_card_titles'] ?? '' ),
			'card_title_tag'   => Utils::validate_html_tag( $settings['card_title_tag'] ?? 'h3' ),
			'loading'          => $loading,
			'sizes'            => '(max-width: 767px) 50vw, ' . max( 1, (int) round( 100 / $count ) ) . 'vw',
		];

		$this->add_render_attribute( 'wrap', 'class', $wrap_class );
		$this->add_render_attribute(
			'stage',
			[
				'class' => 'mdc-reveal',
				'style' => '--mdc-count:' . $count . ';',
			]
		);
		$this->add_render_attribute( 'title', 'class', 'mdc-reveal__title' );

		$title_done = false;
		?>
		<div <?php $this->print_render_attribute_string( 'wrap' ); ?>>
			<div <?php $this->print_render_attribute_string( 'stage' ); ?>>
				<?php
				foreach ( $cards as $index => $card ) {
					if ( $show_title && ! $title_done && $index === $split ) {
						$this->render_title( $title_tag, $title_text );
						$title_done = true;
					}

					if ( is_array( $card ) ) {
						$this->render_card( $card, $index, $index >= $split, $opts );
					}
				}

				if ( $show_title && ! $title_done ) {
					$this->render_title( $title_tag, $title_text );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Print the centre title.
	 *
	 * @param string $tag  Validated HTML tag.
	 * @param string $text Title text.
	 * @return void
	 */
	private function render_title( $tag, $text ) {
		echo '<' . esc_html( $tag ) . ' ';
		$this->print_render_attribute_string( 'title' );
		echo '>' . esc_html( $text ) . '</' . esc_html( $tag ) . '>';
	}

	/**
	 * Editor live preview. Must mirror render() markup and classes.
	 *
	 * @return void
	 */
	protected function content_template() {
		?>
		<#
		var cards = settings.cards || [];

		if ( cards.length ) {
			var split          = Math.ceil( cards.length / 2 ),
				titleText      = settings.title || '',
				showTitle      = 'yes' === settings.show_title && '' !== titleText,
				showCardTitles = 'yes' === settings.show_card_titles,
				titleTag       = elementor.helpers.validateHTMLTag( settings.title_tag || 'h2' ),
				cardTitleTag   = elementor.helpers.validateHTMLTag( settings.card_title_tag || 'h3' ),
				loading        = settings.image_loading || 'lazy',
				wrapClass      = 'mdc-reveal-wrap ' + ( showTitle ? 'mdc-reveal-wrap--animated' : 'mdc-reveal-wrap--static' ),
				titleHtml      = '';

			if ( 'yes' === settings.clip_overflow ) {
				wrapClass += ' mdc-reveal-wrap--clip';
			}
			if ( 'yes' === settings.editor_anim ) {
				wrapClass += ' mdc-reveal-wrap--editor-anim';
			}
			if ( showTitle && 'yes' === settings.mobile_effect ) {
				wrapClass += ' mdc-reveal-wrap--m-fx';
			} else if ( showTitle && 'yes' === settings.show_title_mobile ) {
				wrapClass += ' mdc-reveal-wrap--title-mobile';
			}
			if ( showTitle ) {
				titleHtml = '<' + titleTag + ' class="mdc-reveal__title">' + _.escape( titleText ) + '</' + titleTag + '>';
			}
		#>
		<div class="{{ wrapClass }}">
			<div class="mdc-reveal" style="--mdc-count:{{ cards.length }};">
				<# _.each( cards, function( card, index ) {
					var isEnd     = index >= split,
						cardTitle = card.title || '',
						link      = card.link || {},
						hasLink   = !! link.url,
						cardTag   = hasLink ? 'a' : 'div',
						attrs     = '',
						media     = card.image || {},
						imgUrl    = '',
						alt       = '',
						imgAttrs  = '';

					if ( hasLink ) {
						var rel = [];
						attrs += ' href="' + _.escape( link.url ) + '"';
						if ( link.is_external ) {
							attrs += ' target="_blank"';
							rel.push( 'noopener' );
						}
						if ( link.nofollow ) {
							rel.push( 'nofollow' );
						}
						if ( rel.length ) {
							attrs += ' rel="' + rel.join( ' ' ) + '"';
						}
						if ( ! showCardTitles && cardTitle ) {
							attrs += ' aria-label="' + _.escape( cardTitle ) + '"';
						}
					}

					if ( media.url || media.id ) {
						imgUrl = elementor.imagesManager.getImageUrl( {
							id: media.id,
							url: media.url,
							size: card.image_size,
							dimension: card.image_custom_dimension,
							model: view.getEditModel()
						} ) || media.url;

						alt = card.image_alt ? card.image_alt : ( showCardTitles ? '' : cardTitle );
						imgAttrs = ' src="' + _.escape( imgUrl ) + '" alt="' + _.escape( alt ) + '"';
						imgAttrs += ' loading="' + ( 'lazy' === loading ? 'lazy' : 'eager' ) + '" decoding="async"';
						if ( 'priority' === loading ) {
							imgAttrs += ' fetchpriority="high"';
						}
					}

					if ( showTitle && index === split ) {
						#>{{{ titleHtml }}}<#
					}
				#>
				<{{{ cardTag }}} class="mdc-reveal__item {{ isEnd ? 'mdc-reveal__item--end' : 'mdc-reveal__item--start' }}" style="--mdc-i:{{ index }};--mdc-s:{{ 0 === index % 2 ? '-1' : '1' }};"{{{ attrs }}}>
					<# if ( imgUrl ) { #>
						<img class="mdc-reveal__image"{{{ imgAttrs }}}>
					<# } #>
					<# if ( showCardTitles && cardTitle ) { #>
						<{{{ cardTitleTag }}} class="mdc-reveal__item-title">{{ cardTitle }}</{{{ cardTitleTag }}}>
					<# } #>
				</{{{ cardTag }}}>
				<# } ); #>
				<# if ( showTitle && split >= cards.length ) { #>
					{{{ titleHtml }}}
				<# } #>
			</div>
		</div>
		<# } #>
		<?php
	}
}
