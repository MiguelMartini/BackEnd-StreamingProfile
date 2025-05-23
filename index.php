<?php
//rotas
    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");

    require_once 'DbConnect.php';
    $objDb = new DbConnect;
    $conn = $objDb->connect();
    
    $user = file_get_contents('php://input');
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method){
        case "GET":
            $sql = "SELECT * FROM profiles";
            $path = explode('/',$_SERVER['REQUEST_URI']);
            
            if(isset($path[3]) && is_numeric($path[3])){
                $sql .=" WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt -> bindParam(':id', $path[3]);
                $stmt ->execute();
                $profiles = $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                $stmt = $conn->prepare($sql);
                $stmt ->execute();
                $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            echo json_encode($profiles);        
            break;

        case "POST":
            $profiles = json_decode(file_get_contents('php://input') );
            $sql = "INSERT INTO profiles(id, name, created_at) VALUES (null, :name, :created_at)";
            $created_at = date('Y-m-d');
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $profiles->name);
            $stmt->bindParam(':created_at', $created_at); 
            if($stmt ->execute()){
                $response = ['satus' => 1 , 'message' => "Profile created"];
            }else{
                $response = ['satus' => 0 , 'message' => "Profile not created"];
            }
            break;

            case "PUT":
                $profiles = json_decode (file_get_contents('php://input'));
                $sql = "UPDATE profiles SET name= :name, updated_at =:updated_at WHERE id= :id";

                $stmt = $conn->prepare($sql); // prepara a query para evitar um SQL injection
                $updated_at = date('Y-m-d');

                $stmt -> bindParam(':id', $profiles->id);
                $stmt -> bindParam(':name', $profiles->name);
                $stmt -> bindParam(':updated_at', $updated_at);
    
                //Execução da query e resposta
                if($stmt->execute()){
                    $response = ['status' => 1, 'message' => 'Record updated successfully.'];
                }else{
                    $response = ['status' => 0, 'message' => 'Record not updated Created'];
                }
                echo json_encode($response);
                break;
    }
?>