<?php
/**
 * Meta-box for WV Cards: URL, target, front/back + link-front/link-back images.
 *
 * @package Desymphony
 */

namespace Desymphony\Theme;

defined( 'ABSPATH' ) || exit;

class DS_Card_Meta {

	/* ── hooks ─────────────────────────────────────────────── */
	public static function init() {
		add_action( 'add_meta_boxes',        [ __CLASS__, 'register_metabox' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_media' ] );
		add_action( 'save_post_wv_card',     [ __CLASS__, 'save_meta' ], 10, 2 );
	}

	public static function enqueue_media() : void {
		$screen = get_current_screen();
		if ( isset( $screen->post_type ) && 'wv_card' === $screen->post_type ) {
			wp_enqueue_media();
		}
	}

	/* ── UI ───────────────────────────────────────────────── */
	public static function register_metabox() : void {
		add_meta_box(
			'wv_card_meta',
			__( 'Card Details', DS_THEME_TEXTDOMAIN ),
			[ __CLASS__, 'render_metabox' ],
			'wv_card',
			'normal',
			'high'
		);
	}

	public static function render_metabox( \WP_Post $post ) : void {
		wp_nonce_field( 'wv_card_meta_nonce', 'wv_card_meta_nonce' );

		$url            = get_post_meta( $post->ID, '_wv_card_url', true );
		$target         = get_post_meta( $post->ID, '_wv_card_target', true ) ?: '_blank';

		$front_id       = (int) get_post_meta( $post->ID, '_wv_card_front_image_id',       true );
		$back_id        = (int) get_post_meta( $post->ID, '_wv_card_back_image_id',        true );
		$link_front_id  = (int) get_post_meta( $post->ID, '_wv_card_link_front_image_id',  true );
		$link_back_id   = (int) get_post_meta( $post->ID, '_wv_card_link_back_image_id',   true );

		$front_src      = $front_id      ? wp_get_attachment_image_src( $front_id,      'medium' )[0] : '';
		$back_src       = $back_id       ? wp_get_attachment_image_src( $back_id,       'medium' )[0] : '';
		$link_front_src = $link_front_id ? wp_get_attachment_image_src( $link_front_id, 'medium' )[0] : '';
		$link_back_src  = $link_back_id  ? wp_get_attachment_image_src( $link_back_id,  'medium' )[0] : '';
		?>

		<p>
			<label for="wv_card_url"><?php esc_html_e( 'Card URL', DS_THEME_TEXTDOMAIN ); ?></label><br>
			<input type="url" id="wv_card_url" name="wv_card_url" value="<?php echo esc_url( $url ); ?>" style="width:100%;">
		</p>

		<p>
			<label><?php esc_html_e( 'Link Target', DS_THEME_TEXTDOMAIN ); ?></label><br>
			<label><input type="radio" name="wv_card_target" value="_blank" <?php checked( $target, '_blank' ); ?>> _blank</label><br>
			<label><input type="radio" name="wv_card_target" value="_self"  <?php checked( $target, '_self' );  ?>> _self</label>
		</p>

		<?php
		self::image_field( 'Front',       'front',       $front_id,      $front_src );
		self::image_field( 'Back',        'back',        $back_id,       $back_src );
		self::image_field( 'Link Front',  'link_front',  $link_front_id, $link_front_src );
		self::image_field( 'Link Back',   'link_back',   $link_back_id,  $link_back_src );
	}

	/* helper to render each image picker */
	private static function image_field( string $label, string $slug, int $id, string $src ) : void { ?>
		<p>
			<strong><?php echo esc_html( $label ); ?></strong><br>
			<div id="wv_card_<?php echo esc_attr( $slug ); ?>_container">
				<?php if ( $src ) : ?>
					<img src="<?php echo esc_url( $src ); ?>" style="max-width:100%;margin-bottom:10px;" />
				<?php endif; ?>
			</div>
			<input type="hidden"
				   id="wv_card_<?php echo esc_attr( $slug ); ?>_image_id"
				   name="_wv_card_<?php echo esc_attr( $slug ); ?>_image_id"
				   value="<?php echo esc_attr( $id ); ?>">
			<button type="button" class="button" id="wv_card_set_<?php echo esc_attr( $slug ); ?>">
				<?php esc_html_e( 'Set', DS_THEME_TEXTDOMAIN ); ?>
			</button>
			<button type="button" class="button" id="wv_card_remove_<?php echo esc_attr( $slug ); ?>">
				<?php esc_html_e( 'Remove', DS_THEME_TEXTDOMAIN ); ?>
			</button>
		</p>
	<?php }

	/* ── save ─────────────────────────────────────────────── */
	public static function save_meta( $post_id, $post ) : void {

		if (
			! isset( $_POST['wv_card_meta_nonce'] ) ||
			! wp_verify_nonce( $_POST['wv_card_meta_nonce'], 'wv_card_meta_nonce' ) ||
			( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
			! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		// URL & target
		update_post_meta( $post_id, '_wv_card_url',    isset( $_POST['wv_card_url'] )    ? esc_url_raw( wp_unslash( $_POST['wv_card_url'] ) )    : '' );
		update_post_meta( $post_id, '_wv_card_target', isset( $_POST['wv_card_target'] ) ? sanitize_text_field( wp_unslash( $_POST['wv_card_target'] ) ) : '_blank' );

		// images
		self::save_image( $post_id, 'front' );
		self::save_image( $post_id, 'back' );
		self::save_image( $post_id, 'link_front' );
		self::save_image( $post_id, 'link_back' );
	}

	private static function save_image( int $post_id, string $slug ) : void {
		$key = "_wv_card_{$slug}_image_id";
		if ( isset( $_POST[ $key ] ) && $_POST[ $key ] !== '' ) {
			update_post_meta( $post_id, $key, (int) $_POST[ $key ] );
		} else {
			delete_post_meta( $post_id, $key );
		}
	}
}

/* ── JS (one generic handler) ───────────────────────────── */
add_action( 'admin_footer', function () {
	$screen = get_current_screen();
	if ( ! isset( $screen->post_type ) || 'wv_card' !== $screen->post_type ) { return; } ?>
	<script>
	jQuery(function ($) {
		$('[id^="wv_card_set_"]').on('click', function (e) {
			e.preventDefault();
			const slug  = this.id.replace('wv_card_set_', '');
			const frame = wp.media({ title: 'Select image', button:{ text:'Use image' }, multiple:false });
			frame.on('select', function(){
				const img = frame.state().get('selection').first().toJSON();
				$('#wv_card_'+slug+'_image_id').val(img.id);
				$('#wv_card_'+slug+'_container').html('<img src="'+img.url+'" style="max-width:100%;margin-bottom:10px;" />');
			});
			frame.open();
		});
		$('[id^="wv_card_remove_"]').on('click', function (e) {
			e.preventDefault();
			const slug = this.id.replace('wv_card_remove_', '');
			$('#wv_card_'+slug+'_image_id').val('');
			$('#wv_card_'+slug+'_container').empty();
		});
	});
	</script>
<?php } );
