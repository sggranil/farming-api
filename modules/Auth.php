<?php
	
	require_once("./vendor/autoload.php");

	class Auth {
		protected $gm;
		protected $pdo;
        protected $get;

		public function __construct(\PDO $pdo) {
			$this->gm = new GlobalMethods($pdo);
            $this->get = new Get($pdo);
			$this->pdo = $pdo;
		}

		// JWT Methods

		protected function generate_header() {
			$header = [
				"typ"=>'PWA',
				"alg"=>'HS256',
				"ver"=>'1.0.0',
				"dev"=>'Simon Gerard Granil'
			];
			return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
		}

		protected function generate_payload($id, $email) {
			$payload = [   
				'uid'=>$id,
				'un'=>$email,
				'iby'=>'Farming App',
				'ie'=>'dev.simongranil@gmail.com',
				'idate'=>date_create(),
				'exp'=>time() + (10 * 10 * 12 * 0)
			];
			return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
		}

		protected function generate_token($id, $email) {
			$header = $this->generate_header();
			$payload = $this->generate_payload($id, $email);
			$hashSignature = hash_hmac('sha256', $header. "." .$payload, "www.farmingapp.com");
			$signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($hashSignature));

			return $header . "." . $payload . "." . $signature;
		}

		// User Authorization Methods

		public function encrypt_password($password) {
			$hashFormat = "$2y$10$";
		    $saltLength = 22;
		    $salt = $this->generate_salt($saltLength);
		    return crypt($password, $hashFormat.$salt);
		}

        protected function generate_salt($len) {
			$urs = md5(uniqid(mt_rand(), true));
            $b64String = base64_encode($urs);
            $mb64String = str_replace('+', '.', $b64String);
            return substr($mb64String, 0, $len);
		}

        protected function password_check($password, $existingHash) {
			$hash = crypt($password, $existingHash);
			if($hash === $existingHash){
				return true;
			}
			return false;
		}

		protected function get_payload($token) {
			$token = explode('.', $token);
			return $token[1];
		}

		protected function is_authorized($token) {
			$token = explode('.', $token);
			$payload = json_decode(base64_decode($token[1]));
			$exp = $payload->exp;
			$now = time();
			if($now < $exp) {
				return true;
			}
			return false;
		}

		protected function check_auth($token) {
			if($this->is_authorized($token)) {
				return $this->get_payload($token);
			}
			return false;
		}

		protected function getTokenSignature($d) {
			$token = explode('.', $d);
			return $token[2];
		}

		public function check_token_log($id, $token) {
			$sql = "SELECT * FROM token_tbl WHERE userid_fld = $id";
			$res = $this->gm->executeQuery($sql);

			switch($res['code']) {
				case 200:
					if (strlen($res['data'][0]['tokenlog_fld']) > 0) {
						return true;
					} else {
						$this->gm->update('token_tbl', ['tokenlog_fld'=>$token], "userid_fld = $id");
					}
				break;

				case 404:
					$payload = [
						"userid_fld" => $id,
						"tokenlog_fld" => $token,
					];
					
					$this->gm->insert("token_tbl", $payload);
				break;

				default:
					return false;
				break;
			}
		}

		public function checkValidSignature($param1, $param2) {
			$sql = "SELECT * FROM token_tbl WHERE userid_fld = ?";
			$prep = $this->pdo->prepare($sql);
			$prep->execute([
				$param1,
			]);

			if ($res = $prep->fetchAll()) {
				return $this->getTokenSignature($res[0]['tokenlog_fld']) == $param2;
			}
			return false;
		}
		
		// Login

		public function AddUser($dt) {
			$payload = [];	
			$code = 200;
			$remarks = "failed";
			$message = "Account creation failed.";
			$data = [
				'userfname_fld' => $dt->userfname_fld,
				'usermname_fld' => $dt->usermname_fld,
				'userlname_fld' => $dt->userlname_fld,
				'userpos_fld' => $dt->userpos_fld,
				'username_fld' => $dt->username_fld,
				'userpwd_fld' => $this->encrypt_password($dt->userpwd_fld),
			];

			$res = $this->gm->insert('user_tbl', $data);

            if($res['code'] == 200) {
                $code = 200;
                $remarks = "success";
                $message = "User added to database";
            }
            return $this->gm->response($payload, $remarks, $message, $code);
		}

		public function loginUser($dt) {
			$payload = [];
			$code = 200;
			$remarks = "failed";
			$message = "Login failed. Check you account credentials.";

			$sql = "SELECT * FROM user_tbl WHERE username_fld = '$dt->username_fld' LIMIT 1";
			$res = $this->gm->executeQuery($sql);

			if ($res['code'] == 200) {
				if ($this->password_check($dt->userpwd_fld, $res['data'][0]['userpwd_fld'])) {
					$id = $res['data'][0]['userid_fld'];
					$email = $res['data'][0]['username_fld'];
					$token = $this->generate_token($id, $email);

					// Checking current user's token

					if ($this->check_token_log($id, $token)) {
						// $code = 401;
						$remarks = "auth";
						$message = "Authorization failed. You already logged in with other device.";
					} else {
						$payload = $token;
						// $code = 200;
						$remarks = "success"; 
						$message = "Login success.";
					}
				}
			}
			return $this->gm->response($payload, $remarks, $message, $code);		
		}

		// Add

        // public function addStaff($dt) {
        //     $code = 0;
        //     $payload = null;
        //     $remarks = "failed";
        //     $message = "Unable to add data";
        //     $data = $dt;

        //     $res = $this->gm->insert('staff_tbl', $data);

        //     if($res['code'] == 200) {
        //         $code = 200;
        //         $remarks = "success";
        //         $message = "Staff added to database";
        //     }
        //     return $this->gm->response($payload, $remarks, $message, $code);
        // }
	}
?>