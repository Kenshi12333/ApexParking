<?php 
session_start();
require_once "../bl/userManager.php";
require_once "../bl/rolesManager.php";

$usermanager = new userManager();
$users = $usermanager -> getUser();
$advancedUsers = $usermanager-> getAdvancedUser();

$rolesManager = new rolesManager();
$roles = $rolesManager ->getRoles();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Parking POS</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        
        body { 
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #e2e8f0;
        }
        
        .card-panel { 
            background: rgba(15, 23, 42, 0.65) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 16px; 
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5) !important;
        }
        
        .section-title { margin-top: 0; font-weight: 500; color: #60a5fa; letter-spacing: 1px; }
        
        table.highlight tbody tr:hover {
            background-color: rgba(255,255,255,0.05) !important;
        }
        
        
        td, th { color: #e2e8f0; }
        thead tr { background: rgba(0,0,0,0.3) !important; border-bottom: 1px solid #3b82f6; }
        .divider { background-color: rgba(255,255,255,0.1); }

        
        .input-field input, .select-wrapper input.select-dropdown {
            color: white !important;
            border-bottom: 1px solid rgba(255,255,255,0.3) !important;
        }
        .input-field label { color: #94a3b8 !important; }
        .input-field .prefix { color: #60a5fa !important; }
        .dropdown-content { background-color: #1e293b; }
        .dropdown-content li>a, .dropdown-content li>span { color: #60a5fa; }
    </style>
</head>
<body>

<nav class="blue darken-4 z-depth-0">
    <div class="nav-wrapper" style="padding: 0 30px;">
        <a href="DashboardPage.php" class="brand-logo" style="display: flex; align-items: center; cursor: default;">
            <img src="https://img.freepik.com/premium-vector/road-sign-parking-cars-parking-place-cars-parking-space-parking_546559-2873.jpg?semt=ais_hybrid&w=740&q=80" 
                 alt="Logo" 
                 style="height: 40px; border-radius: 4px; margin-right: 15px;">
            <span style="font-weight: 300; color: #ffffff;">Admin Panel</span>
        </a>
        
        <ul id="nav-mobile" class="right hide-on-med-and-down">
            <li><a href="terminalPOSPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                <i class="material-icons left">point_of_sale</i>POS Terminal
            </a></li>

            <li><a href="DashboardPage.php" class="waves-effect waves-light btn white blue-text text-darken-4 z-depth-0" style="border-radius: 8px; font-weight: bold; margin-right: 15px;">
                <i class="material-icons left">dashboard</i>Dashboard
            </a></li>

            <li><a href="LoginPage.php" class="waves-effect waves-light btn red darken-1 z-depth-0" style="border-radius: 8px; font-weight: bold;">
                <i class="material-icons left">exit_to_app</i>Sign Out
            </a></li>
        </ul>
    </div>
</nav>
    
    <br>
    <div class="container" style="width: 95%; max-width: 1600px; margin-top: 20px;">
        <div class="row">
            
            <div class="col s12 m4 l4">
                <div class="card-panel z-depth-2">
                    <h5 class="section-title"><i class="material-icons left" style="color: #60a5fa;">person_add</i> User Details</h5>
                    <div class="divider"></div><br>
                    
                    <div class="row">
                        <div class="input-field col s12 m6 l6">
                            <i class="material-icons prefix">badge</i>
                            <input id="FName" type="text" class="validate" maxlength="49">
                            <label for="FName">First Name</label>
                        </div>
                        <div class="input-field col s12 m6 l6">
                            <input id="LName" type="text" class="validate" maxlength="49">
                            <label for="LName">Last Name</label>
                        </div>

                        <div class="input-field col s12">
                            <i class="material-icons prefix">email</i>
                            <input id="Email" type="email" class="validate" maxlength="49">
                            <label for="Email">Email Address</label>
                        </div>

                        <div class="input-field col s12">
                            <i class="material-icons prefix">lock</i>
                            <input id="Password" type="password" class="validate" maxlength="49">
                            <label for="Password">Password</label>
                        </div>

                        <div class="input-field col s12">
                            <i class="material-icons prefix">assignment_ind</i>
                            <select id="roleSelect">
                                <option value="" disabled selected>Assign a Role</option>
                                <?php foreach($roles as $index => $role) : ?>
                                    <option value="<?= $role['userRoleID'] ?>"><?= $role['roleName'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label>System Role</label>
                        </div>

                        <div class="col s12 mt-2" style="margin-top: 20px;">
                            <a class="waves-effect waves-light btn-large green darken-1 z-depth-0" style="width: 100%; border-radius: 12px; font-weight: bold;" onclick="submitFunc()">
                                <i class="material-icons right">check_circle</i>Add New User
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12 m8 l8">
                <div class="card-panel z-depth-2">
                    <h5 class="section-title"><i class="material-icons left" style="color: #60a5fa;">people</i> Registered Users</h5>
                    <div class="divider"></div><br>
                    
                    <div style="overflow-x: auto;">
                        <table class="highlight centered responsive-table">
                            <thead>
                                <tr>
                                    <th style="border-radius: 5px 0 0 0;">#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th class="center-align" style="border-radius: 0 5px 0 0;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($advancedUsers)) : ?>
                                    <?php foreach($advancedUsers as $index => $user) : ?>
                                        <tr>
                                            <td><b><?= $index + 1 ?></b></td>
                                            <td><?= !empty($user['firstName']) ? htmlspecialchars($user['firstName']) : '<span class="grey-text"><i>N/A</i></span>' ?></td>
                                            <td><?= !empty($user['lastName']) ? htmlspecialchars($user['lastName']) : '<span class="grey-text"><i>N/A</i></span>' ?></td>
                                            <td><span class="new badge blue darken-1" data-badge-caption=""><?= htmlspecialchars($user['roleName']) ?></span></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td class="center-align">
                                                <a class="waves-effect waves-light btn-small light-blue darken-1 tooltipped z-depth-0" data-position="top" data-tooltip="Update User" onclick="updateFunc(<?= $user['userID'] ?>)" style="margin:3px; border-radius: 8px;">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <a class="waves-effect waves-light btn-small red darken-2 tooltipped z-depth-0" data-position="top" data-tooltip="Delete User" onclick="deleteFunc(<?= $user['userID'] ?>)" style="margin:3px; border-radius: 8px;">
                                                    <i class="material-icons">delete</i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6" class="center-align grey-text" style="padding: 30px;">
                                            <i class="material-icons large grey-text text-lighten-2">person_off</i><br>
                                            No users registered yet.
                                        </td>
                                    </tr>
                                <?php endif ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>   
    </div>

    <footer class="blue darken-4" style="margin-top: 50px; padding: 20px 0; text-align: center;">
        <div style="font-weight: 600; font-size: 1.1rem; color: #ffffff; letter-spacing: 1px;">Apex Parking Systems</div>
        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.8); margin-top: 5px;">Property of JAGSolutions | All rights reserved&reg;</div>
    </footer>
            
<script src="../Scripts/service.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
        
        var tooltips = document.querySelectorAll('.tooltipped');
        M.Tooltip.init(tooltips);
    });
</script>
</body>
</html>