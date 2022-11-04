<?php 

    class Get {
        protected $gm;
        protected $pdo;

        public function __construct(\PDO $pdo) {
            $this->gm = new GlobalMethods($pdo);
            $this->pdo = $pdo;
        }

        public function getUser($dt) {
			$payload = [];
			$code = 0;
			$remarks = "failed";
			$message = "Unable to retrieve data";

			$sql = "SELECT * FROM user_tbl";
			if ($dt->userid_fld != null) {
				$sql.=" WHERE userid_fld = $dt->userid_fld";
			}
			
			$res = $this->gm->executeQuery($sql);

			if ($res['code'] == 200) {
				$payload = $res['data'];
				$code = 200;
				$remarks = "success";
				$message = "Retrieving data...";
			}
			return $this->gm->response($payload, $remarks, $message, $code);
		}

		public function getEquipment($dt) {
			$payload = [];
			$code = 0;
			$remarks = "failed";
			$message = "Unable to retrieve data";

			$sql = "SELECT * FROM equipment_tbl";
			if ($dt->eid_fld != null) {
				$sql.=" WHERE eid_fld = $dt->eid_fld";
			}
			
			$res = $this->gm->executeQuery($sql);
			
			if ($res['code'] == 200) {
				$payload = $res['data'];
				$code = 200;
				$remarks = "success";
				$message = "Retrieving data...";
			}
			return $this->gm->response($payload, $remarks, $message, $code);
		}
    }
?>