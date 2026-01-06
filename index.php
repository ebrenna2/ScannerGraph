<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Scan Tracker</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        canvas {
            max-height: 80vh;
        }
    </style>
</head>
<body>

<h2>Weekly Scan Count</h2>
<canvas id="scanChart"></canvas>

<script>
let chart;

async function loadData() {
    const response = await fetch("data.php");
    const data = await response.json();

    const labels = data.map(d => d.week);
    const values = data.map(d => d.count);

    const avg =
        values.reduce((a, b) => a + b, 0) / values.length;

    if (chart) {
        chart.destroy();
    }

    chart = new Chart(document.getElementById("scanChart"), {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Total Document Count",
                    data: values,
                    backgroundColor: [
                        "#66c2a5", "#fc8d62", "#8da0cb", "#e78ac3"
                    ],
                    borderColor: "black",
                    borderWidth: 1
                },
                {
                    label: "Average",
                    type: "line",
                    data: Array(values.length).fill(avg),
                    borderColor: "red",
                    borderDash: [5, 5],
                    borderWidth: 2,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Total Document Count"
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: "Week Starting"
                    }
                }
            }
        }
    });
}

// Initial load
loadData();

// Auto refresh every 5 minutes (same as Dash Interval)
setInterval(loadData, 5 * 60 * 1000);
</script>

</body>
</html>
