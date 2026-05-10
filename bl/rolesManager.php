<?php
    require_once "../model/database.php";
    require_once "../model/rolesModel.php";
    class rolesManager{

        private $rolesModel;



        public function __construct()
        {
     

        $database = new Database();
        $db = $database -> connectDB();
        $this -> rolesModel = new rolesModel($db);

    }

        public function getRoles(){
        $response = $this->rolesModel->readRoles();
        return $response->fetchAll(PDO::FETCH_ASSOC);

        }

public function getRoleCounts(){
    $response = $this->rolesModel->countUsersPerRole();
    return $response->fetchAll(PDO::FETCH_ASSOC);
}

    }
?>