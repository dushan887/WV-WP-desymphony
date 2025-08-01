<?php
/**
 * Meta-box for WV Awards: Type + front / back images
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Award_Meta {

	/* ── constants ─────────────────────────────────────────── */
	private const NONCE        = 'wv_award_meta_nonce';
	private const TYPE_FIELD   = '_wv_award_type';
	private const FRONT_FIELD  = '_wv_award_front_image_id';
	private const BACK_FIELD   = '_wv_award_back_image_id';
	private const TYPES        = [ 'Wine', 'Rakija', 'Food' ];

	/* ── hooks ─────────────────────────────────────────────── */
	public static function init() : void {
		add_action( 'add_meta_boxes',           [ __CLASS__, 'register_metabox' ] );
		add_action( 'admin_enqueue_scripts',    [ __CLASS__, 'enqueue_media' ] );
		add_action( 'save_post_wv_award',       [ __CLASS__, 'save_meta' ], 10, 2 );
	}

	/* ── assets ────────────────────────────────────────────── */
	public static function enqueue_media() : void {
		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && 'wv_award' === $screen->post_type ) {
			wp_enqueue_media();
		}
	}

	/* ── UI ───────────────────────────────────────────────── */
	public static function register_metabox() : void {
		add_meta_box(
			'wv_award_meta',
			__( 'Award Details', DS_THEME_TEXTDOMAIN ),
			[ __CLASS__, 'render_metabox' ],
			'wv_award',
			'normal',
			'high'
		);
	}

	public static function render_metabox( \WP_Post $post ) : void {
		wp_nonce_field( self::NONCE, self::NONCE );

		$type       = get_post_meta( $post->ID, self::TYPE_FIELD,  true );
		$front_id   = (int) get_post_meta( $post->ID, self::FRONT_FIELD, true );
		$back_id    = (int) get_post_meta( $post->ID, self::BACK_FIELD,  true );

		$front_src  = $front_id ? wp_get_attachment_image_src( $front_id, 'medium' )[0] : '';
		$back_src   = $back_id  ? wp_get_attachment_image_src( $back_id,  'medium' )[0] : '';
		?>

		<p>
			<label for="wv_award_type_field"><?php esc_html_e( 'Type', DS_THEME_TEXTDOMAIN ); ?></label><br>
			<select name="wv_award_type_field" id="wv_award_type_field" style="width:100%;">
				<option value=""><?php esc_html_e( 'Select type…', DS_THEME_TEXTDOMAIN ); ?></option>
				<?php foreach ( self::TYPES as $opt ) : ?>
					<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $type, $opt ); ?>><?php echo esc_html( $opt ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<strong><?php esc_html_e( 'Front', DS_THEME_TEXTDOMAIN ); ?></strong><br>
			<div id="wv_award_front_container"><?php if ( $front_src ) : ?><img src="<?php echo esc_url( $front_src ); ?>" style="max-width:100%;margin-bottom:10px;" /><?php endif; ?></div>
			<input type="hidden" id="wv_award_front_image_id" name="wv_award_front_image_id" value="<?php echo esc_attr( $front_id ); ?>">
			<button type="button" class="button" id="wv_award_set_front"><?php esc_html_e( 'Set Front', DS_THEME_TEXTDOMAIN ); ?></button>
			<button type="button" class="button" id="wv_award_remove_front"><?php esc_html_e( 'Remove Front', DS_THEME_TEXTDOMAIN ); ?></button>
		</p>

		<p>
			<strong><?php esc_html_e( 'Back', DS_THEME_TEXTDOMAIN ); ?></strong><br>
			<div id="wv_award_back_container"><?php if ( $back_src ) : ?><img src="<?php echo esc_url( $back_src ); ?>" style="max-width:100%;margin-bottom:10px;" /><?php endif; ?></div>
			<input type="hidden" id="wv_award_back_image_id" name="wv_award_back_image_id" value="<?php echo esc_attr( $back_id ); ?>">
			<button type="button" class="button" id="wv_award_set_back"><?php esc_html_e( 'Set Back', DS_THEME_TEXTDOMAIN ); ?></button>
			<button type="button" class="button" id="wv_award_remove_back"><?php esc_html_e( 'Remove Back', DS_THEME_TEXTDOMAIN ); ?></button>
		</p>

		<script>
		jQuery(function ($) {

			var frameFront, frameBack;

			// Front image
			$('#wv_award_set_front').on('click', function(e){
				e.preventDefault();
				if ( frameFront ) { frameFront.open(); return; }
				frameFront = wp.media({
					title: '<?php echo esc_js( __( 'Select Front', DS_THEME_TEXTDOMAIN ) ); ?>',
					button:{ text: '<?php echo esc_js( __( 'Use this front image', DS_THEME_TEXTDOMAIN ) ); ?>' },
					multiple: false
				});
				frameFront.on('select', function(){
					var img = frameFront.state().get('selection').first().toJSON();
					$('#wv_award_front_image_id').val(img.id);
					$('#wv_award_front_container').html('<img src="'+img.url+'" style="max-width:100%;margin-bottom:10px;" />');
				});
				frameFront.open();
			});

			$('#wv_award_remove_front').on('click', function(e){
				e.preventDefault();
				$('#wv_award_front_image_id').val('');
				$('#wv_award_front_container').empty();
			});

			// Back image
			$('#wv_award_set_back').on('click', function(e){
				e.preventDefault();
				if ( frameBack ) { frameBack.open(); return; }
				frameBack = wp.media({
					title: '<?php echo esc_js( __( 'Select Back', DS_THEME_TEXTDOMAIN ) ); ?>',
					button:{ text: '<?php echo esc_js( __( 'Use this back image', DS_THEME_TEXTDOMAIN ) ); ?>' },
					multiple: false
				});
				frameBack.on('select', function(){
					var img = frameBack.state().get('selection').first().toJSON();
					$('#wv_award_back_image_id').val(img.id);
					$('#wv_award_back_container').html('<img src="'+img.url+'" style="max-width:100%;margin-bottom:10px;" />');
				});
				frameBack.open();
			});

			$('#wv_award_remove_back').on('click', function(e){
				e.preventDefault();
				$('#wv_award_back_image_id').val('');
				$('#wv_award_back_container').empty();
			});
		});
		</script>
		<?php
	}

	/* ── save ─────────────────────────────────────────────── */
	public static function save_meta( int $post_id, \WP_Post $post ) : void {

		if (
			! isset( $_POST[ self::NONCE ] ) ||
			! wp_verify_nonce( $_POST[ self::NONCE ], self::NONCE ) ||
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		// Type
		if ( isset( $_POST['wv_award_type_field'] ) ) {
			$val = sanitize_text_field( wp_unslash( $_POST['wv_award_type_field'] ) );
			if ( in_array( $val, self::TYPES, true ) ) {
				update_post_meta( $post_id, self::TYPE_FIELD, $val );
			} else {
				delete_post_meta( $post_id, self::TYPE_FIELD );
			}
		}

		// Front image
		if ( isset( $_POST['wv_award_front_image_id'] ) ) {
			update_post_meta( $post_id, self::FRONT_FIELD, intval( $_POST['wv_award_front_image_id'] ) );
		} else {
			delete_post_meta( $post_id, self::FRONT_FIELD );
		}

		// Back image
		if ( isset( $_POST['wv_award_back_image_id'] ) ) {
			update_post_meta( $post_id, self::BACK_FIELD, intval( $_POST['wv_award_back_image_id'] ) );
		} else {
			delete_post_meta( $post_id, self::BACK_FIELD );
		}
	}
}
