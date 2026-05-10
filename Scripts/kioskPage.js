let currentKioskTransactionID = 0;
let currentKioskTotal = 0;

function findVehicle() {
    let plate = document.getElementById("kioskPlateInput").value.trim().toUpperCase();

    if(!plate) {
        Swal.fire({ title: "Input Required", text: "Please enter your plate number.", icon: "info", heightAuto: false });
        return;
    }

    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { 
            action: "getBillByPlate", 
            plateNumber: plate 
        },
        success: function(response) {
            let parts = response.trim().split('|');
            
            if(parts[0] === "success") {
                currentKioskTransactionID = parts[1];
                let plateNumber = parts[2];
                let vehicleType = parts[3];
                let time_in = parts[4];
                currentKioskTotal = parseFloat(parts[5]);

                document.getElementById("displayTotal").textContent = "₱ " + currentKioskTotal.toFixed(2);
                document.getElementById("displayInfo").textContent = `${vehicleType} | In: ${time_in}`;
                
                $("#searchSection").hide();
                $("#paymentDetails").fadeIn();
            } else {
                Swal.fire({ title: "Not Found", text: "No active parking session found for this plate.", icon: "error", heightAuto: false });
            }
        },
        error: function() {
            Swal.fire({ title: "System Error", text: "Could not retrieve bill.", icon: "error", heightAuto: false });
        }
    });
}

function payAtKiosk() {
    let email = document.getElementById("kioskEmailInput").value.trim();
    let plateNumber = document.getElementById("kioskPlateInput").value.trim().toUpperCase();
    
    if(!email || !email.includes('@')) {
        Swal.fire({ title: "Email Required", text: "Please enter a valid email address.", icon: "warning", heightAuto: false });
        return;
    }

    Swal.fire({
        title: "Processing...",
        text: "Finalizing payment and sending your receipt.",
        allowOutsideClick: false,
        heightAuto: false, 
        didOpen: () => { Swal.showLoading(); }
    });


    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { 
            action: "processKioskPayment", 
            transactionID: currentKioskTransactionID,
            totalAmount: currentKioskTotal,
            email: email,
            plateNumber: plateNumber
        },
        success: function(response) {
            if(response.trim() === "success") {
                Swal.fire({
                    html: `
                    <div style="padding: 10px; font-family: 'Segoe UI', sans-serif;">
                        <div style="background: linear-gradient(135deg, #22c55e, #16a34a); color: white; width: 85px; height: 85px; border-radius: 50%; line-height: 85px; font-size: 45px; margin: 0 auto 20px auto; box-shadow: 0 10px 20px rgba(34, 197, 94, 0.3);">✓</div>
                        <h2 style="color: #0f172a; margin: 0 0 5px 0; font-weight: 900; font-size: 3rem;">₱${parseFloat(currentKioskTotal).toFixed(2)}</h2>
                        <p style="color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 25px; font-size: 0.9rem;">Payment Successful</p>
                        
                        <div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-bottom: 25px; text-align: left; border: 1px solid #e2e8f0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #64748b; font-size: 0.95rem;">Plate Number</span>
                                <span style="color: #0f172a; font-weight: 800; font-size: 0.95rem;">${plateNumber}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b; font-size: 0.95rem;">E-Receipt</span>
                                <span style="color: #0d47a1; font-weight: 700; font-size: 0.95rem;">Sent to Email</span>
                            </div>
                        </div>
                        
                        <div style="background: rgba(13, 71, 161, 0.05); border-left: 5px solid #0d47a1; border-radius: 0 8px 8px 0; padding: 15px 20px; text-align: left;">
                            <p style="margin: 0; color: #0d47a1; font-weight: 800; font-size: 1.1rem; display: flex; align-items: center;">
                                <i class="material-icons" style="margin-right: 8px; font-size: 1.3rem;">door_sliding</i> Please proceed at the exit gate.
                            </p>
                            <p style="margin: 5px 0 0 28px; font-size: 0.9rem; color: #475569; font-weight: 500;">Please show the receipt to our parking attendant. Drive safely!</p>
                        </div>
                    </div>`,
                    showConfirmButton: true,
                    confirmButtonText: "DONE",
                    confirmButtonColor: '#0d47a1',
                    width: '450px',
                    padding: '2em',
                    background: '#ffffff',
                    allowOutsideClick: false,
                    heightAuto: false
                }).then(() => {
                    resetKiosk();
                });
            } else {
                Swal.fire({ title: "Payment Error", text: response, icon: "error", heightAuto: false });
            }
        },
        error: function() {
            Swal.fire({ title: "Network Error", text: "Please see the attendant.", icon: "error", heightAuto: false });
        }
    });
}

function resetKiosk() {
    $("#paymentDetails").hide();
    $("#searchSection").show();
    document.getElementById("kioskPlateInput").value = "";
    document.getElementById("kioskEmailInput").value = "";
    currentKioskTransactionID = 0;
    currentKioskTotal = 0;
}

document.getElementById("kioskPlateInput").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        findVehicle();
    }
});

function promptKioskExit() {
    Swal.fire({
        title: 'Exit Kiosk Mode',
        heightAuto: false, 
        html: `
            <div style="margin-bottom: 15px;">
                <p style="color: #424242; font-size: 0.9rem; margin-top: 0;">Enter authorized credentials to close Kiosk.</p>
                <input type="email" id="exitEmail" class="browser-default" placeholder="Email Address" style="width: 100%; padding: 10px; margin-bottom: 10px; background: #fff; border: 1px solid #90caf9; color: #0d47a1; border-radius: 5px; outline: none;">
                <input type="password" id="exitPass" class="browser-default" placeholder="Password" style="width: 100%; padding: 10px; background: #fff; border: 1px solid #90caf9; color: #0d47a1; border-radius: 5px; outline: none;">
            </div>
        `,
        background: '#ffffff',
        color: '#0d47a1',
        showCancelButton: true,
        confirmButtonColor: '#0d47a1',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Unlock',
        preConfirm: () => {
            const email = document.getElementById('exitEmail').value;
            const pass = document.getElementById('exitPass').value;
            if (!email || !pass) {
                Swal.showValidationMessage('Please enter both email and password');
            }
            return { email: email, pass: pass };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "../controllers/Controller.php",
                type: "POST",
                data: {
                    action: "kioskExitAuth",
                    email: result.value.email,
                    password: result.value.pass
                },
                success: function(response) {
                    let role = response.toString().trim();
                    if(["1", "2", "3", "5"].includes(role)) {
                        Swal.fire({
                            title: "Access Approved", 
                            text: "Logging out dummy account...", 
                            icon: "success", 
                            heightAuto: false, 
                            showConfirmButton: false, 
                            allowOutsideClick: false, 
                            allowEscapeKey: false,
                            timer: 3000,
                            background: '#ffffff', 
                            color: '#0d47a1'
                        }).then(() => {
                            window.location.href = "logout.php";
                        });

                    } else {
                        Swal.fire({
                            title: "Access Denied", 
                            text: "Only authorized staff can unlock the Kiosk.", 
                            icon: "error", 
                            heightAuto: false, 
                            background: '#ffffff', 
                            color: '#0d47a1'
                        });
                    }
                },
                error: function(xhr) {
                    alert(xhr.status + " : " + xhr.responseText);
                }
            });
        }
    });
}