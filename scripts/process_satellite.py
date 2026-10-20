#!/usr/bin/env python3
"""
ForestWatch Satellite Image Processing Engine
Menghitung NDVI (Normalized Difference Vegetation Index), klasifikasi tutupan lahan,
dan deteksi perubahan kanopi multi-temporal menggunakan NumPy dan Pillow.
"""

import sys
import os
import json
import argparse
import numpy as np
from PIL import Image, ImageDraw

def compute_ndvi_from_bands(red_band, nir_band):
    """
    NDVI = (NIR - RED) / (NIR + RED)
    Rentang nilai: -1.0 hingga +1.0
    """
    red = red_band.astype(np.float32)
    nir = nir_band.astype(np.float32)
    
    denominator = nir + red
    # Cegah division by zero
    denominator[denominator == 0] = 0.00001
    
    ndvi = (nir - red) / denominator
    return np.clip(ndvi, -1.0, 1.0)

def classify_ndvi_matrix(ndvi):
    """
    Klasifikasi matriks piksel NDVI ke kategori tutupan kanopi
    """
    total_pixels = ndvi.size
    
    water_mask = ndvi < 0.0
    bare_mask = (ndvi >= 0.0) & (ndvi < 0.2)
    shrub_mask = (ndvi >= 0.2) & (ndvi < 0.4)
    med_veg_mask = (ndvi >= 0.4) & (ndvi < 0.6)
    dense_forest_mask = ndvi >= 0.6
    
    return {
        "water_pixels": int(np.sum(water_mask)),
        "water_pct": round(float(np.sum(water_mask) / total_pixels * 100), 2),
        "bare_land_pixels": int(np.sum(bare_mask)),
        "bare_land_pct": round(float(np.sum(bare_mask) / total_pixels * 100), 2),
        "shrub_pixels": int(np.sum(shrub_mask)),
        "shrub_pct": round(float(np.sum(shrub_mask) / total_pixels * 100), 2),
        "moderate_veg_pixels": int(np.sum(med_veg_mask)),
        "moderate_veg_pct": round(float(np.sum(med_veg_mask) / total_pixels * 100), 2),
        "dense_forest_pixels": int(np.sum(dense_forest_mask)),
        "dense_forest_pct": round(float(np.sum(dense_forest_mask) / total_pixels * 100), 2),
        "mean_ndvi": round(float(np.mean(ndvi)), 4),
        "min_ndvi": round(float(np.min(ndvi)), 4),
        "max_ndvi": round(float(np.max(ndvi)), 4),
        "std_ndvi": round(float(np.std(ndvi)), 4)
    }

def colorize_ndvi(ndvi):
    """
    Mengonversi matriks NDVI ke citra RGB dengan palet warna remote sensing:
    - Biru: Badan air (< 0.0)
    - Cokelat/Merah: Lahan gundul (0.0 - 0.2)
    - Kuning: Semak / vegetasi jarang (0.2 - 0.4)
    - Hijau muda: Vegetasi sedang (0.4 - 0.6)
    - Hijau tua lebat: Hutan kanopi rapat (> 0.6)
    """
    h, w = ndvi.shape
    rgb = np.zeros((h, w, 3), dtype=np.uint8)
    
    # Air
    mask = ndvi < 0.0
    rgb[mask] = [59, 130, 246]
    
    # Lahan Terbuka
    mask = (ndvi >= 0.0) & (ndvi < 0.2)
    rgb[mask] = [217, 119, 6]
    
    # Semak
    mask = (ndvi >= 0.2) & (ndvi < 0.4)
    rgb[mask] = [234, 179, 8]
    
    # Vegetasi Sedang
    mask = (ndvi >= 0.4) & (ndvi < 0.6)
    rgb[mask] = [16, 185, 129]
    
    # Hutan Lebat
    mask = ndvi >= 0.6
    rgb[mask] = [6, 95, 70]
    
    return Image.fromarray(rgb)

