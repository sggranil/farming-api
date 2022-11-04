<?php 
    class Post {
        protected $gm;
        protected $pdo;
        protected $get;
        protected $auth;

        public function __construct(\PDO $pdo) {
            $this->pdo = $pdo;
            $this->gm = new GlobalMethods($pdo);
            $this->get = new Get($pdo);
            $this->auth = new Auth($pdo);
        }

        public function AddEquipment($dt) {
            $payload = [];	
			$code = 200;
			$remarks = "failed";
			$message = "Account creation failed.";

			$res = $this->gm->insert('equipment_tbl', $dt);

            if($res['code'] == 200) {
                $code = 200;
                $remarks = "success";
                $message = "User added to database";
            }
            return $this->gm->response($payload, $remarks, $message, $code);
        }

        public function deleteEquipment($dt) {
            $payload = [];	
			$code = 200;
			$remarks = "failed";
			$message = "Equipment deletion failed.";

			$res = $this->gm->delete('equipment_tbl', "eid_fld = $dt->eid_fld");

            if($res['code'] == 200) {
                $code = 200;
                $remarks = "success";
                $message = "Equipment deleted";
            }
            return $this->gm->response($payload, $remarks, $message, $code);
        }

        public function updateQuantity($dt) {
            $payload = [];	
			$code = 200;
			$remarks = "failed";
			$message = "Update failed.";

			$res = $this->gm->update("equipment_tbl", ["eqty_fld"=>$dt->eqty_fld], "eid_fld = $dt->eid_fld");

            if($res['code'] == 200) {
                $code = 200;
                $remarks = "success";
                $message = "Quantity updated successfully";
            }
            return $this->gm->response($payload, $remarks, $message, $code);
        }
    }
?>