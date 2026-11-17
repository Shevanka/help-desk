import Chart from 'chart.js/auto';

/**
 * Renders every <canvas data-chart='{"type":…,"labels":[…],"datasets":[…]}'>.
 * Config is produced server-side by the <x-chart> Blade component.
 */
Chart.defaults.font.family = "Figtree, ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#64748b';
Chart.defaults.borderColor = '#e2e8f0';
Chart.defaults.plugins.legend.labels.usePointStyle = true;
Chart.defaults.plugins.legend.labels.boxWidth = 8;
Chart.defaults.maintainAspectRatio = false;

const integerTicks = { precision: 0, beginAtZero: true };

document.querySelectorAll('canvas[data-chart]').forEach((canvas) => {
    const cfg = JSON.parse(canvas.dataset.chart);
    const circular = cfg.type === 'doughnut' || cfg.type === 'pie';
    const horizontal = cfg.indexAxis === 'y';

    new Chart(canvas, {
        type: cfg.type,
        data: { labels: cfg.labels, datasets: cfg.datasets },
        options: {
            indexAxis: cfg.indexAxis ?? 'x',
            cutout: cfg.type === 'doughnut' ? '65%' : undefined,
            interaction: { mode: circular ? 'nearest' : 'index', intersect: circular },
            plugins: {
                legend: { display: cfg.legend ?? circular, position: circular ? 'right' : 'bottom' },
            },
            scales: circular ? {} : {
                x: horizontal ? { ticks: integerTicks, beginAtZero: true } : { grid: { display: false } },
                y: horizontal ? { grid: { display: false } } : { ticks: integerTicks, beginAtZero: true },
            },
        },
    });
});
