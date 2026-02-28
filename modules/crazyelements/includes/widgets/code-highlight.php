<?php
namespace CrazyElements;

use CrazyElements\PrestaHelper;

if (!defined('_PS_VERSION_')) {
    exit; // Exit if accessed directly.
}

class Widget_Code_Highlight extends Widget_Base {

	public function get_name() {
		return 'code-highlight';
	}

	public function get_title() {
		return PrestaHelper::__( 'Code Highlight', 'elementor' );
	}

	public function get_icon() {
		return 'ceicon-code-highlight';
	}

	public function get_categories() {
		return array( 'crazy_addons_free' );
	}

	public function get_keywords() {
		return [ 'code', 'highlight', 'syntax', 'highlighter', 'javascript', 'css', 'php', 'html', 'java', 'js' ];
	}

	public function get_style_depends() {
		return [ 'prismjs_style' ];
	}
	
	public function get_script_depends() {
		$depends = [
			'prismjs_core' => true,
			'prismjs_loader' => true,
			'prismjs_normalize' => true,
			'highlight_handler' => true,
			'prismjs_line_numbers' => true,
			'prismjs_line_highlight' => true,
			'prismjs_copy_to_clipboard' => true,
		];

		if ( ! Plugin::$instance->preview->is_preview_mode() ) {
			$settings = $this->get_settings_for_display();

			if ( ! $settings['line_numbers'] ) {
				unset( $depends['prismjs_line_numbers'] );
			}

			if ( ! $settings['highlight_lines'] ) {
				unset( $depends['prismjs_line_highlight'] );
			}

			if ( ! $settings['copy_to_clipboard'] ) {
				unset( $depends['prismjs_copy_to_clipboard'] );
			}
		}

		return array_keys( $depends );
	}

