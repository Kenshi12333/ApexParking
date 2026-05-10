var fnameInput = document.getElementById("FName");

if(fnameInput){
   fnameInput.addEventListener("input", function(){
        validateNumFields(this);
   });
}

function validateNumFields(element){
    element.value = element.value.replace(/[^a-zA-Z0-9]/g, '');
}

function submitFunc(){
    var firstName = document.getElementById("FName").value;
    var lastName = document.getElementById("LName").value;
    var select = document.getElementById("roleSelect").value;
    var email = document.getElementById("Email").value;
    var password = document.getElementById("Password").value;

    if (!firstName || !lastName || !select || !email || !password) {
        Swal.fire({
            title: "Missing Information!",
            text: "Please fill out all the nessesary Information before adding a user.",
            icon: "warning",
            confirmButtonColor: "#3085d6"
        });
        return;
    }
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            title: "Invalid Email!",
            text: "Please enter a valid email address that includes a '@' (ex: user@example.com).",
            icon: "error",
            confirmButtonColor: "#d33"
        });
        return;
    }

    $.ajax({
        url: "../controllers/Controller.php", 
        type: "POST",
        data: {
            fName : firstName,
            lName : lastName,
            roleID: select,
            email : email,
            password : password
        },
        success : function(returnedData){
            Swal.fire({
                title: "Added User!",
                icon: "success",
                showConfirmButton: false,
                timer: 1000
            }).then((result) => {
                location.reload(true);
            });
        },
        error: function(xhr){
            alert(xhr.status + " : " + xhr.responseText);
        }
    });
}

function updateFunc(userID){
    var firstName = document.getElementById("FName").value;
    var lastName = document.getElementById("LName").value;
    var select = document.getElementById("roleSelect").value;
    var email = document.getElementById("Email").value;
    var password = document.getElementById("Password").value;

    if (!firstName || !lastName || !select || !email || !password) {
        Swal.fire({
            title: "Missing Information!",
            text: "Please fill out all fields (Name, Email, Password, and Role) to update.",
            icon: "warning",
            confirmButtonColor: "#3085d6"
        });
        return; 
    }
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            title: "Invalid Email!",
            text: "Please enter a valid email address that includes a '@' (ex: user@example.com).",
            icon: "error",
            confirmButtonColor: "#d33"
        });
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showDenyButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        denyButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url :"../controllers/Controller.php",
                type: "POST",
                data: {
                    fName : firstName,
                    lName : lastName,
                    uID : userID,
                    roleID : select,
                    email : email,
                    password : password
                },
                success: function(returnedData) {
                    Swal.fire({
                        title: "Saved",
                        text: "User is updated to database!",
                        icon: "success",
                        showConfirmButton : false,
                        timer: 1500
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    alert(xhr.status + " : " + xhr.responseText);
                }
            });
        } else if (result.isDenied) {
            Swal.fire({
                title: "Saved",
                text: "User is not updated to database!",
                icon: "error",
                showConfirmButton : false,
                timer: 1500
            });
        }
    });
}

function deleteFunc(userId){
    var firstName = document.getElementById("FName").value;
    var lastName = document.getElementById("LName").value;

    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showDenyButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        denyButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url :"../controllers/Controller.php",
                type: "POST",
                data: {
                    remover : userId
                },
                success: function(returnedData) {
                    Swal.fire({
                        title: "Saved",
                        text: "User is deleted to database!",
                        icon: "success",
                        showConfirmButton : false,
                        timer: 1500
                    }).then(() => location.reload());
                },
                error: function(xhr) {
                    alert(xhr.status + " : " + xhr.responseText);
                }
            });
        } else if (result.isDenied) {
            Swal.fire({
                title: "Saved",
                text: "User is not deleted to database!",
                icon: "error",
                showConfirmButton : false,
                timer: 1500
            });
        }
    });
}

function redirectFunc(redirectID){
    if(redirectID == 1){
        window.location.href="../views/LoginPage.php";
    }else if (redirectID == 2){
        window.location.href="../views/DashboardPage.php";
    }else if (redirectID == 3){
        window.location.href = "../views/registrationPage.php";
    }else if (redirectID == 4){
     
        window.location.href = "../views/terminalPOSPage.php";
    
     }else if (redirectID == 5){
     
        window.location.href = "../views/kioskPage.php";
    }
}

function loginFunc(){
    var LoginEmail = document.getElementById("icon_Email").value;
    var LoginPassword = document.getElementById("icon_Password").value;
    
    $.ajax({
        url :"../controllers/Controller.php",
        type: "POST",
        data: {
            lEmail : LoginEmail,
            lPassword : LoginPassword
        },
        success: function(returnedData) {
            if(returnedData) { 
                if(returnedData >= 1 && returnedData <= 3) { 
                    Swal.fire({
                        title: "Login Success!",
                        text: "Select your destination:",
                        icon: "success",
                        showCancelButton: true,
                        confirmButtonColor: "#1565c0",
                        cancelButtonColor: "#2e7d32",
                        confirmButtonText: "User Management",
                        cancelButtonText: "Go to Dashboard"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            redirectFunc(3); 
                        } else {
                            redirectFunc(2); 
                        }
                    });
                } 
                else if(returnedData == 4) { 
                    Swal.fire({
                        icon: "success",
                        title: "Login Success!",
                        text: "Opening POS Terminal",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => { 
                        redirectFunc(4); 
                    });
                }
                else if(returnedData == 5) { 
                    Swal.fire({
                        icon: "success",
                        title: "Login Success!",
                        text: "Opening Kiosk Terminal",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => { 
                        redirectFunc(5); 
                    });
                }
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Access Denied",
                    text: "Invalid email or password.",
                    timer: 2000
                });
            }
        },
        error: function(xhr) {
            alert(xhr.status + " : " + xhr.responseText);
        }
    });
}
$(document).ready(function(){
    $('select').formSelect();
  });
