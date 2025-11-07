#!/bin/bash
# Script per installare l'hook automatico dei permessi sul NAS

echo "🎯 Installing auto-permissions hook for NAS..."

HOOK_CONTENT='#!/bin/bash
echo "🔧 Sistemazione automatica permessi..."
sudo chown -R 33:33 /volume1/docker/nextcloud/html/apps/activitytimecalculator/
sudo chmod -R 755 /volume1/docker/nextcloud/html/apps/activitytimecalculator/
sudo docker exec nextcloud-app php occ app:enable activitytimecalculator --force > /dev/null 2>&1
echo "✅ Permessi sistemati e app abilitata!"
'

# Crea la cartella hooks se non esiste
mkdir -p .git/hooks/

# Crea l'hook
echo "$HOOK_CONTENT" > .git/hooks/post-merge
chmod +x .git/hooks/post-merge

echo "✅ Hook installed!"
echo "📝 Now run on NAS: ./install-hook.sh"
