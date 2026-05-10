<?php 
session_start();
require_once "../bl/userManager.php";
$usermanager = new userManager();
$users = $usermanager -> getUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking POS - Login</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>
    
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            background-color: #1a252f;
        }

        #vanta-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .login-box {
            position: relative;
            z-index: 1;
        }

        .card-panel {
            background: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(20px);
            border-radius: 20px !important;
            padding: 45px !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5) !important;
        }

        .brand-logo-text {
            font-weight: 900;
            color: #0d47a1; 
            margin-bottom: 30px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
     
        .modal-footer {
            display: flex;
            justify-content: center;
            background-color: transparent !important;
        }
    </style>
</head>
<body>

<div id="vanta-canvas"></div>

<div class="container login-box">
    <div class="row">
        <div class="col s12 m8 offset-m2 l6 offset-l3">
            <div class="card-panel z-depth-4">
                
                <h4 class="center-align brand-logo-text">
                    <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" 
                         alt="Parking Sign" 
                         style="height: 75px; vertical-align: middle; margin-bottom: 5px; border-radius: 8px;">
                    <br>
                    Apex Parking Systems
                </h4>
                <p class="center-align grey-text text-darken-2" style="font-weight: 500;"></p>
                <br>

                <form onsubmit="event.preventDefault(); loginFunc();">
                    <div class="row margin">
                        <div class="input-field col s12">
                            <i class="material-icons prefix blue-text text-darken-3">email</i>
                            <input id="icon_Email" type="email" class="validate" required maxlength="49">
                            <label for="icon_Email">Email Address</label>
                        </div>
                    </div>
                    
                    <div class="row margin">
                        <div class="input-field col s12">
                            <i class="material-icons prefix blue-text text-darken-3">lock</i>
                            <input id="icon_Password" type="password" class="validate" required maxlength="49">
                            <label for="icon_Password">Password</label>
                        </div>
                    </div>
                    
                    <div class="row" style="margin-top: 20px; margin-bottom: 10px;">
                        <div class="input-field col s12">
                            <button type="submit" class="waves-effect waves-light btn-large blue darken-4 z-depth-2" style="width: 100%; border-radius: 10px; font-weight: bold; display: flex; justify-content: center; align-items: center; gap: 8px;">
                                <i class="material-icons" style="margin: 0;">login</i> Sign In
                            </button>
                        </div>
                    </div>
                    
                    <div class="center-align" style="margin-top: 20px;">
                        <span class="grey-text text-darken-2" style="font-weight: 500;">Experiencing Problems? </span>
                        <a href="#contactModal" class="modal-trigger blue-text text-darken-4" style="font-weight: bold; text-decoration: underline;">Contact Us</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<div id="contactModal" class="modal" style="border-radius: 15px; max-width: 450px; max-height: 90%; top: 10% !important;">
    <div class="modal-content center-align" style="padding: 40px 30px 30px 30px;">
        <i class="material-icons large blue-text text-darken-4" style="margin-bottom: 15px;">admin_panel_settings</i>
        <h5 style="font-weight: 900; color: #0d47a1; margin-top: 0;">Experiencing Problems?</h5>
        
        <p class="grey-text text-darken-2" style="font-size: 1.05rem; margin-top: 20px; line-height: 1.6;">
            If you have forgotten your credentials or are locked out of the system, please contact your nearest <b>Administrator</b> or <b>Manager</b> to request access or reset your account.
        </p>

        <div style="margin: 25px 0; padding: 15px; background: rgba(13, 71, 161, 0.05); border-radius: 8px; border: 1px solid rgba(13, 71, 161, 0.1);">
            <p style="margin: 0; font-weight: bold; color: #0d47a1; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">Or Contact Us Directly</p>
            <p style="margin: 8px 0 0 0; color: #424242; font-weight: 500;">
                <i class="material-icons tiny blue-text text-darken-4" style="vertical-align: -2px; margin-right: 5px;">phone</i> 0901-898-9988
            </p>
            <p style="margin: 5px 0 0 0; color: #424242; font-weight: 500;">
                <i class="material-icons tiny blue-text text-darken-4" style="vertical-align: -2px; margin-right: 5px;">email</i> JAGSolutions@mail.com
            </p>
        </div>
        
        <a href="#!" class="modal-close waves-effect waves-light btn-large blue darken-4" style="border-radius: 8px; font-weight: bold; width: 80%; box-shadow: 0 4px 6px rgba(0,0,0,0.2); margin-top: 10px;">Understood</a>
    </div>
</div>

<script src="../Scripts/service.js"></script>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        var modals = document.querySelectorAll('.modal');
        M.Modal.init(modals, {
            dismissible: true,   
            preventScrolling: false 
        });
    });

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
      shininess: 35.00,
      waveHeight: 20.00,
      waveSpeed: 0.80,
      zoom: 0.90
    })
</script>
</body>
</html>