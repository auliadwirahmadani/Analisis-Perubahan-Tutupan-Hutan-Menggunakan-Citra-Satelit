/**
 * ForestWatch ApexCharts Visualizations
 */

let trendChartInstance = null;
let lulcChartInstance = null;
let comparisonChartInstance = null;

function renderDashboardCharts(trendData, lulcData, comparisonData) {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#475569';
    const gridColor = isDark ? '#1e293b' : '#e2e8f0';

    // 1. Yearly Forest Trend Chart (Area Chart)
    const trendEl = document.querySelector("#yearlyTrendChart");
    if (trendEl && trendData && trendData.length > 0) {
        const years = trendData.map(d => d.year);
        const primary = trendData.map(d => parseFloat(d.primary_forest));
        const secondary = trendData.map(d => parseFloat(d.secondary_forest));
        const deforestation = trendData.map(d => parseFloat(d.deforestation_rate));
        const reforestation = trendData.map(d => parseFloat(d.reforestation));

        const trendOptions = {
            series: [
                { name: 'Hutan Primer (Ha)', data: primary },
                { name: 'Hutan Sekunder (Ha)', data: secondary },
                { name: 'Laju Deforestasi (Ha)', data: deforestation },
                { name: 'Reforestasi & Pemulihan (Ha)', data: reforestation }
            ],
            chart: {
                type: 'area',
                height: 340,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#059669', '#10b981', '#ef4444', '#3b82f6'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            xaxis: {
                categories: years,
                labels: { style: { colors: textColor } },
                axisBorder: { color: gridColor },
                axisTicks: { color: gridColor }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor },
                    formatter: val => (val >= 1000 ? (val / 1000).toFixed(1) + 'k Ha' : val.toFixed(0) + ' Ha')
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: { formatter: val => Number(val).toLocaleString('id-ID') + ' Hektar' }
            },
            legend: {
                position: 'top',
                labels: { colors: textColor }
            },
            grid: { borderColor: gridColor }
        };

        if (trendChartInstance) trendChartInstance.destroy();
        trendChartInstance = new ApexCharts(trendEl, trendOptions);
        trendChartInstance.render();
    }

    // 2. LULC Composition Donut Chart
    const lulcEl = document.querySelector("#lulcDonutChart");
    if (lulcEl && lulcData && lulcData.length > 0) {
        const series = lulcData.map(d => d.ha);
        const labels = lulcData.map(d => d.name);
        const colors = lulcData.map(d => d.color);

        const lulcOptions = {
            series: series,
            labels: labels,
            colors: colors,
            chart: {
                type: 'donut',
                height: 330,
                fontFamily: 'Inter, sans-serif',
                background: 'transparent'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Area',
                                color: textColor,
                                formatter: w => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return (total / 1000).toFixed(0) + 'k Ha';
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'bottom',
                labels: { colors: textColor }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: { formatter: val => Number(val).toLocaleString('id-ID') + ' Ha' }
            },
            stroke: { show: false }
        };

        if (lulcChartInstance) lulcChartInstance.destroy();
        lulcChartInstance = new ApexCharts(lulcEl, lulcOptions);
        lulcChartInstance.render();
    }

    // 3. Region Comparison Chart
    const compEl = document.querySelector("#regionComparisonChart");
    if (compEl && comparisonData && comparisonData.length > 0) {
        const regionNames = comparisonData.map(d => d.region_name.split('(')[0].trim());
        const forestAreas = comparisonData.map(d => parseFloat(d.total_forest_ha));
        const deforestationRates = comparisonData.map(d => parseFloat(d.deforestation_rate_ha));

        const compOptions = {
            series: [
                { name: 'Total Tutupan Hutan (Ha)', data: forestAreas },
                { name: 'Laju Deforestasi Tahunan (Ha)', data: deforestationRates }
            ],
            chart: {
                type: 'bar',
                height: 320,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                background: 'transparent'
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 6
                }
            },
            colors: ['#059669', '#ef4444'],
            dataLabels: { enabled: false },
            xaxis: {
                categories: regionNames,
                labels: { style: { colors: textColor, fontSize: '11px' } },
                axisBorder: { color: gridColor }
            },
            yaxis: {
                labels: {
                    style: { colors: textColor },
                    formatter: val => (val >= 1000 ? (val / 1000).toFixed(0) + 'k' : val)
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: { formatter: val => Number(val).toLocaleString('id-ID') + ' Ha' }
            },
            legend: {
                position: 'top',
                labels: { colors: textColor }
            },
            grid: { borderColor: gridColor }
        };

        if (comparisonChartInstance) comparisonChartInstance.destroy();
        comparisonChartInstance = new ApexCharts(compEl, compOptions);
        comparisonChartInstance.render();
    }
}
