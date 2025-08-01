<?php
namespace Desymphony\Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Desymphony\Auth\Classes\DS_Auth_Page_Guard;
use Desymphony\Auth\Classes\DS_Role_Manager;
use Desymphony\Auth\Classes\DS_Auth_Login;
use Desymphony\Auth\Classes\DS_Auth_Registration;
use Desymphony\Auth\Classes\DS_Auth_Password_Reset;
use Desymphony\Auth\Classes\DS_Auth_Set_New_Password;
use Desymphony\Auth\Classes\DS_Auth_Profile;
use Desymphony\Auth\Classes\DS_Auth_2FA;
use Desymphony\Auth\Classes\DS_Auth_Email_Confirmation;
use Desymphony\Auth\Classes\DS_Auth_Thank_You;
use Desymphony\Auth\Classes\DS_Admin_User_Table;
use Desymphony\Auth\Classes\DS_Admin_Notification;
use Desymphony\Auth\Classes\DS_Auth_Legacy_Blocker;





class DS_Auth_Init {

    private string $version;

    public function __construct( string $version ) {
        $this->version = $version;

        DS_Role_Manager::add_roles();

        $page_guard = new DS_Auth_Page_Guard();

        $login          = new DS_Auth_Login();
		$registration   = new DS_Auth_Registration();
		$password_reset = new DS_Auth_Password_Reset();
        $set_new_pass   = new DS_Auth_Set_New_Password();
		$two_fa         = new DS_Auth_2FA();
		$email_confirm  = new DS_Auth_Email_Confirmation();
		$thank_you_page = new DS_Auth_Thank_You();
        $admin_user_table = new DS_Admin_User_Table();
        $admin_notification = new DS_Admin_Notification();
        $legacy_blocker = new DS_Auth_Legacy_Blocker();
        
		
        $GLOBALS['wv_register_controller'] = $registration;


    }
}