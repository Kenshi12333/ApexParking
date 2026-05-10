<?php

class UserModel{

private $connect;

public function __construct($db)
{
   $this->connect = $db;
}

public function createUser($fName, $lName, $userRoleID, $email, $password){
    $insertQuery = "INSERT INTO tbl_users (firstName, lastName, userRoleID, email, password, createdAt, updatedAt) VALUES(:firstName, :lastName, :userRoleID, :email, :password, :createdAt, :updatedAt)";

    $dateNow = date('Y-m-d H:i:s');

    $hashPassword = password_hash($password, PASSWORD_BCRYPT);

    $response = $this->connect->prepare($insertQuery);
    $response->bindParam(":firstName",$fName);
    $response->bindParam(":lastName", $lName);

    $response->bindParam(":userRoleID", $userRoleID);
   
    $response->bindParam(":email", $email);
    $response->bindParam(":password", $hashPassword);

    $response->bindParam(":createdAt", $dateNow);
    $response->bindParam(":updatedAt", $dateNow);

    return $response->execute();
     

}

    public function readUser(){
        $selectQuery = "SELECT * FROM tbl_users";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();

       return $response;

    }

    public function updateUser($uID, $fName, $lName, $roleID, $email, $password){
    $updateQuery = "UPDATE tbl_users SET firstName =:firstName, lastName =:lastName, updatedAt =:updatedAt , userRoleID =:userRoleID, email =:email, password =:password  WHERE userID =:userID";
    $response = $this->connect->prepare($updateQuery);

    $dateNow = date('Y-m-d H:i:s');

    $hashPassword = password_hash($password, PASSWORD_BCRYPT);

    $response->bindParam(":firstName", $fName);
    $response->bindParam(":lastName", $lName);
    $response->bindParam(":userRoleID", $roleID);

    $response->bindParam(":email", $email);
    $response->bindParam(":password", $hashPassword);

     $response->bindParam(":updatedAt", $dateNow);

    $response->bindParam(":userID", $uID);

    $response->execute();
    return $response;
    
    }


    public function deleteUser($uID){
        $deleteQuery = "DELETE FROM tbl_users WHERE userID =:userID";
        $response = $this->connect->prepare($deleteQuery);

        $response->bindParam(":userID", $uID);
        $response->execute();
        return $response;
    }

     public function readAdvancedUser(){
        $selectQuery = "SELECT * FROM tbl_users INNER JOIN tbl_systemroles ON tbl_users.userRoleID = tbl_systemroles.userRoleID";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();

       return $response;

    }

    public function loginUser($email){
        $selectQuery ="SELECT * FROM tbl_users WHERE email = :email";
        $response = $this->connect->prepare($selectQuery);
        $response->bindParam(":email",$email);
        $response->execute();

        return $response;
    }

}



?>