def generate_sample_satellite_data(output_dir):
    """
    Membuat citra satelit sampel sintetis resolusi tinggi
    untuk simulasi pemantauan 2018 vs 2024
    """
    os.makedirs(output_dir, exist_ok=True)
    width, height = 512, 512
    
    # Buat sampel 2018 (Hutan Lebat Utuh)
    np.random.seed(42)
    img_2018 = Image.new('RGB', (width, height), color=(28, 90, 45))
    draw_2018 = ImageDraw.Draw(img_2018)
    
    # Tambahkan sungai alami
    points = [(50, 0), (120, 150), (200, 260), (320, 380), (480, 512)]
    draw_2018.line(points, fill=(35, 75, 125), width=24)
    
    # Tekstur kanopi hutan alami (variasi hijau tua)
    pixels_2018 = np.array(img_2018)
    noise_2018 = np.random.randint(-15, 15, (height, width, 3))
    pixels_2018 = np.clip(pixels_2018.astype(np.int16) + noise_2018, 0, 255).astype(np.uint8)
    img_2018 = Image.fromarray(pixels_2018)
    
    path_2018 = os.path.join(output_dir, "sample_forest_2018.png")
    img_2018.save(path_2018)
    
    # Buat sampel 2024 (Deforestasi & Pembukaan Lahan)
    img_2024 = img_2018.copy()
    draw_2024 = ImageDraw.Draw(img_2024)
    
    # Pembukaan lahan blok 1 (Tambang/Lahan Terbuka)
    draw_2024.polygon([(260, 80), (440, 100), (460, 240), (280, 220)], fill=(185, 130, 75))
    # Pembukaan lahan blok 2 (Kebun Kelapa Sawit Berpola Baris)
    draw_2024.rectangle([(60, 300), (230, 480)], fill=(130, 145, 60))
    # Jalan akses loging
    draw_2024.line([(0, 200), (260, 150), (450, 280), (512, 300)], fill=(160, 140, 110), width=8)
    
    pixels_2024 = np.array(img_2024)
    noise_2024 = np.random.randint(-12, 12, (height, width, 3))
    pixels_2024 = np.clip(pixels_2024.astype(np.int16) + noise_2024, 0, 255).astype(np.uint8)
    img_2024 = Image.fromarray(pixels_2024)
    
    path_2024 = os.path.join(output_dir, "sample_forest_2024.png")
    img_2024.save(path_2024)
    
    # Buat juga citra NDVI heatmap dari sampel 2024
    # Estimasi pseudo-NIR dari band Green & Blue
    r = pixels_2024[:, :, 0]
    g = pixels_2024[:, :, 1]
    b = pixels_2024[:, :, 2]
    pseudo_nir = np.clip(g.astype(np.float32) * 1.5 - r.astype(np.float32) * 0.3, 0, 255)
    ndvi_2024 = compute_ndvi_from_bands(r, pseudo_nir)
    ndvi_colored = colorize_ndvi(ndvi_2024)
    path_ndvi = os.path.join(output_dir, "sample_ndvi_heatmap.png")
    ndvi_colored.save(path_ndvi)
    
    return {
        "sample_2018": path_2018,
        "sample_2024": path_2024,
        "sample_ndvi": path_ndvi
    }

def main():
    parser = argparse.ArgumentParser(description="ForestWatch Satellite Image Processing")
    parser.add_argument("--test", action="store_true", help="Uji coba komputasi spektral NDVI")
    parser.add_argument("--generate-samples", type=str, help="Buat dataset citra satelit sampel pada folder tertentu")
    parser.add_argument("--input", type=str, help="Path citra satelit input untuk diproses")
    parser.add_argument("--output", type=str, help="Path citra output NDVI heatmap")
    
    args = parser.parse_args()
    
    if args.test:
        # Simulasi matriks 100x100
        sim_red = np.random.uniform(20, 80, (100, 100)).astype(np.float32)
        sim_nir = np.random.uniform(100, 220, (100, 100)).astype(np.float32)
        ndvi = compute_ndvi_from_bands(sim_red, sim_nir)
        stats = classify_ndvi_matrix(ndvi)
        print(json.dumps({"status": "SUCCESS", "message": "Satellite Engine Berfungsi Sempurna", "stats": stats}, indent=2))
        return

    if args.generate_samples:
        results = generate_sample_satellite_data(args.generate_samples)
        print(json.dumps({"status": "SUCCESS", "files": results}, indent=2))
        return

    if args.input:
        if not os.path.exists(args.input):
            print(json.dumps({"status": "ERROR", "message": f"File {args.input} tidak ditemukan."}))
            sys.exit(1)
            
        img = Image.open(args.input).convert("RGB")
        arr = np.array(img)
        r = arr[:, :, 0]
        g = arr[:, :, 1]
        
        # Menggunakan band Green sebagai proxy Near-Infrared jika citra RGB standar
        pseudo_nir = np.clip(g.astype(np.float32) * 1.45, 0, 255)
        ndvi = compute_ndvi_from_bands(r, pseudo_nir)
        stats = classify_ndvi_matrix(ndvi)
        
        if args.output:
            colored = colorize_ndvi(ndvi)
            colored.save(args.output)
            stats["output_image"] = args.output
            
        print(json.dumps({"status": "SUCCESS", "data": stats}, indent=2))
        return

    parser.print_help()

if __name__ == "__main__":
    main()
