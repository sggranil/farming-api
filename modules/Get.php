<?php 

    class Get {
        protected $gm;
        protected $pdo;

        public function __construct(\PDO $pdo) {
            $this->gm = new GlobalMethods($pdo);
            $this->pdo = $pdo;
        }

        public function getStudent($dt) {
			$payload = [];
			$code = 0;
			$remarks = "failed";
			$message = "Unable to retrieve data";

			$sql = "SELECT * FROM student_tbl";
			if ($dt->staffno_fld != null) {
				$sql.=" WHERE studno_fld = $dt->studno_fld";
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