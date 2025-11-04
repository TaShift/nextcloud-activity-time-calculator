#!/bin/bash
# Manual update script - use if git pull has issues

echo "🔄 Manual update of Activity Time Calculator..."

cd /volume1/docker/nextcloud/html/apps/activitytimecalculator

# Pull changes
echo "📥 Downloading updates..."
git fetch origin
git reset --hard origin/main

# Run setup
echo "🔧 Running setup..."
chmod +x setup-nas.sh
./setup-nas.sh

echo "✅ Update completed!"
