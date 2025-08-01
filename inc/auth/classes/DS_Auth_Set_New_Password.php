<?php
/**
 * “Choose new password” page – reached via ?rp_key=…&login=…
 * Shortcode: [wv_addon_set_password_form]
 */
namespace Desymphony\Auth\Classes;

if ( ! defined( 'ABSPATH' ) ) { exit; }

class DS_Auth_Set_New_Password {

	private const HP_FIELD = 'wv_hp';

	public function __construct() {
		add_shortcode( 'wv_addon_set_password_form', [ $this, 'render_form' ] );
		add_action( 'wp_ajax_nopriv_wv_addon_set_password', [ $this, 'ajax_set_password' ] );
		add_action( 'wp_ajax_wv_addon_set_password',        [ $this, 'ajax_set_password' ] );
	}

	/* ───────────────────────────────
    *  Shortcode markup
    * ─────────────────────────────── */
    public function render_form(): string {

        $key   = sanitize_text_field( $_GET['rp_key'] ?? '' );
        $login = sanitize_text_field( $_GET['login']  ?? '' );

        if ( empty( $key ) || empty( $login ) ) {
            return '<p class="text-center">'.esc_html__( 'Invalid or expired link.', DS_THEME_TEXTDOMAIN ).'</p>';
        }

        ob_start(); ?>
    <style>
    #wv-progress-bar{display:none!important}
    #wv-setpass-wrapper{
        min-height:100vh;
        background:url(https://winevisionfair.com/wp-content/uploads/2025/06/DSK_Reset_password_Bck.jpg) center/cover no-repeat;
    }
    @media(max-width:768px){
        #wv-setpass-wrapper{background-image:url(https://winevisionfair.com/wp-content/uploads/2025/06/MOB_Reset_password_Bck.jpg)}
    }
    .auth-box{
        background:linear-gradient(180deg,var(--wv-c),transparent);
        border-radius:1rem;
    }
    #wv-setpass-messages .error{color:#e74c3c}
    .wv-toggle-pass{
        position:absolute;top:50%;right:1rem;transform:translateY(-50%);
        font-size:1.25rem;cursor:pointer;color:var(--wv-c_50);
    }
    </style>

    <div id="wv-setpass-messages" class="text-center"></div>
    <div id="wv-setpass-wrapper" class="wv-auth-wrapper d-flex justify-content-center py-64" >
    <div class="container container-1024">
    <div class="row justify-content-center">
    <div class="col-lg-6">

        <form id="wv-setpass-form" class="wv-auth-form wv-block br-16 p-32" action="#">
        <div class="text-center mb-32">
            <img src="https://winevisionfair.com/wp-content/uploads/2025/06/Header_Logo_Info_DARK.svg" alt="Logo" class="w-100">
        </div>
        <h2 class="fw-600 my-32 text-center">New password</h2>
        <div class="p-32 auth-box">

            <!-- New password -->
            <label for="wv_new_pass1" class="fw-400 d-block mb-8 wv-color-w"><?php esc_html_e('New password',DS_THEME_TEXTDOMAIN);?></label>
            <div class="wv-input-group mb-12 position-relative">
                <input class="wv-bg-w lh-2 w-100"
                    type="password" id="wv_new_pass1" required
                    placeholder="<?php esc_attr_e('Enter your password',DS_THEME_TEXTDOMAIN);?>">
                <span class="wv-toggle-pass wv wv_show" role="button" tabindex="0" aria-label="Show password"></span>
            </div>
            <p class="fs-12 mb-12 border-bottom pb-12 wv-bc-w wv-color-w"><?php esc_html_e('Minimum 10 characters with capital letters and numbers',DS_THEME_TEXTDOMAIN);?></p>

            <!-- Confirm password -->
            <label for="wv_new_pass2" class="fw-400 d-block mb-8 wv-color-w"><?php esc_html_e('Confirm new password',DS_THEME_TEXTDOMAIN);?></label>
            <div class="wv-input-group mb-12 position-relative">
                <input class="wv-bg-w lh-2 w-100"
                    type="password" id="wv_new_pass2" required
                    placeholder="<?php esc_attr_e('Re-type your password',DS_THEME_TEXTDOMAIN);?>">
                <span class="wv-toggle-pass wv wv_show" role="button" tabindex="0" aria-label="Show password"></span>
            </div>
            <p class="fs-12 mb-12 border-bottom pb-12 wv-bc-w wv-color-w"><?php esc_html_e('Re-type your password',DS_THEME_TEXTDOMAIN);?></p>

            <?php
                printf('<input type="text" name="%s" tabindex="-1" style="position:absolute;left:-9999px" aria-hidden="true" />',esc_attr(self::HP_FIELD));
                wp_nonce_field( 'wv_set_password_nonce', 'wv_set_password_nonce_field' );
            ?>
            <input type="hidden" id="wv_login"  value="<?php echo esc_attr($login);?>">
            <input type="hidden" id="wv_rp_key" value="<?php echo esc_attr($key);?>">

            <div class="d-block text-center pt-24">
                <button type="submit" class="wv-button wv-button-lg wv-button-pill wv-button-c2 py-12">
                     <?php esc_html_e('Set new password',DS_THEME_TEXTDOMAIN);?>
                </button>
            </div>
        </div>
        </form>

    </div>
    </div>
    </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded',()=>{
    /* toggle eye icons */
    document.querySelectorAll('.wv-toggle-pass').forEach(t=>{
        t.addEventListener('click',()=>switchVis(t));
        t.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' ')switchVis(t);});
    });
    function switchVis(toggle){
        const input = toggle.parentElement.querySelector('input[type="password"],input[type="text"]');
        const shown = input.type==='text';
        input.type  = shown?'password':'text';
        toggle.classList.toggle('wv_show', shown);
        toggle.classList.toggle('wv_hide',!shown);
        toggle.setAttribute('aria-label', shown?'Show password':'Hide password');
    }

    /* submit handler (unchanged validation / fetch) */
    const f   = document.getElementById('wv-setpass-form'),
            p1  = document.getElementById('wv_new_pass1'),
            p2  = document.getElementById('wv_new_pass2'),
            box = document.getElementById('wv-setpass-messages');

    const ajaxUrl = (window.wvAddonAjax&&wvAddonAjax.ajaxUrl) || '<?php echo admin_url('admin-ajax.php');?>';

    f.addEventListener('submit',e=>{
        e.preventDefault(); box.textContent='';
        if(p1.value!==p2.value){box.innerHTML='<div class="error"><?php echo esc_js(__('Passwords do not match.',DS_THEME_TEXTDOMAIN));?></div>';return;}
        if(p1.value.length<10){box.innerHTML='<div class="error"><?php echo esc_js(__('Password must be at least 10 characters.',DS_THEME_TEXTDOMAIN));?></div>';return;}

        const data=new URLSearchParams({
        action :'wv_addon_set_password',
        nonce  :f.querySelector('[name="wv_set_password_nonce_field"]').value,
        login  :document.getElementById('wv_login').value,
        rp_key :document.getElementById('wv_rp_key').value,
        pass1  :p1.value,
        pass2  :p2.value,
        <?php echo self::HP_FIELD;?>:f.querySelector('[name="<?php echo self::HP_FIELD;?>"]').value
        });

        fetch(ajaxUrl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:String(data)})
        .then(r=>r.json())
        .then(r=>{ if(r.success){window.location.href=r.data.redirect;} else{box.innerHTML='<div class="error">'+r.data.message+'</div>';}})
        .catch(()=>{box.innerHTML='<div class="error"><?php echo esc_js(__('Something went wrong.',DS_THEME_TEXTDOMAIN));?></div>';});
    });
    });
    </script>
    <?php
        return ob_get_clean();
    }



	/* ───────────── AJAX: validate key & save password ───────────── */
	public function ajax_set_password(): void {

		check_ajax_referer( 'wv_set_password_nonce', 'nonce' );

		if ( ! empty( $_POST[ self::HP_FIELD ] ?? '' ) ) {
			wp_send_json_error( [ 'message' => __( 'Spam detected.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$login  = sanitize_text_field( $_POST['login']  ?? '' );
		$key    = sanitize_text_field( $_POST['rp_key'] ?? '' );
		$p1     = (string) ( $_POST['pass1'] ?? '' );
		$p2     = (string) ( $_POST['pass2'] ?? '' );

		if ( $p1 !== $p2 ) { wp_send_json_error( [ 'message'=>__( 'Passwords do not match.', DS_THEME_TEXTDOMAIN ) ] ); }
		if ( strlen( $p1 ) < 10 ) { wp_send_json_error( [ 'message'=>__( 'Password must be at least 10 characters.', DS_THEME_TEXTDOMAIN ) ] ); }
		if ( ! preg_match('/[A-Z]/',$p1) || ! preg_match('/\d/',$p1) ) {
			wp_send_json_error( [ 'message'=>__( 'Password must include an uppercase letter and a number.', DS_THEME_TEXTDOMAIN ) ] );
		}

		$user = check_password_reset_key( $key, $login );
		if ( is_wp_error( $user ) ) {
			wp_send_json_error( [ 'message'=>__( 'Invalid or expired reset link.', DS_THEME_TEXTDOMAIN ) ] );
		}

		reset_password( $user, $p1 );

		wp_send_json_success( [
			'message'  => __( 'Password updated.', DS_THEME_TEXTDOMAIN ),
			'redirect' => home_url( '/login/' ),
		] );
	}
}
