<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		
			extract($_POST);		
			$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
			if($qry->num_rows > 0){
				foreach ($qry->fetch_array() as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				if($_SESSION['login_type'] != 1){
					return 2 ;
				}
					return 1;
			}else{
				return 3;
			}
	}
	function login2(){
		
			extract($_POST);
			if(isset($email))
				$username = $email;
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' and password = '".md5($password)."' ");
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				if($key != 'passwors' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if($_SESSION['login_alumnus_id'] > 0){
				$bio = $this->db->query("SELECT * FROM alumnus_bio where id = ".$_SESSION['login_alumnus_id']);
				if($bio->num_rows > 0){
					foreach ($bio->fetch_array() as $key => $value) {
						if($key != 'passwors' && !is_numeric($key))
							$_SESSION['bio'][$key] = $value;
					}
				}
			}
			if($_SESSION['bio']['status'] != 1){
					foreach ($_SESSION as $key => $value) {
						unset($_SESSION[$key]);
					}
					return 2 ;
					exit;
				}
				return 1;
		}else{
			return 3;
		}
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}
	function logout2(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:../index.php");
	}

	function save_user(){
		extract($_POST);
		if (isset($name) && isset($username) && isset($email) && isset($phone)) {
			$data = " name = '$name' ";
			$data .= ", username = '$username' ";
			$data .= ", email = '$email' ";
			$data .= ", phone = '$phone' ";
			if(!empty($password))
				$data .= ", password = '".md5($password)."' ";
			if(isset($type))
				$data .= ", type = '$type' ";
			// if($type == 1)
			// 	$establishment_id = 0;
			// $data .= ", establishment_id = '$establishment_id' ";
			$chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
			if($chk > 0){
				return 2;
			}
			if(empty($id)){
				$save = $this->db->query("INSERT INTO users set ".$data);
			}else{
				$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
			}
			if($save){
				return 1;
			}
		}
	}
	function delete_user(){
		try {
			extract($_POST);
			$delete = $this->db->query("DELETE FROM users where id = ".$id);
			if($delete)
				return 1;
		} catch (Exception $e) {
			return 2;
		}
		
	}
	function signup(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("INSERT INTO users set ".$data);
		if($save){
			$uid = $this->db->insert_id;
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
			if($data){
				$aid = $this->db->insert_id;
				$this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}
	function update_account(){
		extract($_POST);
		$data = " name = '".$firstname.' '.$lastname."' ";
		$data .= ", username = '$email' ";
		if(!empty($password))
		$data .= ", password = '".md5($password)."' ";
		$chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
		if($chk > 0){
			return 2;
			exit;
		}
			$save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
		if($save){
			$data = '';
			foreach($_POST as $k => $v){
				if($k =='password')
					continue;
				if(empty($data) && !is_numeric($k) )
					$data = " $k = '$v' ";
				else
					$data .= ", $k = '$v' ";
			}
			if($_FILES['img']['tmp_name'] != ''){
							$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
							$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
							$data .= ", avatar = '$fname' ";

			}
			$save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
			if($data){
				foreach ($_SESSION as $key => $value) {
					unset($_SESSION[$key]);
				}
				$login = $this->login2();
				if($login)
				return 1;
			}
		}
	}

	function save_settings(){
		extract($_POST);
		$data = " name = '".str_replace("'","&#x2019;",$name)."' ";
		$data .= ", email = '$email' ";
		$data .= ", contact = '$contact' ";
		$data .= ", about_content = '".htmlentities(str_replace("'","&#x2019;",$about))."' ";
		if($_FILES['img']['tmp_name'] != ''){
						$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
						$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
					$data .= ", cover_img = '$fname' ";

		}
		
		// echo "INSERT INTO system_settings set ".$data;
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set ".$data);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set ".$data);
		}
		if($save){
		$query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
		foreach ($query as $key => $value) {
			if(!is_numeric($key))
				$_SESSION['system'][$key] = $value;
		}

			return 1;
				}
	}

	
	function save_category(){
		extract($_POST);
		$data = " name = '$name' ";
		$chk = $this->db->query("SELECT * FROM categories where name = '$name' ")->num_rows;
		if($chk > 0)
		return 2;
			if(empty($id)){
				$save = $this->db->query("INSERT INTO categories set $data");
			}else{
				$save = $this->db->query("UPDATE categories set $data where id = $id");
			}
		if($save)
			return 1;
	}
	function delete_category(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM categories where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_house(){
		extract($_POST);
		$data = " house_no = '$house_no' ";
		$data .= ", description = '$description' ";
		$data .= ", category_id = '$category_id' ";
		$data .= ", electricity_number = '$electricity_number' ";
		$data .= ", water_meter = '$water_meter' ";
		$data .= ", service_price = '$service_price' ";
		$data .= ", wifi = '$wifi' ";
		$data .= ", price = '$price' ";
		$data .= ", belongings = '$belongings' ";
		$chk = $this->db->query("SELECT * FROM houses where house_no = '$house_no' ")->num_rows;
		if($chk > 0 ){
			$save = $this->db->query("UPDATE houses set $data where id = $id");
		}else {
			$save = $this->db->query("INSERT INTO houses set $data");
		}
		if($save)
			return 1;
	}
	function delete_house(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM houses where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function save_tenant(){
		extract($_POST);
		$data = " user_id = '$user_id' ";
		$data .= ", house_id = '$house_id' ";
		$data .= ", date_in = '$date_in' ";
			if(empty($id)){
				$save = $this->db->query("INSERT INTO tenants set $data");
				$save2 = $this->db->query("UPDATE houses set house_status = 1  where id = $house_id ");
			}else{
				$save = $this->db->query("UPDATE tenants set $data where id = $id");
				$save2 = $this->db->query("UPDATE houses set house_status = 1 where id = $house_id ");
			}
		if($save)
			return 1;
	}
	function delete_tenant(){
		extract($_POST);
		$delete = $this->db->query("UPDATE tenants set status = 0 where id = ".$id);
		if($delete){
			$this->db->query("UPDATE houses set house_status = 0 where id = $house_id ");
			return 1;
		}
	}
	function get_tdetails(){
		extract($_POST);
		$data = array();
		$tenants =$this->db->query("SELECT t.*,u.name as name,h.house_no,h.price FROM tenants t inner join houses h on h.id = t.house_id inner join users u on u.id = t.user_id where t.id = {$id} ");
		foreach($tenants->fetch_array() as $k => $v){
			if(!is_numeric($k)){
				$$k = $v;
			}
		}
		$months = abs(strtotime(date('Y-m-d')." 23:59:59") - strtotime($date_in." 23:59:59"));
		$months = floor(($months) / (30*60*60*24));
		$data['months'] = $months;
		$payable= abs($price * $months);
		$data['payable'] = number_format($payable,2);
		$paid = $this->db->query("SELECT SUM(amount) as paid FROM payments where id != '$pid' and tenant_id =".$id);
		$last_payment = $this->db->query("SELECT * FROM payments where id != '$pid' and tenant_id =".$id." order by unix_timestamp(date_created) desc limit 1");
		$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
		$data['paid'] = number_format($paid,2);
		$data['last_payment'] = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['date_created'])) : 'N/A';
		$data['outstanding'] = number_format($payable - $paid,2);
		$data['price'] = number_format($price,2);
		$data['name'] = ucwords($name);
		$data['rent_started'] = date('M d, Y',strtotime($date_in));

		return json_encode($data);
	}
	
	function save_payment(){
		extract($_POST);
		$POST = $_POST;
		$tenants =$this->db->query("SELECT t.*,u.name as name,h.* FROM tenants t inner join houses h on h.id = t.house_id inner join users u on u.id = t.user_id where t.id = {$tenant_id} ")->fetch_array();
		$house_electricity_number = $tenants['electricity_number'];
		$house_water_meter = $tenants['water_meter'];
		$electricity_price = $electricity_number * $house_electricity_number;
		$water_price = $house_water_meter * $water_number;
		$total_amount = $tenants['price'] + $electricity_price + $water_price;
		$POST['electricity_price'] =  $electricity_price;
		$POST['water_price'] =  $water_price;
		$POST['electricity_number'] =  $electricity_number;
		$POST['water_number'] =  $water_number;
		$POST['total_amount'] =  $total_amount;
		$POST['amount'] =  $tenants['price'];
		$POST['status'] =  0;
		$data = "";
		foreach($POST as $k => $v){
			if(!in_array($k, array('id','ref_code')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO payments set $data");
			$id=$this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE payments set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}
	function delete_payment(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM payments where id = ".$id);
		if($delete){
			return 1;
		}
	}
	function calculate_payment(){
		extract($_POST);
		$tenants =$this->db->query("SELECT * FROM tenants t inner join houses h on h.id = t.house_id where t.id = {$id} ")->fetch_array();
		$lastElectricNumberData = $this->db->query("SELECT electricity_number FROM payments where tenant_id = {$id} order by unix_timestamp(date_created) desc limit 1 ");
		$lastElectricNumber = 0;
		if ($lastElectricNumberData->num_rows > 0) {
			$lastElectricNumber = $lastElectricNumberData->fetch_array()['electricity_number'];
		}
		if(empty($tenants)) { 
			return 2;
		}
		$electricity_price = $tenants['electricity_number'];
		$price = $tenants['price'];
		$house_electricity_number = $tenants['electricity_number'];
		$house_water_meter = $tenants['water_meter'];
		$electricity_price = ($electricity_number - $lastElectricNumber) * $house_electricity_number;
		$water_price = $house_water_meter * $water_number;
		$total_amount = $tenants['price'] + $electricity_price + $water_price;
		$POST['electricity_price'] =  $electricity_price;
		$POST['water_price'] =  $water_price;
		$POST['electricity_number'] =  $electricity_number;
		$POST['water_number'] =  $water_number;
		$POST['total_amount'] =  $total_amount;
		$POST['tenant_id'] =  $id;
		$POST['amount'] =  $price;
		$POST['status'] =  0;
		$data = "";
		foreach($POST as $k => $v){
			if(!in_array($k, array('id','ref_code')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($payment_id)){
			$save = $this->db->query("INSERT INTO payments set $data");
			$payment_id = $this->db->insert_id;
		}else{
			$save = $this->db->query("UPDATE payments set $data where id = $payment_id");
		}

		if($save){
			return 1;
		}
	}

	function update_payment(){
		extract($_POST);
		$payment =$this->db->query("SELECT * FROM payments where id = {$id} ")->fetch_array();
		print($payment['status']);
		if ($payment['status'] == 1) {
			return 1;
		}
		$update = $this->db->query("UPDATE payments set status=1 where id = $id");
		if($update){
			return 1;
		}
	}
}