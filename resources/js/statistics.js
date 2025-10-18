import Chart from 'chart.js/auto';


// --- Fake data ---
const days = ["Day 1", "Day 2", "Day 3", "Day 4", "Day 5", "Day 6", "Day 7"];
const sittingTimes = [90, 110, 80, 100, 120, 70, 70];
const standingTimes = [30, 40, 20, 30, 50, 30, 20];
const totalTimes = sittingTimes.map((sit, i) => sit + standingTimes[i]);

// --- Bar Chart (Time per Day) ---
const barCtx = document.getElementById('activityChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: days,
        datasets: [
            {
                label: 'Total Time',
                data: totalTimes,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Sitting',
                data: sittingTimes,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },
            {
                label: 'Standing',
                data: standingTimes,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'Daily Activity Time (minutes)' }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Minutes' }
            }
        }
    }
});

// --- Pie Chart (Overall Proportions) ---
const pieCtx = document.getElementById('summaryPie').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Sitting', 'Standing'],
        datasets: [{
            data: [640, 310], // Fake totals
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(75, 192, 192, 0.7)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: { display: true, text: 'All-Time Sitting vs Standing' }
        }
    }
});