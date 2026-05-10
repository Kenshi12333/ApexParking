<?php 
session_start();
require_once "../bl/userManager.php";
require_once "../bl/parkingManager.php";

$usermanager = new userManager();
$parkingManager = new parkingManager();

if(isset($_POST["fName"], $_POST["lName"]) && $_POST["roleID"] && !isset($_POST["uID"])) {
    $usermanager->addUserFunc($_POST["fName"], $_POST["lName"], $_POST["roleID"], $_POST["email"], $_POST["password"]);
    exit;
} elseif (isset($_POST["fName"], $_POST["lName"],$_POST["uID"],$_POST["roleID"], $_POST["email"], $_POST["password"])) {
    $usermanager->updateUserFunc($_POST["fName"],$_POST["lName"],$_POST["uID"],$_POST["roleID"], $_POST["email"], $_POST["password"]);
} elseif (isset($_POST["remover"])) {
    $usermanager->removeUserFunc($_POST["remover"]);
} else if (isset($_POST["lEmail"], $_POST["lPassword"])) {
    $usermanager->loginFunc($_POST["lEmail"], $_POST["lPassword"]);
}



elseif(isset($_POST["plateNumber"], $_POST["vehicleTypeID"], $_POST["slotID"])) {
    $parkingManager->checkInVehicleFunc($_POST["plateNumber"], $_POST["vehicleTypeID"], $_POST["slotID"]);
    exit;
}
 

elseif(isset($_POST["checkoutID"], $_POST["totalAmount"], $_POST["discountID"], $_POST["penaltyID"])) {
    $parkingManager->checkOutVehicleFunc($_POST["checkoutID"], $_POST["totalAmount"], $_POST["discountID"], $_POST["penaltyID"]);
    exit;
}


elseif(isset($_POST["action"]) && $_POST["action"] == "getBillByPlate") {
    $parkingManager->getBillByPlateFunc($_POST["plateNumber"]);
    exit;
}



