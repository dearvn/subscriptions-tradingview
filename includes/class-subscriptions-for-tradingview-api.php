<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements TradingView Api
 *
 * @class   Subscriptions_For_TradingView_Api
 * @since   1.0.0
 * @author  TDV
 * @package TradingView Subscription
 */

if ( ! class_exists( 'Subscriptions_For_TradingView_Api' ) ) {

	/**
	 * Class Subscriptions_For_TradingView_Api
	 */
	class Subscriptions_For_TradingView_Api {

		/**
		 * Single instance of the class
		 *
		 * @var \TWS_Tradingview
		 */
		protected static $instance;

        /**
		 * Token Id
		 *
		 * @var string
		 */
		private $token = '';
        
        /**
		 * Session Id
		 *
		 * @var string
		 */
		private $sessionid = '';
        
        private $urls = array(
            "tvcoins" => "https://www.tradingview.com/tvcoins/details/",
            "username_hint" => "https://www.tradingview.com/username_hint/",
            "list_users" => "https://www.tradingview.com/pine_perm/list_users/",
            "modify_access" => "https://www.tradingview.com/pine_perm/modify_user_expiration/",
            "add_access" => "https://www.tradingview.com/pine_perm/add/",
            "remove_access" => "https://www.tradingview.com/pine_perm/remove/",
            "pub_scripts" => "https://www.tradingview.com/pubscripts-get/",
            "pri_scripts" => "https://www.tradingview.com/pine_perm/list_scripts/",
            "pine_facade" => "https://pine-facade.tradingview.com/pine-facade/get/",
        );
        
		/**
		 * Returns single instance of the class
		 *
		 * @return \TWS_Tradingview
		 * @since 1.0.0
		 */
		/*public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}*/

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {
            //add_filter( 'tws_customer_subscription_payment_done_mail', array( $this, 'add_account_tradingview_into_chart' ), 10, 3 );
            $key = get_option('wps_tdv_token');
            if ($key) {
            
                $headers = array(
                    'cookie' => 'sessionid='.$key
                );
            
                $test = wp_remote_get( $this->urls["tvcoins"], 
                    array(
                        'headers' => $headers
                    )
                );
                if ( wp_remote_retrieve_response_code( $test ) == 200 ) {
                    $this->sessionid = $key;
                }
            }
            
            if (empty($this->sessionid)) {
                $username = get_option( 'tws_tradingview_username');
                $password = get_option( 'tws_tradingview_password');

                $payload = array(
                    'username' => trim($username),
                    'password' => $password,
                    'remember' => 'on'
                );
                //body, contentType = encode_multipart_formdata(payload)
                $userAgent = 'TWAPI/3.0 (Darwin; Darwin Kernel Version 19.4.0: Wed Mar  4 22:28:40 PST 2020; root:xnu-6153.101.6~15/RELEASE_X86_64; 19.4.0)';
                
                $login_headers = array(
                    'origin' => 'https://www.tradingview.com',
                    'User-Agent' => $userAgent,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'referer' => 'https://www.tradingview.com'
                );
                
                $login = wp_remote_post( 'https://www.tradingview.com/accounts/signin/', 
                    array(
                        'body'    => $payload,
                        'headers' => $login_headers
                    )
                );
                if ( wp_remote_retrieve_response_code( $login ) == 200 ) {
                    $sessionid = wp_remote_retrieve_cookie_value($login, 'sessionid');
                    if (!empty($sessionid)) {
                        $this->sessionid = $sessionid;
                        update_option('tradingview_session', $sessionid);
                    }
                }
            }
            
            if (empty($this->sessionid)) {
                do_action( 'tws_tradingview_disconnect_mail', $this );
            }
        }
        
        public function getPrivateIndicators() {
            $headers = array(
                'cookie' => 'sessionid='.$this->sessionid
            );

            $resp = wp_remote_get( $this->urls["pri_scripts"], 
                array(
                    'headers' => $headers
                )
            );
            $datas = [];
            if ( wp_remote_retrieve_response_code( $resp ) == 200 ) {
                $data = wp_remote_retrieve_body($resp);
                $datas = (array)json_decode( $data );
            }
            
            
            if (empty($datas)) {
                return [];
            }
            
            $resp = wp_remote_post( $this->urls["pub_scripts"], 
                array(
                    'headers' => $headers,
                    'body' => [
                        'scriptIdPart' => implode(",", $datas),
                        'show_hidden' => true]
                )
            );
            $indicators = [];
            if ( wp_remote_retrieve_response_code( $resp ) == 200 ) {
                $data = wp_remote_retrieve_body($resp);
                $items = (array)json_decode( $data );
                foreach($items as $item) {
                    $indicators[] = [
                        'id' => $item->scriptIdPart,
                        'name' => $item->scriptName
                    ];
                }
            }
            
            return $indicators;
        }
        
        public function getNameChart($pine_id) {
            $headers = array(
                'cookie' => 'sessionid='.$this->sessionid
            );

            $resp = wp_remote_get( $this->urls["pine_facade"]."{$pine_id}/1?no_4xx=true", 
                array(
                    'headers' => $headers
                )
            );
            $datas = [];
            if ( wp_remote_retrieve_response_code( $resp ) == 200 ) {
                $data = wp_remote_retrieve_body($resp);
                $datas = (array)json_decode( $data );
            }
            
            return !empty($datas['scriptName']) ? $datas['scriptName'] : '';
        }
        
        /**
         * 
         * @param type $pine_id
         */
        public function getListUsers($pine_id) {
            $payload = array(
                'pine_id' => $pine_id
            );

            $headers = array(
                'origin' => 'https://www.tradingview.com',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => 'sessionid='.$this->sessionid
            );
            $resp = wp_remote_post( $this->urls['list_users'].'?limit=30&order_by=-created', 
                array(
                    'body'    => $payload,
                    'headers' => $headers
                )
            );
            
            $datas = [];
            if ( wp_remote_retrieve_response_code( $resp ) == 200 ) {
                $data = wp_remote_retrieve_body($resp);
                $datas = (array)json_decode( $data );
            }
            
            /*
             * [id] => 44513150
             * [username] => donaldit
             * [userpic] => ''
             * [expiration] => 2022-11-30T23:59:59.999000+00:00
             * [created] => 2022-11-06T03:25:32.982881+00:00
             */
            return $datas;
        }
        
        public function validate_username($username) {
            $resp = wp_remote_get( $this->urls["username_hint"]."?s={$username}");
            if( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) != 200 && wp_remote_retrieve_response_code( $resp ) != 201) {
                return false;
            }
            
            $data = wp_remote_retrieve_body($resp);
            $usersList = (array)json_decode( $data );
            
            $validUser = false;
            foreach( $usersList as $user) {
                $item = (array)$user;
                if (strtolower($item['username']) == strtolower($username)) {
                    $validUser = true;
                    break;
                }
            }
            return $validUser;
        }
        
        public function get_access_details($username, $pine_id) {
            $payload = array(
                'pine_id' => $pine_id,
                'username' => $username,
            );

            $headers = array(
                'origin' => 'https://www.tradingview.com',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Cookie' => 'sessionid='.$this->sessionid
            );
            $resp = wp_remote_post( $this->urls['list_users'].'?limit=10&order_by=-created', 
                array(
                    'body'    => $payload,
                    'headers' => $headers
                )
            );
            
            $access_details = array(
                'hasAccess' => false,
                'currentExpiration' => '',
                'noExpiration' => false,
                'pine_id' => $pine_id,
                'username' => $username,
            );
            if( is_wp_error( $resp ) || wp_remote_retrieve_response_code( $resp ) != 200 && wp_remote_retrieve_response_code( $resp ) != 201) {
                return $access_details;
            }
            
            $data = wp_remote_retrieve_body($resp);
            $usersList = (array)json_decode( $data );
            
            $users = $usersList['results'];

            if (empty($users)) {
              return $access_details;
            }
            
            $hasAccess = false;
            $noExpiration = false;
            $expiration = '';
            foreach( $users as $user) {
                $item = (array)$user;
                if (strtolower($item['username']) == strtolower($username)) {
                    $hasAccess = true;
                    if (!empty($item["expiration"])) {
                        $expiration = $item['expiration'];
                    } else {
                        $noExpiration = true;
                    }
                    break;
                }
            }
            $access_details['hasAccess'] = $hasAccess;
            $access_details['noExpiration'] = $noExpiration;
            $access_details['currentExpiration'] = $expiration;
                    
            return $access_details;
        }
        
        public function add_access($access_details, $expiration) {
            
            //$noExpiration = $access_details['noExpiration'];
            $access_details['expiration'] = $access_details['currentExpiration'];
            $access_details['status'] = 'Not Applied';
            $payload = array(
                'pine_id' => $access_details['pine_id'],
                'username_recip' => $access_details['username']
            );
            if (!empty($expiration)) {
                $payload['expiration'] = $expiration;
            } else {
                $payload['noExpiration'] = true;
            }
                
            $enpoint_type = $access_details['hasAccess'] ? 'modify_access' : 'add_access';

            $headers= array(
                'origin' => 'https://www.tradingview.com',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'cookie' => 'sessionid='.$this->sessionid
            );
            
            $resp = wp_remote_post( $this->urls[$enpoint_type], 
                array(
                    'body'    => $payload,
                    'headers' => $headers
                )
            );
            
            if ( wp_remote_retrieve_response_code( $resp ) == 200 || wp_remote_retrieve_response_code( $resp ) == 201 ) {
                return true;
            }
              
            return false;
        }
    
        public function remove_access($access_details) {
            $payload = array(
              'pine_id' => $access_details['pine_id'],
              'username_recip' => $access_details['username']
            );
            
            $headers = array(
                'origin' => 'https://www.tradingview.com',
                'Content-Type' => 'application/x-www-form-urlencoded',
                'cookie' =>  'sessionid='.$this->sessionid
            );
            
            $resp = wp_remote_post( $this->urls['remove_access'], 
                array(
                    'body'    => $payload,
                    'headers' => $headers
                )
            );
            if ( wp_remote_retrieve_response_code( $resp ) == 200 || wp_remote_retrieve_response_code( $resp ) == 201 ) {
                return true;
            }
            
            return false;
        }
	}


}

/**
 * Unique access to instance of TWS_Tradingview class
 *
 * @return \TWS_Tradingview
 */
//function TWS_Tradingview() { //phpcs:ignore
//	return TWS_Tradingview::get_instance();
//}
