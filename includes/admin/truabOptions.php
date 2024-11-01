<?php
/**
 * Options Page
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add Menu Page
add_action('admin_menu', 'register_truabilities_menu');

function register_truabilities_menu() {
    add_menu_page('TruAbilities', 'TruAbilities', 'manage_options', 'truAbilities', 'truabilities_user_authenticate', $icon_url = 'dashicons-universal-access-alt', $position = null);
}

/**
 * Callback the options page.
 */
function truabilities_user_authenticate() {

    $getAuthToken = get_option('truabLicense');
    // For JS and CSS Files
    wp_enqueue_style('truab-admin-css', plugins_url('../../css/bootstrap.min.css', __FILE__));
    ?>
	<div class="wrap container-fluid" style="border:1px solid #ccc;">
    <h1 class="wp-heading-inline"><?php esc_html_e('TruAbilities Compliance Connector', 'truab-compliance') ?></h1>
		<?php if ($getAuthToken != '') { ?>
			<div class="alert alert-info" role="alert">
			<?php esc_html_e('Account Successfully Authenticated.', 'truab-compliance') ?> <br>
			<?php esc_html_e('License Key:', 'truab-compliance') ?> <input type="password" value="<?php echo $getAuthToken; ?>" readonly disabled />
			</div>
            <div class="deauth-sec">
                <a href="javascript:void(0);" class="deauth-key btn btn-primary"><?php esc_html_e('Deauthorize License', 'truab-compliance') ?></a>
            </div>
            <div class="form-group col-sm-6">
                <div class="spinner-border text-secondary" role="status" style="display:none">
                <span class="sr-only"><?php esc_html_e('Loading...', 'truab-compliance') ?></span>
                </div>
                <div class="alert alert-danger" role="alert" style="display:none">
                <div class="alert alert-success" role="alert" style="display:none">
                </div>
            </div>
		<?php } else { ?>
			<p><?php esc_html_e('Enter your TruAbilities registered email to authenticate.', 'truab-compliance') ?></p>
			<form action="" class="needs-validation" id="truabAuthForm" method="post">
				<input type="hidden" name="site_url" id="site_url" value="<?php echo site_url(); ?>">
				<div class="form-group col-sm-6">
					<label for="userName"><?php esc_html_e('Email:', 'truab-compliance') ?></label>
					<input type="text" class="form-control" id="userName" placeholder="Enter email" name="username" required>
				</div>
				<div class="form-group col-sm-6">
					<button type="submit" class="btn btn-primary"><?php esc_html_e('Submit', 'truab-compliance') ?></button>
				</div>
				<div class="form-group col-sm-6">
					<div class="spinner-border text-secondary" role="status" style="display:none">
					<span class="sr-only"><?php esc_html_e('Loading...', 'truab-compliance') ?></span>
					</div>
					<div class="alert alert-danger" role="alert" style="display:none">
                    <div class="alert alert-success" role="alert" style="display:none">
					</div>
				</div>
			</form>
		<?php } ?>
	</div>

	<?php
// Call Admin Ajax
    include 'truabAdminAjax.php';

}
// Ajax Request
add_action('wp_ajax_truab_get_user_auth', 'truab_get_user_auth');
add_action('wp_ajax_nopriv_truab_get_user_auth', 'truab_get_user_auth');

function truab_get_user_auth() {
    $userName = sanitize_email(trim($_POST['username']));
    $siteUrl = site_url();

    /* Get JWT Token */
    $getJWTokenEndpoint = 'https://api.truabilities.com/truabilities/api/v1/services/plugin/auth';

    $getJWTokenData = [
        "username" => $userName
    ];
    $argsJWToken = array(
        'method' => 'POST',
        'body'        => json_encode($getJWTokenData),
        'headers'     => array(
            'Content-Type' => 'application/json', 
            'apiKey' => 'jdAuLBMQ4R5IF6KHGtWku3FXjvjEhgZM', 
            'accept-Language'=> 'en'
        )
    );

    $getJWT = json_decode(wp_remote_retrieve_body(wp_remote_post( $getJWTokenEndpoint, $argsJWToken )));
    $tokenOutputRes = $getJWT->data;
    $tokenOutput = $tokenOutputRes[0]->token;
    $endPoint = "https://api.truabilities.com/truabilities/api/v1/services/plugin/license";

    $protocolCheck = $_SERVER['HTTPS'];

    $explodedSecuredURL = explode('https://', $siteUrl);
    $explodedURL = explode('http://', $siteUrl);
    if(count($explodedSecuredURL) > 1) {
        $newURL = $explodedSecuredURL[1];
    } else {
        $newURL = $explodedURL[1];
    }

    $data = [
        'username' => $userName,
        'url' => $newURL,
    ];

    $argsLicenseKey = array(
        'method' => 'POST',
        'body'        => json_encode($data),
        'headers'     => array(
            'Content-Type' => 'application/json', 
            'apiKey' => 'jdAuLBMQ4R5IF6KHGtWku3FXjvjEhgZM', 
            'token' => $tokenOutput
        )
    );

    $userAuth = json_decode(wp_remote_retrieve_body(wp_remote_post( $endPoint, $argsLicenseKey )));
    $apiRespData = $userAuth->data;
    $resp_arr = ['status' => '', 'license_key_encrypted' => '', 'usrmsg' => ''];
    if ($userAuth->status == 200) {
        $resp_arr['license_status'] = $apiRespData[0]->license_status;
        $resp_arr['status'] = $userAuth->status;
        $resp_arr['usrmsg'] = $userAuth->message;

        // Add option
        update_option("truabLicense", $apiRespData[0]->license_key);
    } else {
        $resp_arr['usrmsg'] = $userAuth->message;
    }
    echo json_encode($resp_arr);
    die();
}

add_action('wp_ajax_deauth_truab_key', 'deauth_truab_key_func');
add_action('wp_ajax_nopriv_deauth_truab_key', 'deauth_truab_key_func');

function deauth_truab_key_func() {
    $resp_arr = ['flag' => false, 'msg' => '', 'url' => ''];

    delete_option('truabLicense');

    $resp_arr['flag'] = true;
    $resp_arr['msg'] = 'You have deauthenticated your website.';
    $resp_arr['url'] = admin_url() . 'admin.php?page=truAbilities';

    echo json_encode($resp_arr);
    exit;
}
