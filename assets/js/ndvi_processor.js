/**
 * ForestWatch NDVI Spectral Processor & Canvas Engine
 */

let sourceCanvas = null;
let ndviCanvas = null;
let currentImageData = null;
let calculatedNdviMatrix = [];

document.addEventListener('DOMContentLoaded', () => {
    sourceCanvas = document.getElementById('sourceCanvas');
    ndviCanvas = document.getElementById('ndviCanvas');
    if (!sourceCanvas || !ndviCanvas) return;

    // Load initial sample
    loadSampleSatelliteImage('2018');

    // Sample selection buttons
    const btnSample2018 = document.getElementById('btnSample2018');
    const btnSample2024 = document.getElementById('btnSample2024');
    const customImageInput = document.getElementById('customImageInput');

    if (btnSample2018) {
        btnSample2018.addEventListener('click', () => {
            setActiveSampleBtn(btnSample2018);
            loadSampleSatelliteImage('2018');
        });
    }

    if (btnSample2024) {
        btnSample2024.addEventListener('click', () => {
            setActiveSampleBtn(btnSample2024);
            loadSampleSatelliteImage('2024');
        });
    }

    if (customImageInput) {
        customImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => processSatelliteImage(img);
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Hover inspector on Canvas
    setupHoverInspector();
});

function setActiveSampleBtn(activeBtn) {
    document.querySelectorAll('.sample-btn').forEach(b => {
        b.classList.remove('bg-emerald-600', 'text-white');
        b.classList.add('bg-slate-800', 'text-slate-300');
    });
    activeBtn.classList.add('bg-emerald-600', 'text-white');
    activeBtn.classList.remove('bg-slate-800', 'text-slate-300');
}

function loadSampleSatelliteImage(year) {
    const img = new Image();
    img.crossOrigin = 'Anonymous';
    img.onload = () => processSatelliteImage(img);
    img.src = `${window.APP_BASE_URL}/assets/samples/sample_forest_${year}.png`;
}

function processSatelliteImage(img) {
    const sCtx = sourceCanvas.getContext('2d');
    const nCtx = ndviCanvas.getContext('2d');

    const width = 480;
    const height = 480;

    sourceCanvas.width = width;
    sourceCanvas.height = height;
    ndviCanvas.width = width;
    ndviCanvas.height = height;

    sCtx.drawImage(img, 0, 0, width, height);

    currentImageData = sCtx.getImageData(0, 0, width, height);
    const pixels = currentImageData.data;
    const ndviImageData = nCtx.createImageData(width, height);
    const ndviPixels = ndviImageData.data;

    calculatedNdviMatrix = [];
    let sumNdvi = 0;
    let minNdvi = 1.0;
    let maxNdvi = -1.0;

    let waterCount = 0;
    let bareCount = 0;
    let shrubCount = 0;
    let moderateCount = 0;
    let denseCount = 0;
    const totalPixels = width * height;

    for (let i = 0; i < pixels.length; i += 4) {
        const r = pixels[i];
        const g = pixels[i + 1];
        const b = pixels[i + 2];

        // Estimasi Near-Infrared (NIR) dari spektrum hijau & merah jika RGB biasa
        // Pada citra vegetasi alami, pantulan NIR jauh lebih tinggi daripada Red
        const nir = Math.min(255, Math.max(0, g * 1.5 - r * 0.2));

        const denominator = (nir + r) === 0 ? 0.0001 : (nir + r);
        let ndvi = (nir - r) / denominator;
        ndvi = Math.max(-1.0, Math.min(1.0, ndvi));

        calculatedNdviMatrix.push(ndvi);
        sumNdvi += ndvi;
        if (ndvi < minNdvi) minNdvi = ndvi;
        if (ndvi > maxNdvi) maxNdvi = ndvi;

        // Tentukan warna heatmap berdasarkan klasifikasi NDVI
        let color = [0, 0, 0];
        if (ndvi < 0.0) {
            color = [59, 130, 246]; // Biru - Air
            waterCount++;
        } else if (ndvi < 0.2) {
            color = [217, 119, 6];  // Cokelat/Kuning - Lahan Terbuka/Tambang
            bareCount++;
        } else if (ndvi < 0.4) {
            color = [234, 179, 8];  // Kuning cerah - Semak/Rumput
            shrubCount++;
        } else if (ndvi < 0.6) {
            color = [16, 185, 129]; // Hijau muda - Vegetasi Sedang/Kebun
            moderateCount++;
        } else {
            color = [6, 95, 70];    // Hijau tua pekat - Hutan Tropis Lebat
            denseCount++;
        }

        ndviPixels[i] = color[0];
        ndviPixels[i + 1] = color[1];
        ndviPixels[i + 2] = color[2];
        ndviPixels[i + 3] = 255;
    }

    nCtx.putImageData(ndviImageData, 0, 0);

    // Update UI Stats
    const avgNdvi = sumNdvi / totalPixels;
    document.getElementById('metricMeanNdvi').innerText = avgNdvi.toFixed(3);
    document.getElementById('metricMinNdvi').innerText = minNdvi.toFixed(3);
    document.getElementById('metricMaxNdvi').innerText = maxNdvi.toFixed(3);

    const densePct = (denseCount / totalPixels * 100).toFixed(1);
    const modPct = (moderateCount / totalPixels * 100).toFixed(1);
    const shrubPct = (shrubCount / totalPixels * 100).toFixed(1);
    const barePct = (bareCount / totalPixels * 100).toFixed(1);
    const waterPct = (waterCount / totalPixels * 100).toFixed(1);

    document.getElementById('barDense').style.width = densePct + '%';
    document.getElementById('textDensePct').innerText = densePct + '%';

    document.getElementById('barModerate').style.width = modPct + '%';
    document.getElementById('textModeratePct').innerText = modPct + '%';

    document.getElementById('barShrub').style.width = shrubPct + '%';
    document.getElementById('textShrubPct').innerText = shrubPct + '%';

    document.getElementById('barBare').style.width = barePct + '%';
    document.getElementById('textBarePct').innerText = barePct + '%';

    document.getElementById('barWater').style.width = waterPct + '%';
    document.getElementById('textWaterPct').innerText = waterPct + '%';

    // Total tutupan hutan (dense + moderate)
    const totalForest = (parseFloat(densePct) + parseFloat(modPct)).toFixed(1);
    document.getElementById('metricForestCover').innerText = totalForest + '%';
}

function setupHoverInspector() {
    const handleInspect = (e, canvas) => {
        if (!currentImageData || calculatedNdviMatrix.length === 0) return;

        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;

        const x = Math.floor((e.clientX - rect.left) * scaleX);
        const y = Math.floor((e.clientY - rect.top) * scaleY);

        if (x < 0 || x >= canvas.width || y < 0 || y >= canvas.height) return;

        const index = y * canvas.width + x;
        const ndviVal = calculatedNdviMatrix[index] || 0;

        const pixelIndex = index * 4;
        const r = currentImageData.data[pixelIndex];
        const g = currentImageData.data[pixelIndex + 1];
        const b = currentImageData.data[pixelIndex + 2];

        document.getElementById('hoverCoords').innerText = `X: ${x}, Y: ${y}`;
        document.getElementById('hoverRgb').innerText = `R:${r} G:${g} B:${b}`;
        document.getElementById('hoverNdvi').innerText = ndviVal.toFixed(3);

        let category = "Hutan Tropis Lebat";
        let colorClass = "text-emerald-400";
        if (ndviVal < 0) { category = "Badan Air / Awan"; colorClass = "text-blue-400"; }
        else if (ndviVal < 0.2) { category = "Lahan Terbuka / Tambang"; colorClass = "text-amber-400"; }
        else if (ndviVal < 0.4) { category = "Semak Belukar / Alang-alang"; colorClass = "text-yellow-400"; }
        else if (ndviVal < 0.6) { category = "Vegetasi Sedang / Perkebunan"; colorClass = "text-emerald-300"; }

        const catEl = document.getElementById('hoverCategory');
        catEl.innerText = category;
        catEl.className = 'font-semibold ' + colorClass;
    };

    sourceCanvas.addEventListener('mousemove', (e) => handleInspect(e, sourceCanvas));
    ndviCanvas.addEventListener('mousemove', (e) => handleInspect(e, ndviCanvas));
}
