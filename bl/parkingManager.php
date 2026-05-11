<?php
    require_once "../model/database.php";
    require_once "../model/parkingModel.php";
    
    class parkingManager{

        private $parkingModel;

        public function __construct()
        {
            $database = new Database();
            $db = $database -> connectDB();
            $this -> parkingModel = new parkingModel($db);
        }

        public function getVehicleTypes(){
            $response = $this->parkingModel->getVehicleTypes();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getParkingSlots(){
            $response = $this->parkingModel->getParkingSlots();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getActiveVehicles(){
            $response = $this->parkingModel->getActiveVehicles();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getDiscounts(){
            $response = $this->parkingModel->getDiscounts();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getPenalties(){
            $response = $this->parkingModel->getPenalties();
            return $response->fetchAll(PDO::FETCH_ASSOC);
        }

        public function checkInVehicleFunc($plateNumber, $vehicleTypeID, $slotID){
            try{
                if($plateNumber == "" || $vehicleTypeID == "" || $slotID == ""){
                    echo "Missing required fields";
                    return;
                }

                if($this->parkingModel->checkInVehicle($plateNumber, $vehicleTypeID, $slotID)){
                    echo "Vehicle has been added";
                }else{
                    echo "Error encountered while adding vehicle";
                }

            }catch(PDOException $ex){
                http_response_code(501);
                echo $ex -> getMessage();
                exit;
            }
        }

        public function checkOutVehicleFunc($transactionID, $totalAmount, $discountID, $penaltyID){
            try{
                if($transactionID == "" || $totalAmount == ""){
                    echo "Missing required fields";
                    return;
                }

                if($this->parkingModel->checkOutVehicle($transactionID, $totalAmount, $discountID, $penaltyID)){
                    echo "Vehicle has been checked out";
                }else{
                    echo "Error encountered while checking out vehicle";
                }

            }catch(PDOException $ex){
                http_response_code(501);
                echo $ex -> getMessage();
                exit;
            }
        }

        public function getTransactionDetails($transactionID){
            try{
                $response = $this->parkingModel->getTransactionDetails($transactionID);
                return $response->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $ex){
                echo "Error fetching transaction: " . $ex->getMessage();
                return null;
            }
        }

      
        public function getSlotStats(){
            try{
                $response = $this->parkingModel->getSlotStats();
                return $response->fetch(PDO::FETCH_ASSOC);
            }catch(PDOException $ex){
                return ['total_slots' => 0, 'available_slots' => 0, 'occupied_slots' => 0];
            }
        }

    
        public function getTodayEarnings(){
            try{
                $response = $this->parkingModel->getTodayEarnings();
                $result = $response->fetch(PDO::FETCH_ASSOC);
                return $result['total_revenue'] ? $result['total_revenue'] : 0.00;
            }catch(PDOException $ex){
                return 0.00;
            }
        }


        public function getRecentActivity(){
            try{
                $response = $this->parkingModel->getRecentActivity();
                return $response->fetchAll(PDO::FETCH_ASSOC);
            }catch(PDOException $ex){
                return [];
            }
        }


      public function getBillByPlateFunc($plateNumber) {
            try {
                $response = $this->parkingModel->getTransactionByPlate($plateNumber);
                $data = $response->fetch(PDO::FETCH_ASSOC);

                if ($data) {
                    $gracePeriod = 15; 
                    $isFree = $data['minutesParked'] <= $gracePeriod;
                    $totalDue = $isFree ? 0 : (float)$data['vehicleTypeRate'];
                    $timeIn = date("h:i A", strtotime($data['time_IN']));

                  
                    echo "success|" . $data['transaction_ID'] . "|" . $data['plateNumber'] . "|" . $data['vehicleTypeName'] . "|" . $timeIn . "|" . $totalDue;
                } else {
                    echo "error|Not found";
                }
            } catch (PDOException $ex) {
                echo "error|" . $ex->getMessage();
            }
        }

        public function processKioskPaymentFunc($transactionID, $totalAmount) {
            try {
                if ($transactionID == "" || $totalAmount == "") {
                    return false;
                }

              
                return $this->parkingModel->updateKioskPayment($transactionID, $totalAmount);
                
            } catch (PDOException $ex) {
              
                return false;
            }
        }



        public function getParkedVehicleStats(){
        $response = $this->parkingModel->getParkedVehicleStats();
        return $response->fetchAll(PDO::FETCH_ASSOC);
    }

    
    }
?>