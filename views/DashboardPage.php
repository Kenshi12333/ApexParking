<?php 
session_start();
require_once "../bl/rolesManager.php";
require_once "../bl/parkingManager.php";

$rolesManager = new rolesManager();
$roleCounts = $rolesManager->getRoleCounts();
$label = array_column($roleCounts, 'roleName');
$data = array_column($roleCounts, 'total_users');

$parkingManager = new parkingManager();
$slotStats = $parkingManager->getSlotStats();
$todayRevenue = $parkingManager->getTodayEarnings();
$recentActivity = $parkingManager->getRecentActivity();


$vehicleStats = $parkingManager->getParkedVehicleStats();
$vLabels = array_column($vehicleStats, 'vehicleTypeName');
$vData = array_column($vehicleStats, 'vehicleCount');


$total_slots = ($slotStats['available_slots'] ?? 0) + ($slotStats['occupied_slots'] ?? 0);
$occupancy_percentage = ($total_slots > 0) ? round((($slotStats['occupied_slots'] ?? 0) / $total_slots) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Parking System</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { 
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #e2e8f0;
        }

        .dashboard-card {
            background: rgba(15, 23, 42, 0.65); 
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 16px;
            padding: 30px 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-5px);
        }

        .card-title-text {
            font-size: 1.2rem;
            font-weight: 500;
            color: #60a5fa;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 15px;
        }

        .card-number {
            font-size: 4rem;
            font-weight: 900;
            margin: 10px 0;
            color: #ffffff;
        }

        /* Circular Progress Bar Styles */
        .progress-container {
            display: flex; justify-content: center; align-items: center; padding: 20px 0;
        }
        .circular-progress {
            position: relative; height: 160px; width: 160px; border-radius: 50%;
            background: conic-gradient(#3b82f6 0deg, rgba(255, 255, 255, 0.1) 0deg);
            display: flex; align-items: center; justify-content: center;
        }
        .circular-progress::before {
            content: ""; position: absolute; height: 130px; width: 130px; border-radius: 50%; 
            background-color: #0f1729; 
        }
        .progress-value { position: relative; font-size: 2.5rem; font-weight: 700; color: #ffffff; }

        /* Activity Feed Styles */
        .activity-feed-container { max-height: 250px; overflow-y: auto; padding-right: 10px; text-align: left;}
        .activity-item { padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; justify-content: space-between; align-items: center; }
        .activity-item:last-child { border-bottom: none; }
        .activity-action { color: #e2e8f0; font-weight: 500; font-size: 1rem; display: flex; align-items: center;}
        .activity-time { color: #94a3b8; font-size: 0.85rem; white-space: nowrap; margin-left: 15px; }
        
        .activity-feed-container::-webkit-scrollbar { width: 6px; }
        .activity-feed-container::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); border-radius: 4px;}
        .activity-feed-container::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.5); border-radius: 4px; }

        /* Navbar Button Styles */
        .btn-summary {
            background: linear-gradient(135deg, #22c55e, #16a34a) !important;
            border-radius: 8px !important;
            font-weight: 800 !important;
        }

        .btn-summary:hover {
            background: linear-gradient(135deg, #16a34a, #15803d) !important;
        }

        .roles-overview-row > .col {
            margin-bottom: 28px;
            padding: 0 18px;
        }

        .analytics-row > .col {
            padding: 0 20px;
            margin-bottom: 28px;
        }

        .chart-canvas-wrap {
            position: relative;
            width: 100%;
            box-sizing: border-box;
            padding: 0 28px 8px;
        }
    </style>
</head>
<body>

  <nav class="blue darken-4 z-depth-0">
    <div class="nav-wrapper" style="padding: 0 30px;">
        <a href="DashboardPage.php" class="brand-logo" style="display: flex; align-items: center; cursor: default;">
            <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" 
                 alt="Logo" 
                 style="height: 40px; border-radius: 4px; margin-right: 15px;">
            <span style="font-weight: 300; color: #ffffff;">System Dashboard</span>
        </a>
        
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <?php if(isset($_SESSION['loggedUser']) && $_SESSION['loggedUser']['userRoleID'] == 1): ?>
                <li><a href="registrationPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                    <i class="material-icons left">people</i>User Mgmt
                </a></li>
            <?php endif; ?>

            <li><a href="terminalPOSPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                <i class="material-icons left">point_of_sale</i>POS Terminal
            </a></li>

            <li><a onclick="sendOperationsSummary()" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px; cursor: pointer;">
                <i class="material-icons left">summarize</i>Summarize
            </a></li>

            <li><a href="LoginPage.php" class="waves-effect waves-light btn red darken-1 z-depth-0" style="border-radius: 8px; font-weight: bold;">
                <i class="material-icons left">exit_to_app</i>Sign Out
            </a></li>
        </ul>
    </div>
</nav>
    <div class="container" style="width: 92%; max-width: 1500px; margin-top: 40px; margin-bottom: 50px; padding: 0 24px; box-sizing: border-box;">
        
        <h5 style="color: #ffffff; font-weight: 300; margin-bottom: 30px;">Live Operations</h5>
        
        <div class="row">
            <div class="col s12 m4">
                <div class="dashboard-card center-align">
                    <i class="material-icons" style="font-size: 3rem; color: #22c55e;">local_parking</i>
                    <div class="card-title-text" style="color: #22c55e;">Available Slots</div>
                    <div class="card-number"><?= htmlspecialchars($slotStats['available_slots'] ?? 0) ?></div>
                    <div class="grey-text" style="font-size: 0.9rem; letter-spacing: 1px;">Ready for Check-In</div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="dashboard-card center-align">
                    <i class="material-icons" style="font-size: 3rem; color: #ef4444;">directions_car</i>
                    <div class="card-title-text" style="color: #ef4444;">Occupied Slots</div>
                    <div class="card-number"><?= htmlspecialchars($slotStats['occupied_slots'] ?? 0) ?></div>
                    <div class="grey-text" style="font-size: 0.9rem; letter-spacing: 1px;">Currently Parked</div>
                </div>
            </div>

            <div class="col s12 m4">
                <div class="dashboard-card center-align">
                    <i class="material-icons" style="font-size: 3rem; color: #eab308;">payments</i>
                    <div class="card-title-text" style="color: #eab308;">Today's Revenue</div>
                    <div class="card-number" style="font-size: 3.5rem;">₱<?= number_format($todayRevenue, 2) ?></div>
                    <div class="grey-text" style="font-size: 0.9rem; letter-spacing: 1px;">Total Collected Today</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col s12 m5">
                <div class="dashboard-card center-align">
                    <div class="card-title-text" style="margin-top: 0; margin-bottom: 10px;">Lot Occupancy</div>
                    <div class="progress-container">
                        <div class="circular-progress">
                            <span class="progress-value">0%</span>
                        </div>
                    </div>
                    <div class="grey-text" style="font-size: 1rem; margin-top: 10px;">
                        <?= htmlspecialchars($slotStats['occupied_slots'] ?? 0) ?> / <?= $total_slots ?> Total Slots Filled
                    </div>
                </div>
            </div>







            <div class="col s12 m7">
                <div class="dashboard-card">
                    <div class="card-title-text" style="margin-top: 0; margin-bottom: 20px;">Live Activity Feed</div>
                    <div class="activity-feed-container">
                        
                        <?php if(!empty($recentActivity)): ?>
                            <?php foreach($recentActivity as $activity): ?>
                                <?php 
                                    $isCheckIn = ($activity['paymentStatusID'] == 1);
                                    $icon = $isCheckIn ? 'login' : 'logout';
                                    $color = $isCheckIn ? '#22c55e' : '#ef4444';
                                    $action = $isCheckIn ? 'Check-In at Slot ' . ($activity['parkingSlot'] ?? 'N/A') : 'Check-Out (Paid ₱' . number_format($activity['total_Amount'] ?? 0, 2) . ')';
                                    $timeText = date('h:i A', strtotime($activity['updatedAt']));
                                ?>
                                <div class="activity-item">
                                    <span class="activity-action">
                                        <i class="material-icons left" style="color: <?= $color ?>; margin-right: 15px; font-size: 18px;"><?= $icon ?></i>
                                        <strong style="color: #60a5fa; margin-right: 8px;">
                                            <?= htmlspecialchars($activity['vehicleTypeName']) ?>
                                        </strong> 
                                        [<?= htmlspecialchars($activity['plateNumber']) ?>] - <?= $action ?>
                                    </span>
                                    <span class="activity-time"><?= $timeText ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="text-align: center; color: #94a3b8; padding: 20px 0; font-style: italic;">
                                No recent activity found.
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>

        <h5 style="color: #ffffff; font-weight: 300; margin-top: 40px; margin-bottom: 30px;">System Roles Overview</h5>
        
        <div class="row roles-overview-row">
            <?php foreach($roleCounts as $index => $role) : ?>
                <div class="col s12 m6 l4">
                    <div class="dashboard-card center-align">
                        <i class="material-icons" style="font-size: 3rem; color: #94a3b8;">badge</i>
                        <div class="card-title-text"><?= htmlspecialchars($role['roleName']) ?></div>
                        <div class="card-number"><?= $role['total_users'] ?></div>
                        <div class="grey-text" style="font-size: 0.9rem; letter-spacing: 1px;">Registered Users</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h5 style="color: #ffffff; font-weight: 300; margin-top: 40px; margin-bottom: 30px;">System Analytics</h5>
        
        <div class="row analytics-row">
            <div class="col s12 l6">
                <div class="dashboard-card center-align">
                    <div class="card-title-text" style="margin-bottom: 20px;">Users per System Role</div>
                    <div class="chart-canvas-wrap" style="height: 350px;">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col s12 l6">
                <div class="dashboard-card center-align">
                    <div class="card-title-text" style="margin-bottom: 20px;">Parking Space Utilization</div>
                    <div class="chart-canvas-wrap" style="height: 350px;">
                        <canvas id="parkingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>




        <div class="row analytics-row">
            <div class="col s12">
                <div class="dashboard-card center-align">
                    <div class="card-title-text" style="margin-bottom: 20px;">Parked Vehicle Types</div>
                    <div class="chart-canvas-wrap" style="height: 280px; max-width: 520px; margin: 0 auto;">
                        <canvas id="vehicleDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <footer class="blue darken-4" style="margin-top: 50px; padding: 20px 0; text-align: center;">
        <div style="font-weight: 600; font-size: 1.1rem; color: #ffffff; letter-spacing: 1px;">Apex Parking Systems</div>
        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.8); margin-top: 5px;">Property of JAGSolutions | All rights reserved&reg; 2026</div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <script>
     
        window.barData = {
            label: <?= json_encode($label); ?>,
            data: <?= json_encode($data); ?>
        };

        window.parkingData = {
            label: ['Available Slots', 'Occupied Slots'],
            data: [<?= $slotStats['available_slots'] ?? 0 ?>, <?= $slotStats['occupied_slots'] ?? 0 ?>]
        };
        window.vehicleTypeData = {
            label: <?= json_encode($vLabels); ?>,
            data: <?= json_encode($vData); ?>
        };

        window.occupancyData = {
            percentage: <?= $occupancy_percentage ?>
        };

    
        function sendOperationsSummary(){
            Swal.fire({
                title: 'Send Daily Summary?',
                text: 'This will send an operations summary email to the management team.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22c55e',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Send',
                cancelButtonText: 'Cancel',
                background: '#1e293b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sending...',
                        text: 'Please wait while we prepare and send the summary.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        background: '#1e293b',
                        color: '#fff'
                    });

                    $.post("../controllers/Controller.php", { 
                        sendSummary: 1
                    }, function(response) {
                        Swal.fire({
                            title: "Summary Sent!",
                            text: "Daily operations summary has been sent successfully.",
                            icon: "success",
                            background: '#1e293b',
                            color: '#fff'
                        });
                    }).fail(function() {
                        Swal.fire({
                            title: "Error",
                            text: "Failed to send summary. Please try again.",
                            icon: "error",
                            background: '#1e293b',
                            color: '#fff'
                        });
                    });
                }
            });
        }
    </script>
    
    <script src="../Scripts/Dashboard.js"></script>
</body>
</html>