elseif(isset($_POST["action"]) && $_POST["action"] == "processKioskPayment") {
    $transactionID = $_POST["transactionID"] ?? "";
    $totalAmount = $_POST["totalAmount"] ?? "";
    $email = $_POST["email"] ?? null;
    $plateNumber = $_POST["plateNumber"] ?? "Unknown Plate";
    
    if ($transactionID == "" || $totalAmount == "") {
        echo "Missing fields";
        exit;
    }

    $dbUpdated = $parkingManager->processKioskPaymentFunc($transactionID, $totalAmount);

    if ($dbUpdated) {
        
    
        if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            require_once "../helper/sendEmail.php";
            $subject = "Apex Parking - Payment Receipt";
            $date = date("F j, Y, h:i A");
            
      $body = '
            <table width="100%" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #0a0e27 0%, #132a4c 50%, #1a3a52 100%); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; padding: 50px 20px;">
                <tr>
                    <td align="center">
                        <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 500px;">
                            
                            <!-- Logo Section -->
                            <tr>
                                <td align="center" style="padding-bottom: 40px;">
                                    <div style="display: inline-block; background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); padding: 15px; border-radius: 16px; box-shadow: 0 10px 30px rgba(13, 71, 161, 0.4);">
                                        <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" width="70" style="border-radius: 12px; display: block;">
                                    </div>
                                </td>
                            </tr>

                            <!-- Main Content Card -->
                            <tr>
                                <td style="background: linear-gradient(135deg, #0f1729 0%, #1a2332 100%); border-radius: 20px; padding: 50px 35px; border: 1px solid rgba(96, 165, 250, 0.2); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);">
                                    
                                    <!-- Header -->
                                    <table width="100%">
                                        <tr>
                                            <td align="center" style="padding-bottom: 35px;">
                                                <div style="display: inline-block; background: rgba(34, 197, 94, 0.15); padding: 8px 20px; border-radius: 50px; border: 1px solid rgba(34, 197, 94, 0.4); margin-bottom: 20px;">
                                                    <p style="margin: 0; color: #22c55e; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; font-weight: 700;">✓ Payment Confirmed</p>
                                                </div>
                                                <h1 style="margin: 0 0 15px 0; color: #f0f9ff; font-size: 42px; font-weight: 900; letter-spacing: -1.5px; line-height: 1.1;">Thank You!</h1>
                                                <p style="margin: 0; color: #cbd5e1; font-size: 15px; line-height: 1.6;">Your parking session has been successfully completed. Your vehicle is cleared to exit.</p>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Amount Box (Highlight) -->
                                    <div style="background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(22, 163, 74, 0.2) 100%); border: 2px solid #22c55e; border-radius: 16px; padding: 30px; text-align: center; margin: 30px 0; box-shadow: inset 0 2px 8px rgba(34, 197, 94, 0.1);">
                                        <p style="margin: 0 0 12px 0; color: #cbd5e1; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">Total Amount Due</p>
                                        <p style="margin: 0; color: #4ade80; font-size: 48px; font-weight: 900; letter-spacing: -2px;">₱'.$totalAmount.'</p>
                                    </div>

                                    <!-- Transaction Details -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; margin: 35px 0;">
                                        
                                        <tr>
                                            <td style="padding: 15px 0; border-bottom: 1px solid rgba(96, 165, 250, 0.15);">
                                                <p style="margin: 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Plate Number</p>
                                                <p style="margin: 8px 0 0 0; color: #e0f2fe; font-size: 16px; font-weight: 700; font-family: "Courier New", monospace; letter-spacing: 2px;">'.$plateNumber.'</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 15px 0; border-bottom: 1px solid rgba(96, 165, 250, 0.15);">
                                                <p style="margin: 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Transaction Date & Time</p>
                                                <p style="margin: 8px 0 0 0; color: #cbd5e1; font-size: 14px;">'.$date.'</p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding: 15px 0;">
                                                <p style="margin: 0; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Receipt ID</p>
                                                <p style="margin: 8px 0 0 0; color: #60a5fa; font-size: 15px; font-weight: 700; font-family: "Courier New", monospace;">TKN-'.$transactionID.'</p>
                                            </td>
                                        </tr>

                                    </table>

                                    <!-- Status Message -->
                                    <div style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(96, 165, 250, 0.1) 100%); border-left: 4px solid #3b82f6; border-radius: 8px; padding: 20px; margin-top: 30px;">
                                        <p style="margin: 0; color: #60a5fa; font-size: 13px; font-weight: 600; line-height: 1.6;">
                                            <span style="color: #4ade80; font-weight: 900;">✓</span> Your exit gate is now cleared. You can proceed to exit the parking facility at your convenience.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="padding-top: 50px;">
                                    
                                    <!-- Support Button -->
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td align="center" style="padding-bottom: 35px;">
                                                <a href="mailto:JAGSolutions@mail.com" style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%); color: #ffffff; padding: 14px 40px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; display: inline-block; transition: all 0.3s ease; border: none; box-shadow: 0 8px 16px rgba(13, 71, 161, 0.3);">Get Support</a>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Copyright -->
                                    <div style="border-top: 1px solid rgba(96, 165, 250, 0.15); padding-top: 30px;">
                                        <p style="margin: 0; color: #60a5fa; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Apex Parking Systems</p>
                                        <p style="margin: 8px 0 0 0; color: #7dd3fc; font-size: 11px;">Powered by JAGSolutions</p>
                                        <p style="margin: 8px 0 0 0; color: #475569; font-size: 10px;">Manila, Philippines | &copy; 2026 All Rights Reserved</p>
                                    </div>
                                </td>
                            </tr>
                            
                        </table>
                    </td>
                </tr>
            </table>';

            sendEmail($email, "Valued Customer", $subject, $body);
        }

     
        echo "success";
    } else {
        echo "Database update failed";
    }
    exit;
}

























