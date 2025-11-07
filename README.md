# Activity Time Calculator for Nextcloud

A Nextcloud app that analyzes your CalDAV calendar events and calculates the total time spent on each activity category.

## Features

- 📊 **Time Analysis**: Calculate duration for each event category
- 🗓️ **Calendar Integration**: Works with all your Nextcloud calendars  
- 📈 **Visual Reports**: Clean interface showing time distribution
- ⏰ **Date Range Filtering**: Analyze specific time periods

## Installation

1. Clone into Nextcloud apps directory:
```bash
cd nextcloud/apps/
git clone https://github.com/your-username/nextcloud-activity-time-calculator.git activitytimecalculator
##  ENABLE THE APP
./occ app:enable activitytimecalculator

## **2. COMANDI PER REINSTALLARE**

```bash
# Dal NAS via SSH:
cd /volume1/docker/nextcloud/html/apps/

# Elimina la vecchia versione
sudo rm -rf activitytimecalculator

# Clona la nuova versione da GitHub
sudo git clone https://github.com/TaShift/nextcloud-activity-time-calculator.git activitytimecalculator

# Imposta permessi
sudo chown -R 33:33 activitytimecalculator/
sudo chmod -R 755 activitytimecalculator/

# Abilita l'app
docker exec -it nextcloud-app php occ app:enable activitytimecalculator
