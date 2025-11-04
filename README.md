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