elseif(isset($_POST["action"]) && $_POST["action"] == "summarizeOperations") {
    try {
    
        $earnings = $parkingManager->getTodayEarnings();
        $stats = $parkingManager->getSlotStats();
        $recent = $parkingManager->getRecentActivity();
        
        $date = date("F j, Y");
        $managerEmail = "apexparkingsystems99@gmail.com"; 

        require_once "../helper/sendEmail.php";
        $subject = "Operations Summary - " . $date;

       
        $activityRows = "";
        if(!empty($recent)) {
            foreach(array_slice($recent, 0, 5) as $row) {
                $amount = number_format($row['total_Amount'] ?? 0, 2);
                $plate = htmlspecialchars($row['plateNumber']);
                $activityRows .= "
        <tr>
            <td style='padding: 8px 0; border-bottom: 1px solid #282828; color: #ffffff;'>{$plate}</td>
            <td style='padding: 8px 0; border-bottom: 1px solid #282828; color: #b3b3b3; text-align: right;'>₱{$amount}</td>
        </tr>";
            }
        }

        
        $formattedEarnings = number_format($earnings, 2);
        $totalSlots = $stats['total_slots'] ?? 0;
        $occupiedSlots = $stats['occupied_slots'] ?? 0;

       
      $body = "
<table width='100%' cellpadding='0' cellspacing='0' style='background-color: #0f172a; font-family: Helvetica, Arial, sans-serif; padding: 40px 10px;'>
    <tr>
        <td align='center'>
            <table width='100%' cellpadding='0' cellspacing='0' style='max-width: 500px;'>
                
                <tr>
                    <td align='center' style='padding-bottom: 25px;'>
                        <img src='https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80' width='60' style='border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);'>
                    </td>
                </tr>
                
                <tr>
                    <td style='background-color: #1e293b; border-radius: 16px; padding: 40px 30px; border: 1px solid #334155; box-shadow: 0 10px 25px rgba(0,0,0,0.5);'>
                        
                        <h3 style='margin: 0; color: #3b82f6; font-size: 14px; text-transform: uppercase; letter-spacing: 2px;'>Daily Report</h3>
                        <h1 style='margin: 10px 0 30px 0; color: #f8fafc; font-size: 32px; font-weight: 900; letter-spacing: -0.5px;'>Operations Summary</h1>
                        
                        <table width='100%' style='margin-bottom: 35px;'>
                            <tr>
                                <td width='48%' style='background-color: #0f172a; padding: 20px 15px; border-radius: 12px; border: 1px solid #334155; text-align: center;'>
                                    <p style='margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;'>Total Revenue</p>
                                    <h2 style='margin: 8px 0 0 0; color: #22c55e; font-size: 24px; font-weight: 900;'>₱{$formattedEarnings}</h2>
                                </td>
                                <td width='4%'></td>
                                <td width='48%' style='background-color: #0f172a; padding: 20px 15px; border-radius: 12px; border: 1px solid #334155; text-align: center;'>
                                    <p style='margin: 0; color: #94a3b8; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold;'>Occupancy</p>
                                    <h2 style='margin: 8px 0 0 0; color: #f8fafc; font-size: 24px; font-weight: 900;'>{$occupiedSlots} / {$totalSlots}</h2>
                                </td>
                            </tr>
                        </table>

                        <h4 style='margin: 0 0 15px 0; color: #f8fafc; font-size: 16px; border-bottom: 2px dashed #334155; padding-bottom: 12px;'>Recent Checkouts</h4>
                        <table width='100%' style='font-size: 14px;'>
                            {$activityRows}
                        </table>

                        <div style='margin-top: 35px; text-align: center; border-top: 1px solid #334155; padding-top: 20px;'>
                            <p style='margin: 0; color: #64748b; font-size: 12px;'>Generated on {$date}</p>
                        </div>

                    </td>
                </tr>
                
                <tr>
                    <td align='center' style='padding-top: 25px;'>
                        <p style='margin: 0; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: bold;'>&copy; 2026 Apex Parking Systems</p>
                        <p style='margin: 5px 0 0 0; color: #475569; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;'>Property of JAGSolutions</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>";
       
        $result = sendEmail($managerEmail, "Manager", $subject, $body);
        if($result === true) {
            echo "success";
        } else {
            echo "Failed to send email";
        }
        exit;

    } catch(Exception $ex) {
        echo "Error: " . $ex->getMessage();
        exit;
    }
}



elseif(isset($_POST["action"]) && $_POST["action"] == "kioskExitAuth") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    require_once "../bl/userManager.php";
    $uManager = new userManager();
    
    $uManager->loginFunc($email, $password); 
    exit;
}

?>