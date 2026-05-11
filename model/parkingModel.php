<?php

class parkingModel{

    private $connect;

    public function __construct($db)
    {
        $this->connect = $db;
    }

    public function getVehicleTypes(){
        $selectQuery = "SELECT * FROM tbl_vehicletypes";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getParkingSlots(){
        $selectQuery = "
            SELECT p.*, s.status_name as slotStatus 
            FROM tbl_parkingslots p
            LEFT JOIN tbl_slotsstatus s ON p.status_id = s.status_id 
            ORDER BY p.levelNumber, p.slotName
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getActiveVehicles(){
        $selectQuery = "
            SELECT 
                t.transaction_ID,
                t.plateNumber,
                t.time_IN,
                t.parkingSlot,
                t.paymentStatusID,
                t.total_Amount,
                v.vehicleTypeName,
                v.vehicleTypeRate,
                TIMESTAMPDIFF(MINUTE, t.time_IN, NOW()) as minutesParked
            FROM tbl_transactions t
            INNER JOIN tbl_vehicletypes v ON t.vehicleTypeID = v.vehicleTypeID
            WHERE t.paymentStatusID IN (1, 3) 
            ORDER BY t.transaction_ID DESC
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getDiscounts(){
        $selectQuery = "SELECT * FROM tbl_discounts WHERE isActive = 'yes'";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getPenalties(){
        $selectQuery = "SELECT * FROM tbl_penalties WHERE isActive = 'yes'";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function checkInVehicle($plateNumber, $vehicleTypeID, $slotID){
        $insertQuery = "
            INSERT INTO tbl_transactions(plateNumber, vehicleTypeID, parkingSlot, paymentStatusID, time_IN, createdAt, updatedAt)
            VALUES(:plateNumber, :vehicleTypeID, :parkingSlot, 1, NOW(), NOW(), NOW())
        ";
        
        $response = $this->connect->prepare($insertQuery);
        $response->bindParam(":plateNumber", $plateNumber);
        $response->bindParam(":vehicleTypeID", $vehicleTypeID);
        $response->bindParam(":parkingSlot", $slotID);
        
        if ($response->execute()) {
            $updateSlot = "UPDATE tbl_parkingslots SET status_id = 2 WHERE slotName = :slotID";
            $slotStmt = $this->connect->prepare($updateSlot);
            $slotStmt->bindParam(":slotID", $slotID);
            $slotStmt->execute();
            return true;
        }
        return false;
    }

    public function checkOutVehicle($transactionID, $totalAmount, $discountID, $penaltyID){
        $getSlotQuery = "SELECT parkingSlot FROM tbl_transactions WHERE transaction_ID = :transactionID";
        $getSlotStmt = $this->connect->prepare($getSlotQuery);
        $getSlotStmt->bindParam(":transactionID", $transactionID);
        $getSlotStmt->execute();
        $row = $getSlotStmt->fetch(PDO::FETCH_ASSOC);
        $slotName = $row['parkingSlot'];

        $updateQuery = "
            UPDATE tbl_transactions 
            SET 
                time_Out = NOW(),
                paymentStatusID = 2,
                total_Amount = :totalAmount,
                discountID = :discountID,
                penaltyID = :penaltyID,
                updatedAt = NOW()
            WHERE transaction_ID = :transactionID
        ";
        
        $response = $this->connect->prepare($updateQuery);
        $response->bindParam(":transactionID", $transactionID);
        $response->bindParam(":totalAmount", $totalAmount);
        $response->bindParam(":discountID", $discountID);
        $response->bindParam(":penaltyID", $penaltyID);
        
        if ($response->execute()) {
            $updateSlot = "UPDATE tbl_parkingslots SET status_id = 1 WHERE slotName = :slotName";
            $slotStmt = $this->connect->prepare($updateSlot);
            $slotStmt->bindParam(":slotName", $slotName);
            $slotStmt->execute();
            return true;
        }
        return false;
    }

    public function getTransactionDetails($transactionID){
        $selectQuery = "
            SELECT t.transaction_ID, t.plateNumber, t.time_IN, t.parkingSlot, v.vehicleTypeName, v.vehicleTypeRate
            FROM tbl_transactions t
            INNER JOIN tbl_vehicletypes v ON t.vehicleTypeID = v.vehicleTypeID
            WHERE t.transaction_ID = :transactionID
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->bindParam(":transactionID", $transactionID);
        $response->execute();
        return $response;
    }

    public function getSlotStats() {
        $selectQuery = "
            SELECT 
                COUNT(slotName) as total_slots,
                SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) as available_slots,
                SUM(CASE WHEN status_id = 2 THEN 1 ELSE 0 END) as occupied_slots
            FROM tbl_parkingslots
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getTodayEarnings() {
        $selectQuery = "
            SELECT SUM(total_Amount) as total_revenue
            FROM tbl_transactions
            WHERE paymentStatusID = 2 AND DATE(updatedAt) = CURDATE()
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getRecentActivity() {
        $selectQuery = "
            SELECT t.plateNumber, t.parkingSlot, t.paymentStatusID, t.total_Amount, t.updatedAt, v.vehicleTypeName
            FROM tbl_transactions t
            INNER JOIN tbl_vehicletypes v ON t.vehicleTypeID = v.vehicleTypeID
            ORDER BY t.updatedAt DESC
            LIMIT 6
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }

    public function getTransactionByPlate($plateNumber) {
        $selectQuery = "
            SELECT t.transaction_ID, t.plateNumber, t.time_IN, v.vehicleTypeName, v.vehicleTypeRate,
            TIMESTAMPDIFF(MINUTE, t.time_IN, NOW()) as minutesParked
            FROM tbl_transactions t
            INNER JOIN tbl_vehicletypes v ON t.vehicleTypeID = v.vehicleTypeID
            WHERE t.plateNumber = :plateNumber AND t.paymentStatusID = 1
            LIMIT 1
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->bindParam(":plateNumber", $plateNumber);
        $response->execute();
        return $response;
    }

    public function updateKioskPayment($transactionID, $totalAmount) {
        $updateQuery = "
            UPDATE tbl_transactions SET paymentStatusID = 3, total_Amount = :totalAmount, updatedAt = NOW()
            WHERE transaction_ID = :transactionID
        ";
        $response = $this->connect->prepare($updateQuery);
        $response->bindParam(":transactionID", $transactionID);
        $response->bindParam(":totalAmount", $totalAmount);
        return $response->execute();
    }





public function getParkedVehicleStats(){
      
        $selectQuery = "
            SELECT v.vehicleTypeName, COUNT(t.transaction_ID) as vehicleCount
            FROM tbl_vehicletypes v
            LEFT JOIN tbl_transactions t ON v.vehicleTypeID = t.vehicleTypeID AND t.paymentStatusID IN (1, 3)
            GROUP BY v.vehicleTypeName
        ";
        $response = $this->connect->prepare($selectQuery);
        $response->execute();
        return $response;
    }







    
}



?>