<?php

class rolesModel{

private $connect;

public function __construct($db)
{
   $this->connect = $db;
}



    public function readRoles(){
        $selectQuery = "SELECT * FROM tbl_systemroles";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();

       return $response;

    }


    public function countUsersPerRole() {
    $selectQuery = "SELECT r.roleName, COUNT(u.userID) AS total_users 
                    FROM tbl_systemroles r 
                    LEFT JOIN tbl_users u ON u.userRoleID = r.userRoleID 
                    GROUP BY r.roleName";
    $response = $this->connect->prepare($selectQuery);
    $response->execute();

    return $response;
}
   
}