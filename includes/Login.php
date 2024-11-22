<?php
//error_reporting(0);
//require '../includes/DbConnect.php';

class User
{
public function auth($user, $pass){
    $sql = "SELECT 
    USERNAME, PASSWORD, SCOPE  ,USER_STATUS     
    FROM LANECOVE.WEBSERVICE_LOGIN WHERE USERNAME = :username ";

    try {
        $count =0;
        $db = new DbConnect();
        $con = $db->connect();
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':username', $user);

        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                $password = $row["PASSWORD"];
                $scope = $row["SCOPE"];
                $status = $row["USER_STATUS"];
                $count++;
            }
            $stmt->closeCursor();
            $con = null;

        if($count > 0){
            if(strcmp($pass,$password)== 0 ){
                if(strcmp($status,"1")== 0){
                    $temp = explode(",",$scope);

                    $response_data['ERROR'] = false;
                    $response_data['MESSAGE'] = "";
                    $response_data['DATA'] = $temp;

                }else{
                $response_data['ERROR'] = true;
                $response_data['DATA'] = "";
                $response_data['MESSAGE'] = "Inactive User";

            }

            }else{
                $response_data['ERROR'] = true;
                $response_data['DATA'] = "";
                $response_data['MESSAGE'] = "Invalid Password";

            }
        }else{
            $response_data['ERROR'] = true;
            $response_data['DATA'] = "";
            $response_data['MESSAGE'] = "Invalid Username";
        }

            
        } else {
            $err =  oci_error($stmt);
            $response_data['ERROR'] = true;
            $response_data['DATA'] = "";
            $response_data['MESSAGE'] = $err['MESSAGE'];
        }

    } catch (PDOException $err) {
        $response_data['ERROR'] = true;
        $response_data['DATA'] = "";
        $response_data['MESSAGE'] = $err->getMessage();
    }

    return $response_data;
}

}