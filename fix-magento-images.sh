#!/bin/bash

echo "==========================================="
echo " Cleaning Magento Product Images"
echo "==========================================="

# 1. Clear cached generated images
echo ">> Removing Magento image cache..."
find pub/media/catalog/product/cache -type f -delete

# 2. Detect corrupted JPG/JPEG & log them
echo ">> Scanning for corrupted JPEG images..."
jpeginfo -c pub/media/catalog/product/**/*.{jpg,JPG,jpeg,JPEG} | grep -i "ERROR\|WARNING" > corrupted.txt
echo ">> Corrupted JPEG file list saved to: corrupted.txt"

# 3. Attempt repair of JPEG files
echo ">> Attempting JPEG repair..."
find pub/media/catalog/product -type f \( -iname "*.jpg" -o -iname "*.jpeg" \) -exec jpegtran -copy none -optimize -perfect {} \; 2>/dev/null

# 4. Fix PNG (remove interlace + metadata + compression)
echo ">> Fixing PNG files (remove interlace & metadata)..."
find pub/media/catalog/product -type f -iname "*.png" -exec mogrify -strip -interlace none -define png:compression-level=6 {} \;

echo "==========================================="
echo " Magento Image Regeneration"
echo "==========================================="

# 5. Clean cache before resize
rm -rf pub/media/catalog/product/cache/*

# 6. Regenerate Magento images
echo ">> Running Magento resize command..."
bin/magento catalog:images:resize

echo "==========================================="
echo " PROCESS COMPLETE"
echo " Check corrupted.txt for files needing re-upload."
echo "==========================================="

