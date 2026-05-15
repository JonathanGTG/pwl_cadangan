// public/js/manager-dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('revenueChart');
    if (!chartCanvas) return; // Mencegah error jika canvas tidak ada

    const ctx = chartCanvas.getContext('2d');
    
    // Create Gradients
    const gradientMocha = ctx.createLinearGradient(0, 0, 0, 400);
    gradientMocha.addColorStop(0, 'rgba(93, 64, 55, 0.4)');
    gradientMocha.addColorStop(1, 'rgba(93, 64, 55, 0.0)');

    const gradientEmerald = ctx.createLinearGradient(0, 0, 0, 400);
    gradientEmerald.addColorStop(0, 'rgba(52, 211, 153, 0.4)');
    gradientEmerald.addColorStop(1, 'rgba(52, 211, 153, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['23 Sep', '24 Sep', '25 Sep', '26 Sep', '27 Sep', '28 Sep', '29 Sep', '30 Sep', '01 Okt'],
            datasets: [
                {
                    label: 'Pendapatan',
                    data: [20, 35, 30, 80, 50, 75, 60, 45, 25],
                    borderColor: '#5D4037',
                    backgroundColor: gradientMocha,
                    borderWidth: 3,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#5D4037',
                    pointBorderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Transaksi',
                    data: [10, 25, 45, 40, 70, 45, 30, 35, 15],
                    borderColor: '#34D399',
                    backgroundColor: gradientEmerald,
                    borderWidth: 3,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#34D399',
                    pointBorderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#3E2723',
                    titleFont: { family: 'Inter', size: 13 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    intersect: false,
                    mode: 'index',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F3F4F6', drawBorder: false },
                    ticks: {
                        color: '#9CA3AF',
                        font: { family: 'Inter', size: 11 },
                        callback: function(value) { return value + '%'; }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: '#9CA3AF',
                        font: { family: 'Inter', size: 11 }
                    }
                }
            },
            interaction: { mode: 'index', intersect: false },
        }
    });
});