	public function get_css_config() {
		// This widget is loading its own CSS using get_style_depends.
		return [
			'key' => $this->get_group_name(),
			'version' => CRAZY_VERSION,
			'file_path' => '',
			'data' => [],
		];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => PrestaHelper::__( 'Code Highlight', 'elementor' ),
			]
		);

		$language_option = [
			'markup' => 'Markup',
			'html' => 'HTML',
			'css' => 'CSS',
			'sass' => 'Sass (Sass)',
			'scss' => 'Sass (Scss)',
			'less' => 'Less',
			'javascript' => 'JavaScript',
			'typescript' => 'TypeScript',
			'jsx' => 'React JSX',
			'tsx' => 'React TSX',
			'php' => 'PHP',
			'ruby' => 'Ruby',
			'json' => 'JSON + Web App Manifest',
			'http' => 'HTTP',
			'xml' => 'XML',
			'svg' => 'SVG',
			'rust' => 'Rust',
			'csharp' => 'C#',
			'dart' => 'Dart',
			'git' => 'Git',
			'java' => 'Java',
			'sql' => 'SQL',
			'go' => 'Go',
			'kotlin' => 'Kotlin + Kotlin Script',
			'julia' => 'Julia',
			'python' => 'Python',
			'swift' => 'Swift',
			'bash' => 'Bash + Shell',
			'scala' => 'Scala',
			'haskell' => 'Haskell',
			'perl' => 'Perl',
			'objectivec' => 'Objective-C',
			'visual-basic,' => 'Visual Basic + VBA',
			'r' => 'R',
			'c' => 'C',
			'cpp' => 'C++',
			'aspnet' => 'ASP.NET (C#)',
		];

		$this->add_control(
			'language',
			[
				'label' => PrestaHelper::__( 'Language', 'elementor' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => false,
				'options' => PrestaHelper::apply_filters( 'elementor_pro/code_highlight/languages', $language_option ),
				'default' => 'javascript',
			]
		);

		$this->add_control(
			'code',
			[
				'label' => PrestaHelper::__( 'Code', 'elementor' ),
				'type' => Controls_Manager::CODE,
				'default' => 'console.log( \'Code is Poetry\' );',
				'dynamic' => [
					'active' => true,
					'categories' => [
						//TagsModule::TEXT_CATEGORY,
					],
				],
			]
		);

		$this->add_control(
			'line_numbers',
			[
				'label' => PrestaHelper::__( 'Line Numbers', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'line-numbers',
				'default' => 'line-numbers',
			]
		);

		$this->add_control(
			'copy_to_clipboard',
			[
				'label' => PrestaHelper::__( 'Copy to Clipboard', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => PrestaHelper::__( 'On', 'elementor' ),
				'label_off' => PrestaHelper::__( 'Off', 'elementor' ),
				'return_value' => 'copy-to-clipboard',
				'default' => 'copy-to-clipboard',
			]
		);

		$this->add_control(
			'highlight_lines',
			[
				'label' => PrestaHelper::__( 'Highlight Lines', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => '1, 3-6',
			]
		);

		$this->add_control(
			'word_wrap',
			[
				'label' => PrestaHelper::__( 'Word Wrap', 'elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => PrestaHelper::__( 'On', 'elementor' ),
				'label_off' => PrestaHelper::__( 'Off', 'elementor' ),
				'return_value' => 'word-wrap',
				'default' => '',
			]
		);

		$this->add_control(
			'theme',
			[
				'label' => PrestaHelper::__( 'Theme', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => 'Solid',
					'dark' => 'Dark',
					'okaidia' => 'Okaidia',
					'solarizedlight' => 'Solarizedlight',
					'tomorrow' => 'Tomorrow',
					'twilight' => 'Twilight',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => PrestaHelper::__( 'Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em' ],
				'range' => [
					'px' => [
						'min' => 115,
						'max' => 1000,
					],
					'em' => [
						'min' => 6,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .highlight-height' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'font_size',
			[
				'label' => PrestaHelper::__( 'Font Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vw' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
					],
					'vw' => [
						'min' => 0.1,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'responsive' => true,
				'selectors' => [
					'{{WRAPPER}} pre, {{WRAPPER}} code, {{WRAPPER}} .line-numbers .line-numbers-rows' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( PrestaHelper::is_admin() ) {
			return;
		}
		$settings = $this->get_settings_for_display();
		$base_url = 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.23.0';
		$jsfiles = array(
			'prismjs_core'=> $base_url . '/components/prism-core.min.js',
			'prismjs_loader'=> $base_url . '/plugins/autoloader/prism-autoloader.min.js',
			'prismjs_normalize'=> $base_url . '/plugins/normalize-whitespace/prism-normalize-whitespace.min.js',
			'prismjs_line_numbers'=> $base_url . '/plugins/line-numbers/prism-line-numbers.min.js',
			'prismjs_line_highlight'=> $base_url . '/plugins/line-highlight/prism-line-highlight.min.js',
			'prismjs_toolbar'=> $base_url . '/plugins/toolbar/prism-toolbar.min.js',
			'prismjs_copy_to_clipboard'=> $base_url . '/plugins/copy-to-clipboard/prism-copy-to-clipboard.min.js'
		);
		$context = \Context::getContext();
		foreach($jsfiles as $id => $js){
			$context->controller->registerJavascript(
				$id,
				$js,
				array(
					'position' => 'bottom',
					'priority' => 100,
					'server' => 'remote',
				)
			);
		}
		?>
		<div class="<?php  echo 'prismjs-' . $settings['theme']; ?> <?php echo PrestaHelper::esc_attr( $settings['copy_to_clipboard'] ); ?> <?php  echo PrestaHelper::esc_attr( $settings['word_wrap'] ); ?>">
			<pre data-line="<?php echo PrestaHelper::esc_attr( $settings['highlight_lines'] ); ?>" class="highlight-height language-<?php echo PrestaHelper::esc_attr( $settings['language'] ); ?> <?php echo PrestaHelper::esc_attr( $settings['line_numbers'] ); ?>">
				<code readonly="true" class="language-<?php echo PrestaHelper::esc_attr( $settings['language'] ); ?>">
					<xmp><?php $this->print_unescaped_setting( 'code' );?></xmp>
				</code>
			</pre>
		</div>
		<?php
	}

	protected function content_template() {
		?>
		<div class="prismjs-{{{ settings.theme }}} {{{settings.copy_to_clipboard}}} {{{settings.word_wrap}}}">
			<pre data-line="{{{settings.highlight_lines }}}" class="highlight-height language-{{{ settings.language }}} {{{ settings.line_numbers }}}">
				<code readonly="true" class="language-{{{ settings.language }}}">
					<xmp>{{{ settings.code }}}</xmp>
				</code>
			</pre>
		</div>
		<?php
	}
}
