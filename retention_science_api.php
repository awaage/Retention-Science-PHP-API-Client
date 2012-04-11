<?php
/*
Retention Science PHP API Client

Copyright (c) 2012 Retention Science

Released under the GNU General Public License
*/
class RetentionScienceApi {
	const API_TEST_URL = 'http://api.retentionsandbox.com';
	const API_URL = 'http://api.retentionscience.com';
	const API_PORT = 80;
	const API_VERSION = '1';

	private $password;
	private $time_out = 60;
	private $user_agent;
	private $username;
	private $testmode;

	// class methods
	public function __construct($username = null, $password = null, $testmode=false) {
		if($username !== null) $this->set_username($username);
		if($password !== null) $this->set_password($password);
		if($testmode) $this->set_testmode($testmode);
	}

	private function perform_call($url, $params = array(), $authenticate = false, $use_post = true) {
		// redefine
		$url = (string) $url;
		$aParameters = (array) $params;
		$authenticate = (bool) $authenticate;
		$use_post = (bool) $use_post;

		// build url
		if ($this->get_testmode()){
			$url = self::API_TEST_URL .'/' . $url;
		} else {
			$url = self::API_URL .'/' . $url;
		}
		
		// validate needed authentication
		if($authenticate && ($this->get_username() == '' || $this->get_password() == '')) {
			throw new RetentionScienceException('No username or password was set.');
		}

		// build GET URL if not using post
		if(!empty($params) && !$use_post){
			$url .= '?'. http_build_query( $params );
		}

		// set options
		$options[CURLOPT_URL] = $url;
		$options[CURLOPT_PORT] = self::API_PORT;
		$options[CURLOPT_USERAGENT] = $this->get_useragent();
		// follow on only if allowed - 20120221
		if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
			$options[CURLOPT_FOLLOWLOCATION] = true;
		}       
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = (int) $this->get_time_out();

		// HTTP basic auth
		if($authenticate) {
			$options[CURLOPT_HTTPAUTH] = CURLAUTH_BASIC;
			$options[CURLOPT_USERPWD] = $this->get_username() .':'. $this->get_password();
		}

		// build post params if $use_post
		if(!empty($params) && $use_post) {
			$options[CURLOPT_POST] = true;
			$options[CURLOPT_POSTFIELDS] = http_build_query( $params );
		}

		// curl init
		$curl = curl_init();
		// set options
		curl_setopt_array($curl, $options);
		// execute
		$response = curl_exec($curl);
		$headers = curl_getinfo($curl);
		// fetch errors and status code
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);
		$http_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if ($errorNumber != 0) {
			$response = 'cURL ERROR: [' . $errorNumber . "] " . $errorMessage;
		}
		// close
		curl_close($curl);
		return array('response_code' => $http_status_code,
			'response' => $response);
	}
	
	private function handle_response($response){
		// decode the returned json
		if ($response['response_code'] == 200 || $response['response_code'] == 201 ){
			return $response['response'];
		} else {
			throw new RetentionScienceException($response['response_code'] . ' - ' . $response['response']);
		}
	}


	// Getters
	private function get_password(){
		return (string) $this->password;
	}
	public function get_time_out(){
		return (int) $this->time_out;
	}
	public function get_useragent(){
		return (string) 'Retention Science PHP API Client/'. self::API_VERSION .' '. $this->user_agent;
	}
	private function get_username(){
		return (string) $this->username;
	}	
	private function get_testmode(){
		return (boolean) $this->testmode;
	}

	// Setters
	private function set_username($username){
		$this->username = (string) $username;
	}
	private function set_password($password){
		$this->password = (string) $password;
	}
	public function set_time_out($seconds){
		$this->time_out = (int) $seconds;
	}
	public function set_user_agent($user_agent){
		$this->user_agent = (string) $user_agent;
	}
	public function set_testmode($testmode){
		$this->testmode = (boolean) $testmode;
	}

	/* Users resource */
	public function get_last_user_record_id() {
		$url = 'users/last_record_id';
		$response = $this->perform_call($url, array(), true, false);
		return $this->handle_response($response);
	}
	
	// user_array is an assoc. array:
	// email, full_name, address1, address2, city, state
	// zip, country, phone, birth_year, gender, ip_address, 
	// number_logons, account_created_on, last_logon_at
	public function update_user($record_id, $user_array) {
		$url = 'users/update/' . urlencode($record_id);
		$response = $this->perform_call($url, array('user' => json_encode($user_array)), true, true);
		return $this->handle_response($response);
	}
	
	/* Orders resource */
	public function get_last_order_record_id() {
		$url = 'orders/last_record_id';
		$response = $this->perform_call($url, array(), true, false);
		return $this->handle_response($response);
	}
	// order_array is an assoc. array:
	// user_record_id, total_price, discount_amount, ordered_at, payment_method, order_items
	public function update_order($record_id, $order_array) {
		$url = 'orders/update/' . urlencode($record_id);
		$response = $this->perform_call($url, array('order' => json_encode($order_array)), true, true);
		return $this->handle_response($response);
	}

	/* Items resource */
	// item_array is an assoc. array:
	// name, manufacturer, model, quantity, price,
  // active, image_list, categories
	public function update_item($record_id, $item_array) {
		$url = 'items/update/' . urlencode($record_id);
		$response = $this->perform_call($url, array('item' => json_encode($item_array)), true, true);
		return $this->handle_response($response);
	}
	
	/* Categories resource */
	// category_array is an assoc. array: 
	// name, description, parent_record_id
	public function update_category($record_id, $category_array) {
		$url = 'categories/update/' . urlencode($record_id);
		$response = $this->perform_call($url, array('category' => json_encode($category_array)), true, true);
		return $this->handle_response($response);
	}
	
}


class RetentionScienceException extends Exception { }

?>
