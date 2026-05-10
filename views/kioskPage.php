<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Self-Pay Kiosk | Apex Parking</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>
    
    <style>
        body { 
            margin: 0;
            padding: 0;
            display: flex; align-items: center; justify-content: center; 
            height: 100vh; overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            background-color: #0d47a1; /* Fallback Blue */
        }

        /* --- VANTA BACKGROUND --- */
        #vanta-canvas {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
        }

        /* --- LIGHT GLASSMORPHISM CARD --- */
        .kiosk-card {
            width: 100%; max-width: 600px; 
            background: rgba(255, 255, 255, 0.90) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px !important; 
            padding: 45px !important;
            border: 1px solid rgba(255, 255, 255, 0.5); 
            box-shadow: 0 25px 50px rgba(0,0,0,0.3) !important;
            position: relative;
            z-index: 10;
        }

        .kiosk-header { text-align: center; margin-bottom: 40px; }
        .kiosk-header h2 { font-weight: 900; color: #0d47a1; letter-spacing: 2px; margin: 0; text-transform: uppercase;}
        .kiosk-header p { color: #424242; font-weight: 500; margin-top: 10px; }
        
        /* Large Touch-Friendly Plate Input */
        .plate-input-container { margin-bottom: 30px; }
        #kioskPlateInput {
            font-size: 3.5rem !important; text-align: center; text-transform: uppercase;
            font-weight: 900; color: #0d47a1 !important; border: 2px solid #90caf9 !important;
            border-radius: 12px; height: 100px !important; background: rgba(255, 255, 255, 0.8) !important;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        }
        #kioskPlateInput::placeholder { color: #b0bec5; font-weight: 700; }
        #kioskPlateInput:focus { 
            border-color: #0d47a1 !important; 
            box-shadow: 0 0 20px rgba(13, 71, 161, 0.2) !important; 
            background: #fff !important;
        }

        /* Payment Details Area */
        #paymentDetails { display: none; background: rgba(13, 71, 161, 0.05); border-radius: 16px; padding: 25px; margin-top: 20px; border: 1px solid rgba(13, 71, 161, 0.1); animation: slideUp 0.4s ease; }
        .fee-label { color: #616161; font-size: 1.1rem; text-transform: uppercase; font-weight: bold; }
        .fee-amount { font-size: 4rem; font-weight: 900; color: #2e7d32; margin: 10px 0; }
        #displayInfo { color: #424242; font-weight: 600; font-size: 1.1rem;}
        
        /* Email Input */
        #kioskEmailInput {
            color: #0d47a1; text-align: center; font-size: 1.2rem; font-weight: 500;
            border-bottom: 2px solid #0d47a1 !important; width: 80%; 
            background: transparent; border-top: none; border-left: none; border-right: none; outline: none;
        }
        #kioskEmailInput::placeholder { color: #9e9e9e; }
        #kioskEmailInput:focus { border-bottom: 2px solid #1976d2 !important; box-shadow: 0 1px 0 0 #1976d2 !important; }

        .btn-kiosk { width: 100%; height: 70px; border-radius: 12px; font-size: 1.5rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; transition: transform 0.2s ease;}
        .btn-kiosk:active { transform: scale(0.98); }
        
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div id="vanta-canvas"></div>

    <div class="kiosk-card">
        <div class="kiosk-header">
            <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" 
                 alt="Parking Sign" 
                 style="height: 60px; vertical-align: middle; margin-bottom: 10px; border-radius: 8px;">
            <h2>APEX KIOSK</h2>
            <p>Enter plate number to settle your balance</p>
        </div>

        <div id="searchSection">
            <div class="plate-input-container">
                <input type="text" id="kioskPlateInput" placeholder="ABC 1234" maxlength="8">
            </div>
            <button class="btn btn-kiosk blue darken-4 waves-effect z-depth-2" onclick="findVehicle()">
                CHECK MY BILL
            </button>
        </div>

        <div id="paymentDetails">
            <div class="center-align">
                <div class="fee-label">Total Amount Due</div>
                <div class="fee-amount" id="displayTotal">₱ 0.00</div>
                <p id="displayInfo"></p>
                
                <div style="margin-top: 25px; margin-bottom: 25px;">
                    <input type="email" id="kioskEmailInput" placeholder="Enter email for receipt & exit pass" required>
                </div>
                
                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button class="btn btn-kiosk grey darken-1 waves-effect" style="flex: 1;" onclick="resetKiosk()">CANCEL</button>
                    <button class="btn btn-kiosk green darken-2 waves-effect z-depth-2" style="flex: 2;" onclick="payAtKiosk()">
                        PAY & GET PASS
                    </button>
                </div>
            </div>
        </div>
    </div>

   <footer style="position: fixed; bottom: 0; width: 100%; padding: 15px; text-align: center; color: #90caf9; background: rgba(13, 71, 161, 0.9); z-index: 20;">
        <p style="margin: 0; font-size: 0.85rem; color: #ffffff;">Property of JAGSolutions | All rights reserved&reg; 2026</p>
        
        <a style="position: absolute; bottom: 12px; right: 20px; color: #90caf9; cursor: pointer;" onclick="promptKioskExit()">
            <i class="material-icons">lock</i>
        </a>
    </footer>

    <script src="../Scripts/kioskPage.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            VANTA.WAVES({
                el: "#vanta-canvas",
                mouseControls: true,
                touchControls: true,
                gyroControls: false,
                minHeight: 200.00,
                minWidth: 200.00,
                scale: 1.00,
                scaleMobile: 1.00,
                color: 0x0d47a1, 
                shininess: 45.00,
                waveHeight: 15.00,
                waveSpeed: 0.80,
                zoom: 0.85
            });
        });
    </script>
</body>
</html>