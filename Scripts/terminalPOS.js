let currentCheckoutData = {
    transactionID: 0,
    baseRate: 0,
    discountAmount: 0,
    penaltyAmount: 0,
    discountID: 0,
    penaltyID: 0
};

document.addEventListener('DOMContentLoaded', function() {
    M.FormSelect.init(document.querySelectorAll('select'));
    M.Modal.init(document.getElementById('checkoutModal'), {
        dismissible: true,
        preventScrolling: false
    });
});

function selectSlot(slotName) {
    document.getElementById('slotID').value = slotName;
    M.FormSelect.init(document.querySelectorAll('select'));
}

function processEntry() {
    let plate = document.getElementById("plateNum").value.trim().toUpperCase();
    let typeID = document.getElementById("vehicleType").value;
    let slotID = document.getElementById("slotID").value;

    if(!plate || !typeID || !slotID) {
        Swal.fire({ title: "Incomplete Data", text: "Please provide all required information.", icon: "warning", background: '#1e293b', color: '#fff' });
        return;
    }

   
    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { 
            plateNumber: plate, 
            vehicleTypeID: typeID, 
            slotID: slotID 
        },
        success: function(response) {
            Swal.fire({ title: "Vehicle Logged!", icon: "success", timer: 1000, showConfirmButton: false, background: '#1e293b', color: '#fff' })
            .then(() => location.reload());
        },
        error: function() {
            Swal.fire({ title: "Error", text: "Could not log entry.", icon: "error", background: '#1e293b', color: '#fff' });
        }
    });
}

function openCheckoutModal(transactionID, plateNumber, vehicleType, baseRate) {
    currentCheckoutData.transactionID = transactionID;
    currentCheckoutData.baseRate = parseFloat(baseRate);
    
    document.getElementById('plateNumberDisplay').textContent = plateNumber;
    document.getElementById('vehicleTypeDisplay').textContent = vehicleType;
    document.getElementById('rateDisplay').textContent = currentCheckoutData.baseRate.toFixed(2);
    
    let now = new Date();
    document.getElementById('checkInTimeDisplay').textContent = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    document.getElementById('discountSelect').value = "0";
    document.getElementById('penaltySelect').value = "0";
    document.getElementById('cashTenderedInput').value = "";
    document.getElementById('changeDisplay').textContent = "₱ 0.00";
    
    M.FormSelect.init(document.querySelectorAll('select'));
    
    calculateTotal();
    
    let instance = M.Modal.getInstance(document.getElementById('checkoutModal'));
    instance.open();
}

function calculateTotal() {
    let discVal = document.getElementById('discountSelect').value;
    let penVal = document.getElementById('penaltySelect').value;
    
    let discount = discVal !== "0" ? parseFloat(discVal.split('|')[1]) : 0;
    let penalty = penVal !== "0" ? parseFloat(penVal.split('|')[1]) : 0;
    
    currentCheckoutData.discountID = discVal !== "0" ? discVal.split('|')[0] : 0;
    currentCheckoutData.penaltyID = penVal !== "0" ? penVal.split('|')[0] : 0;
    
    let total = currentCheckoutData.baseRate - discount + penalty;
    if(total < 0) total = 0;
    
    document.getElementById('totalDueDisplay').textContent = "₱ " + total.toFixed(2);
    calculateChange();
}

function calculateChange() {
    let totalText = document.getElementById('totalDueDisplay').textContent.replace("₱ ", "");
    let total = parseFloat(totalText);
    let cash = parseFloat(document.getElementById('cashTenderedInput').value) || 0;
    
    let change = cash - total;
    if(change >= 0 && cash > 0) {
        document.getElementById('changeDisplay').textContent = "₱ " + change.toFixed(2);
        document.getElementById('changeDisplay').style.color = "#22c55e";
    } else {
        document.getElementById('changeDisplay').textContent = "₱ 0.00";
        document.getElementById('changeDisplay').style.color = "#60a5fa";
    }
}

function confirmPayment() {
    let totalText = document.getElementById('totalDueDisplay').textContent.replace("₱ ", "");
    let total = parseFloat(totalText);
    let cash = parseFloat(document.getElementById('cashTenderedInput').value) || 0;

    if(cash < total) {
        Swal.fire({ title: "Insufficient Cash", text: "Cash tendered is less than total due.", icon: "warning", background: '#1e293b', color: '#fff' });
        return;
    }


    $.ajax({
        url: "../controllers/Controller.php",
        type: "POST",
        data: { 
            checkoutID: currentCheckoutData.transactionID, 
            totalAmount: currentCheckoutData.baseRate,
            discountID: currentCheckoutData.discountID,
            penaltyID: currentCheckoutData.penaltyID
        },
        success: function(response) {
            Swal.fire({ title: "Payment Processed!", icon: "success", timer: 1500, showConfirmButton: false, background: '#1e293b', color: '#fff' })
            .then(() => location.reload());
        },
        error: function() {
            Swal.fire({ title: "Error", text: "Failed to process payment.", icon: "error", background: '#1e293b', color: '#fff' });
        }
    });
}

function finalExit(transactionID, paidAmount) {
    Swal.fire({
        title: 'Open Gate?',
        text: "Vehicle has already paid ₱" + parseFloat(paidAmount).toFixed(2) + " at the kiosk.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Confirm Exit & Open Gate',
        background: '#1e293b',
        color: '#fff'
    }).then((result) => {
        if (result.isConfirmed) {
           
            $.ajax({
                url: "../controllers/Controller.php",
                type: "POST",
                data: { 
                    checkoutID: transactionID, 
                    totalAmount: paidAmount,
                    discountID: 0,
                    penaltyID: 0
                },
                success: function(response) {
                    Swal.fire({ title: "Gate Opened!", icon: "success", timer: 1000, showConfirmButton: false, background: '#1e293b', color: '#fff' })
                    .then(() => location.reload());
                },
                error: function() {
                    Swal.fire({ title: "Error", text: "Failed to process exit.", icon: "error", background: '#1e293b', color: '#fff' });
                }
            });
        }
    });
}