<?php 
session_start();
require_once "../bl/parkingManager.php";

$parkingManager = new parkingManager();

$vehicleTypes = $parkingManager->getVehicleTypes();
$parkingSlots = $parkingManager->getParkingSlots();
$activeVehicles = $parkingManager->getActiveVehicles();
$discounts = $parkingManager->getDiscounts();
$penalties = $parkingManager->getPenalties();
$vehicleCount = count($activeVehicles);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal | Apex Parking Systems</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #e2e8f0;
            min-height: 100vh;
            overflow: hidden;
        }

        .main-container { display: flex; flex-direction: column; height: 100vh; }
        nav { flex-shrink: 0; }
        .content-wrapper { flex: 1; display: flex; overflow: hidden; padding: 20px; gap: 20px; }
        
        .pos-card {
            background: rgba(15, 23, 42, 0.65); 
            backdrop-filter: blur(16px);
            border-radius: 16px;
            overflow: hidden; 
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .card-left { flex: 0 0 35%; display: flex; flex-direction: column; }
        .card-right { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        
        .pos-header { 
            background: rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #60a5fa; 
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .pos-body { padding: 20px; flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        
        .plate-box, .form-section, .parking-tracker, .btn-log-entry { flex-shrink: 0; }

        .plate-box { background: rgba(0, 0, 0, 0.4); border: 1px dashed #3b82f6; border-radius: 12px; padding: 12px; text-align: center; }
        .plate-input { 
            font-size: 2.2rem !important; text-transform: uppercase; text-align: center; font-weight: 900; 
            letter-spacing: 3px; color: #ffffff !important; border-bottom: none !important; margin: 0 !important; height: auto !important;
        }
        .plate-sub { font-size: 0.7rem; color: #60a5fa; text-transform: uppercase; letter-spacing: 2px; margin-top: 3px; }
        
        .form-section { display: flex; flex-direction: column; gap: 10px; }
        .input-field { margin: 0 !important; }
        .input-field input, .select-wrapper input.select-dropdown { 
            color: #ffffff !important; 
            border-bottom: 1px solid rgba(255,255,255,0.3) !important; 
            font-size: 0.9rem !important; 
            text-align: center !important; 
        }
        .input-field label { color: #94a3b8 !important; font-size: 0.85rem !important; }
        
        .dropdown-content { background-color: #1e293b !important; }
        .dropdown-content li > span { 
            color: #60a5fa !important; 
            font-size: 0.9rem !important;
            text-align: center !important;
            display: block !important;
            width: 100% !important;
        }
        
        .parking-tracker { background: rgba(0, 0, 0, 0.3); border-radius: 10px; padding: 12px; }
        .tracker-title { color: #60a5fa; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 600; }
        
        .parking-grid { 
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; max-height: 180px; overflow-y: auto; 
            -ms-overflow-style: none; scrollbar-width: none;  
        }
        .parking-grid::-webkit-scrollbar { display: none; }

        .parking-slot {
            padding: 8px; border-radius: 6px; font-size: 0.65rem; text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2); cursor: pointer; transition: all 0.2s ease;
            font-weight: 600; color: #ffffff;
        }
        .parking-slot.available { background: rgba(34, 197, 94, 0.2); border-color: #22c55e; }
        .parking-slot.available:hover { background: rgba(34, 197, 94, 0.4); box-shadow: 0 0 8px rgba(34, 197, 94, 0.3); }
        .parking-slot.occupied { background: rgba(239, 68, 68, 0.2); border-color: #ef4444; opacity: 0.5; cursor: not-allowed; }
        
        .btn-log-entry { 
            background: linear-gradient(135deg, #22c55e, #16a34a) !important; border-radius: 10px !important; 
            font-weight: 700 !important; height: 50px !important; line-height: 50px !important; margin-top: auto !important; flex-shrink: 0; 
        }
        
        .vehicle-item { 
            padding: 15px; background: rgba(255, 255, 255, 0.03); border-radius: 10px; display: flex; 
            justify-content: space-between; align-items: center; border: 1px solid rgba(255, 255, 255, 0.05); gap: 10px; min-height: 70px; flex-shrink: 0;
            margin-bottom: 10px;
        }
        .vehicle-item:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(59, 130, 246, 0.4); }
        .vehicle-info { flex: 1; min-width: 0; }
        .vehicle-item h4 { color: #ffffff !important; margin: 0 0 5px 0 !important; font-weight: 800; font-size: 1.2rem; }
        .vehicle-item p { color: #94a3b8 !important; margin: 0 !important; font-size: 0.8rem; }
        
        .status-badge {
            background-color: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444;
            font-size: 0.6rem; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: bold; white-space: nowrap;
        }
        
        .btn-checkout { 
            background: linear-gradient(135deg, #ef6c00, #e65100) !important; border-radius: 8px !important; 
            font-weight: 700 !important; font-size: 0.8rem !important; padding: 0 15px !important; 
            height: 38px !important; display: inline-flex !important; align-items: center !important; 
            white-space: nowrap !important; flex-shrink: 0;
        }
        .btn-checkout i { margin-right: 6px !important; font-size: 1.1rem !important; line-height: 1 !important; }
        
        #active-vehicles-list { overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 10px; padding: 15px; }
        
        .checkout-modal { border-radius: 20px !important; background: linear-gradient(135deg, rgba(15, 23, 42, 0.98), rgba(30, 41, 59, 0.98)) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; max-height: 90vh !important; }
        .checkout-header { background: linear-gradient(135deg, #0d47a1, #1565c0); padding: 20px !important; border-radius: 20px 20px 0 0 !important; text-align: center; color: white; }
        .checkout-header h3 { margin: 0; font-weight: 900; letter-spacing: 1px; font-size: 1.5rem; }
        .modal-content { color: #e2e8f0 !important; padding: 20px !important; }
        
        .vehicle-detail { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; padding: 15px; margin-bottom: 15px; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 0.9rem; }
        .detail-label { color: #94a3b8; }
        .detail-value { color: #60a5fa; font-weight: 700; }
        
        .total-box { background: rgba(34, 197, 94, 0.15); border: 2px solid #22c55e; border-radius: 10px; padding: 20px; text-align: center; margin: 15px 0; }
        .total-label { color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; }
        .total-amount { font-size: 2.2rem; font-weight: 900; color: #22c55e; margin-top: 5px; }
        
        .modal-footer { 
            background: rgba(0, 0, 0, 0.3) !important; border-top: 1px solid rgba(255, 255, 255, 0.1) !important; 
            padding: 15px !important; display: flex !important; justify-content: flex-end !important; gap: 15px !important;
        }
        .btn-confirm { background: linear-gradient(135deg, #22c55e, #16a34a) !important; border-radius: 8px !important; font-weight: 700 !important; }
        .btn-close-modal { background: rgba(255, 255, 255, 0.1) !important; color: #e2e8f0 !important; border: 1px solid rgba(255, 255, 255, 0.2) !important; border-radius: 8px !important; }
        
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 3px; }
    </style>
</head>
<body>

    <div class="main-container">
    <nav class="blue darken-4 z-depth-0">
    <div class="nav-wrapper" style="padding: 0 30px;">
        <a href="DashboardPage.php" class="brand-logo" style="display: flex; align-items: center; cursor: default;">
            <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" 
                 alt="Logo" 
                 style="height: 40px; border-radius: 4px; margin-right: 15px;">
            <span style="font-weight: 300; color: #ffffff;">POS <strong style="font-weight: 900;">TERMINAL</strong></span>
        </a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <?php if(isset($_SESSION['loggedUser']) && $_SESSION['loggedUser']['userRoleID'] == 1): ?>
                <li><a href="registrationPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                    <i class="material-icons left">people</i>User Mgmt
                </a></li>

                <li><a href="DashboardPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                    <i class="material-icons left">dashboard</i>Dashboard
                </a></li>
            <?php endif; ?>
            <li><a href="LoginPage.php" class="waves-effect waves-light btn red darken-1 z-depth-0" style="border-radius: 8px; font-weight: bold;">
                <i class="material-icons left">exit_to_app</i>Sign Out
            </a></li>
        </ul>
    </div>
</nav>

        <div class="content-wrapper">
            
            <div class="pos-card card-left">
                <div class="pos-header">
                    <span><i class="material-icons left" style="font-size: 1rem;">add_circle</i> New Check-In</span>
                </div>
                <div class="pos-body">
                    <div class="plate-box">
                        <input id="plateNum" type="text" class="plate-input" placeholder="ABC 1234" maxlength="8">
                        <div class="plate-sub">Plate Number</div>
                    </div>

                    <div class="form-section">
                        <div class="input-field">
                            <i class="material-icons prefix">directions_car</i>
                            <select id="vehicleType">
                                <option value="" disabled selected>Select Category</option>
                                <?php foreach($vehicleTypes as $type): ?>
                                    <option value="<?= $type['vehicleTypeID'] ?>"><?= htmlspecialchars($type['vehicleTypeName']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>Vehicle Type</label>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="input-field">
                            <i class="material-icons prefix">local_parking</i>
                            <select id="slotID">
                                <option value="" disabled selected>Assign Slot</option>
                                <?php foreach($parkingSlots as $slot): ?>
                                    <?php $status = strtolower($slot['slotStatus'] ?? 'available'); ?>
                                    <option value="<?= htmlspecialchars($slot['slotName']) ?>" data-status="<?= $status ?>" <?php if($status === 'occupied') echo 'disabled'; ?>>
                                        <?= htmlspecialchars($slot['slotName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label>Parking Slot</label>
                        </div>
                    </div>

                    <div class="parking-tracker">
                        <div class="tracker-title">Live Tracker</div>
                        <div class="parking-grid" id="parkingGrid">
                            <?php foreach($parkingSlots as $slot): ?>
                                <?php 
                                    $currentStatus = strtolower($slot['slotStatus'] ?? 'available'); 
                                    $onClick = ($currentStatus === 'available') ? "onclick=\"selectSlot('" . htmlspecialchars($slot['slotName']) . "')\"" : "";
                                ?>
                                <div class="parking-slot <?= $currentStatus ?>" <?= $onClick ?>>
                                    <?= htmlspecialchars($slot['slotName']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="waves-effect waves-light btn btn-log-entry" onclick="processEntry()">
                        LOG ENTRY <i class="material-icons right">check_circle</i>
                    </button>
                </div>
            </div>

            <div class="pos-card card-right">
                <div class="pos-header">
                    <span><i class="material-icons left" style="font-size: 1rem;">schedule</i> Awaiting Payment</span>
                    <span id="vehicleCountBadge" style="background: rgba(0,0,0,0.3); padding: 3px 12px; border-radius: 15px; font-size: 0.75rem;">
                        <?= $vehicleCount ?> Vehicle<?= $vehicleCount != 1 ? 's' : '' ?>
                    </span>
                </div>
                
                <div id="active-vehicles-list">
                    <?php if(!empty($activeVehicles)): ?>
                        <?php foreach($activeVehicles as $vehicle): ?>
                            <?php 
                                $isKioskPaid = ($vehicle['paymentStatusID'] == 3);
                                $gracePeriod = 15; 
                                $isFree = $vehicle['minutesParked'] <= $gracePeriod;
                                $effectiveRate = $isFree ? 0 : $vehicle['vehicleTypeRate'];
                            ?>
                            <div class="vehicle-item" style="<?= $isKioskPaid ? 'border-left: 5px solid #22c55e;' : '' ?>">
                                <div class="vehicle-info">
                                    <h4>
                                        <?= htmlspecialchars($vehicle['plateNumber']) ?> 
                                        <?php if($isKioskPaid): ?>
                                            <span class="status-badge" style="background:#22c55e; color:#fff; border-color:#22c55e;">PAID AT KIOSK</span>
                                        <?php elseif($isFree): ?>
                                            <span class="status-badge" style="background:rgba(34,197,94,0.2); color:#22c55e; border-color:#22c55e;">Grace Period</span>
                                        <?php else: ?>
                                            <span class="status-badge">Unpaid</span>
                                        <?php endif; ?>
                                    </h4>
                                    <p>
                                        <i class="material-icons tiny" style="vertical-align: -2px;">directions_car</i> <?= htmlspecialchars($vehicle['vehicleTypeName']) ?> | 
                                        <i class="material-icons tiny" style="vertical-align: -2px;">schedule</i> <?= date("h:i A", strtotime($vehicle['time_IN'])) ?>
                                        <span style="margin-left: 8px; color: #60a5fa; font-weight: bold;">(<?= $vehicle['minutesParked'] ?> mins)</span>
                                    </p>
                                </div>

                                <?php if($isKioskPaid): ?>
                                    <button class="waves-effect waves-light btn green" 
                                            onclick="finalExit(<?= $vehicle['transaction_ID'] ?>, <?= $vehicle['total_Amount'] ?>)">
                                        <i class="material-icons">door_sliding</i> OPEN GATE
                                    </button>
                                <?php else: ?>
                                    <a class="waves-effect waves-light btn btn-checkout" 
                                       onclick="openCheckoutModal(<?= $vehicle['transaction_ID'] ?>, '<?= htmlspecialchars($vehicle['plateNumber']) ?>', '<?= htmlspecialchars($vehicle['vehicleTypeName']) ?>', <?= $effectiveRate ?>)">
                                        <i class="material-icons">payments</i> CHECKOUT
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: #94a3b8; padding: 40px 20px; font-style: italic;">
                            No vehicles currently parked.
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <footer class="blue darken-4" style="flex-shrink: 0; padding: 10px 0; text-align: center; border-top: none;">
            <div style="font-weight: 600; font-size: 1.1rem; color: #ffffff; letter-spacing: 1px;">Apex Parking Systems</div>
            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.8); margin-top: 5px;">Property of JAGSolutions | All rights reserved&reg; 2026</div>
        </footer>
    </div>

    <div id="checkoutModal" class="modal checkout-modal" style="width: 90%; max-width: 500px;">
        <div class="checkout-header">
            <h3>PROCESS PAYMENT</h3>
        </div>
        
        <div class="modal-content">
            <div style="text-align: center; margin-bottom: 15px;">
                <div id="plateNumberDisplay" style="font-size: 2rem; font-weight: 900; color: #60a5fa; letter-spacing: 2px;"></div>
                <div id="vehicleTypeDisplay" style="color: #94a3b8; font-size: 0.9rem;"></div>
            </div>

            <div class="vehicle-detail">
                <div class="detail-row">
                    <span class="detail-label">Check-In Time</span>
                    <span class="detail-value" id="checkInTimeDisplay"></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Daily Rate</span>
                    <span class="detail-value">₱ <span id="rateDisplay"></span></span>
                </div>
            </div>

            <div class="total-box">
                <div class="total-label">TOTAL DUE</div>
                <div class="total-amount" id="totalDueDisplay">₱ 0.00</div>
            </div>

            <div class="form-section">
                <div class="input-field">
                    <i class="material-icons prefix">local_offer</i>
                    <select id="discountSelect" onchange="calculateTotal()">
                        <option value="0" selected>No Discount</option>
                        <?php foreach($discounts as $discount): ?>
                            <option value="<?= $discount['discountID'] ?>|<?= $discount['discountAmount'] ?>"><?= htmlspecialchars($discount['discountName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Discount</label>
                </div>
            </div>

            <div class="form-section">
                <div class="input-field">
                    <i class="material-icons prefix">warning</i>
                    <select id="penaltySelect" onchange="calculateTotal()">
                        <option value="0" selected>No Penalty</option>
                        <?php foreach($penalties as $penalty): ?>
                            <option value="<?= $penalty['penaltyID'] ?>|<?= $penalty['penaltyAmount'] ?>"><?= htmlspecialchars($penalty['penaltyName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Penalty</label>
                </div>
            </div>

            <div class="form-section">
                <div class="input-field">
                    <i class="material-icons prefix">attach_money</i>
                    <input type="number" id="cashTenderedInput" step="0.01" min="0" placeholder="0.00" oninput="calculateChange()">
                    <label for="cashTenderedInput">Cash Tendered (₱)</label>
                </div>
            </div>

            <div style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px; padding: 12px; text-align: center; margin-top: 15px;">
                <div style="color: #94a3b8; font-size: 0.8rem;">CHANGE DUE</div>
                <div style="color: #60a5fa; font-size: 1.5rem; font-weight: 900;" id="changeDisplay">₱ 0.00</div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-light btn btn-close-modal">CANCEL</a>
            <button class="waves-effect waves-light btn btn-confirm" onclick="confirmPayment()">
                CONFIRM PAY <i class="material-icons right">check_circle</i>
            </button>
        </div>
    </div>

    <script src="../Scripts/terminalPOS.js"></script>
</body>
</html>