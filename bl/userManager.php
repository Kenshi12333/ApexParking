<?php
    require_once "../model/database.php";
    require_once "../model/userModel.php";
    class userManager{

        private $userModel;



        public function __construct()
        {
     

        $database = new Database();
        $db = $database -> connectDB();
        $this -> userModel = new UserModel($db);

    }

        public function updateUserFunc($firstName, $lastName, $userID, $roleID, $email, $password): void{
        try {
            if($this->userModel->updateUser($userID,$firstName,$lastName, $roleID, $email,$password)){
            echo "User has been updated";
            }else{
                echo "Error is encountered while updating value to the database";
            }
        }catch(PDOException $ex){
        http_response_code(501);
            echo $ex -> getMessage();
            exit;
        }

        }

        
    
   public function removeUserFunc($userID): void{
        try {
            if($this->userModel->deleteUser($userID)){
            echo "User has been deleted";
            }else{
                echo "Error is encountered while deleting value to the database";
            }
        }catch(PDOException $ex){
        http_response_code(501);
            echo $ex -> getMessage();
            exit;
        }
        }

         public  function addUserFunc($firstName, $lastName, $roleID, $email, $password)
    {
        try{

            if($this->userModel->createUser($firstName,$lastName, $roleID, $email, $password)){
                echo "New User has been added";
            }else {
                echo "Error is encountered while adding value to the database";
            }

        }catch(InvalidArgumentException $ex) {
            http_response_code(501);
            echo $ex -> getMessage();
            exit;
        }

    }


        public function getUser(){
        $response = $this->userModel->readUser();
        return $response->fetchAll(PDO::FETCH_ASSOC);

        }

        public function getAdvancedUser(){
            $response = $this->userModel->readAdvancedUser();
        return $response->fetchAll(PDO::FETCH_ASSOC);
        }


        public function loginFunc($userEmail, $userPassword){
            $result = $this->userModel->loginUser($userEmail);
            $user = $result->fetch(PDO::FETCH_ASSOC);
            if($user && password_verify($userPassword, $user["password"])){
            $_SESSION["loggedUser"] = $user;   
            echo $user['userRoleID'];

            }else {

                echo false;
            }
        }

    
      
    }
       






?>