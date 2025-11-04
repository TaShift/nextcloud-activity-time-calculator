#!/bin/bash
# Setup script per NAS Synology - Run this after git pull

echo "🎯 Setting up Activity Time Calculator on NAS..."

# Permessi .git per git pull
sudo chown -R $(whoami):users .git/ 2>/dev/null || sudo chown -R $(whoami) .git/
sudo chmod -R 755 .git/

# Safe directory
git config --global --add safe.directory $(pwd)

# Installa hook
if [ ! -f .git/hooks/post-merge ]; then
    echo "📦 Installing auto-permissions hook..."
    sudo tee .git/hooks/post-merge > /dev/null << 'EOF'
#!/bin/bash
echo "🔧 Sistemazione automatica permessi..."
sudo chown -R 33:33 /volume1/docker/nextcloud/html/apps/activitytimecalculator/
sudo chmod -R 755 /volume1/docker/nextcloud/html/apps/activitytimecalculator/
sudo docker exec nextcloud-app php occ app:enable activitytimecalculator --force > /dev/null 2>&1
echo "✅ Permessi sistemati e app abilitata!"
EOF
    sudo chmod +x .git/hooks/post-merge
    echo "✅ Hook installed!"
fi

# Permessi app
sudo chown -R 33:33 .
sudo chmod -R 755 .

echo "🎉 Setup completato!"
echo "💡 Now you can use: git pull origin main"
echo "🔧 Permessi si sistemeranno automaticamente dopo ogni pull!"
