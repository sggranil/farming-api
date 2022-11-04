<?php 
	
    require_once("./config/Config.php");
    require_once("./modules/Procedural.php");
    require_once("./modules/Global.php");
    require_once("./modules/Auth.php");
    require_once("./modules/Get.php");
    require_once("./modules/Post.php");

    $db = new Connection();
	$pdo = $db->connect();
    $gm = new GlobalMethods($pdo);
	$auth = new Auth($pdo);
    $get = new Get($pdo);
    $post = new Post($pdo);

    if (isset($_REQUEST['request'])) {
        $req = explode('/', rtrim($_REQUEST['request'], '/'));
    } else {
        $req = array("errorcatcher");
    }

    switch($_SERVER['REQUEST_METHOD']) {
        case 'POST':

            $d = json_decode(file_get_contents("php://input"));

            switch($req[0]) {

                case 'loginuser':
                    echo json_encode($auth->loginUser($d));
                break;

                case 'adduser':
                    echo json_encode($auth->addUser($d));
                break;

                case 'getuser':
                    echo json_encode($get->getUser($d));
                break;

                case 'geteq':
                    echo json_encode($get->getEquipment($d));
                break;

                case 'addeq':
                    echo json_encode($post->addEquipment($d));
                break;

                case 'deleteeq':
                    echo json_encode($post->deleteEquipment($d));
                break;
                
                case 'updateqty':
                    echo json_encode($post->updateQuantity($d));
                break;
            }
        break;

        case 'OPTIONS':
            return 200;
        break;

        default:
            echo errMsg(403);
        break;
    }

?>