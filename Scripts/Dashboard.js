document.addEventListener("DOMContentLoaded", function() {
    
    
    var barChart = document.getElementById("myChart");
    if(barChart) {
        new Chart(barChart, {
            type: 'bar',
            data: {
                labels: window.barData.label,
                datasets: [{
                    label: "Count of registered users",
                    data: window.barData.data,
                    borderWidth: 1,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgb(54, 162, 235)',
                        'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: '#94a3b8', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    }
                },
                plugins: {
                    legend: { labels: { color: '#e2e8f0' } }
                }
            }
        });
    }

 
    var parkingCanvas = document.getElementById("parkingChart");
    if(parkingCanvas) {
        let delayed; 

        new Chart(parkingCanvas, {
            type: 'bar',
            data: {
                labels: window.parkingData.label,
                datasets: [{
                    label: "Live Slots",
                    data: window.parkingData.data,
                    borderWidth: 1,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.4)', 
                        'rgba(239, 68, 68, 0.4)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    onComplete: () => {
                        delayed = true;
                    },
                    delay: (context) => {
                        let delay = 0;
                        if (context.type === 'data' && context.mode === 'default' && !delayed) {
                            delay = context.dataIndex * 300 + context.datasetIndex * 100;
                        }
                        return delay;
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: '#94a3b8', stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { labels: { color: '#e2e8f0' } }
                }
            }
        });
    }

 
    let circularProgress = document.querySelector(".circular-progress");
    let progressValue = document.querySelector(".progress-value");
    
    if (circularProgress && progressValue) {
        let progressStartValue = 0;
        let progressEndValue = window.occupancyData.percentage; 
        let speed = 20; 

        if (progressEndValue > 0) {
            let progress = setInterval(() => {
                progressStartValue++;
                
                progressValue.textContent = `${progressStartValue}%`;
                
                circularProgress.style.background = `conic-gradient(#3b82f6 ${progressStartValue * 3.6}deg, rgba(255, 255, 255, 0.1) 0deg)`;

                if(progressStartValue == progressEndValue){
                    clearInterval(progress);
                }
            }, speed);
        } else {
          
            progressValue.textContent = `0%`;
            circularProgress.style.background = `conic-gradient(rgba(255, 255, 255, 0.1) 360deg, rgba(255, 255, 255, 0.1) 0deg)`;
        }
    }
});


function sendOperationsSummary() {
    Swal.fire({
        title: "Generating Report...",
        text: "Please wait while we compile the daily operations data.",
        background: '#1e293b',
        color: '#fff',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    $.post("../controllers/Controller.php", { 
        action: "summarizeOperations" 
    }, function(response) {
        if(response.trim() === "success") {
            Swal.fire({
                title: "Report Sent!",
                text: "The operations summary has been emailed to the manager.",
                icon: "success",
                background: '#1e293b',
                color: '#fff'
            });
        } else {
            Swal.fire({
                title: "Error",
                text: response,
                icon: "error",
                background: '#1e293b',
                color: '#fff'
            });
        }
    });
}