import Chart from 'chart.js/auto';

// Get data from the database
const day = healthCycle ? new Date(healthCycle.completed_at).toLocaleDateString() : 'Today';
const sittingTime = healthCycle ? healthCycle.sitting_minutes : 0;
const standingTime = healthCycle ? healthCycle.standing_minutes : 0;
const totalTime = sittingTime + standingTime;

// Bar Chart (Time per Day)
const barCtx = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: [day],
        datasets: [
            {
                label: 'Total Time',
                data: [totalTime],
                backgroundColor: '#6C4AB6'
            },
            {
                label: 'Sitting Time',
                data: [sittingTime],
                backgroundColor: '#B9E0FF'
            },
            {
                label: 'Standing Time',
                data: [standingTime],
                backgroundColor: '#8D9EFF'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Daily Activity Time (in minutes)'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Minutes'
                }
            }
        }
    }
});

// Button For Toggling Pie Chart
const togglePiebtn = document.getElementById("togglePieChart");
const allTime = document.getElementById("allTimeChart");
let pieInited = false;
let pieChart;

togglePiebtn.addEventListener("click", () => {
    const expanded = allTime.classList.toggle("expanded");
    togglePiebtn.setAttribute("aria-expanded", expanded);
    togglePiebtn.textContent = expanded
    ? "Hide All-Time Statistics"
    : "Show All-Time Statistics";

    if(expanded && !pieInited) {
        initPieChart();
        pieInited = true;
    }
});

function initPieChart() {
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: ['Sitting', 'Standing'],
            datasets: [{
                data: [totalSitting, totalStanding],
                backgroundColor: [
                    '#6C4AB6',
                    '#8D9EFF'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'All-Time Sitting vs Standing'
                }
            }
        }
    });
}
