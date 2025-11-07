<?php
script('activitytimecalculator', 'dashboard');
style('activitytimecalculator', 'dashboard');
?>

<div id="activity-time-calculator" class="section">
    <h2><?php p($l->t('Activity Time Calculator')); ?></h2>
    
    <div class="calculator-container">
        <div v-if="loading" class="loading">
            <span class="icon-loading-small"></span>
            <span><?php p($l->t('Loading...')); ?></span>
        </div>
        
        <div v-else-if="error" class="error">
            <span class="icon-error"></span>
            <span>{{ error }}</span>
            <button @click="loadReport" class="retry-button">
                <?php p($l->t('Retry')); ?>
            </button>
        </div>
        
        <div v-else-if="hasData" class="report">
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php p($l->t('Total Time')); ?></h3>
                    <div class="stat-value">{{ report.totalHours }} <?php p($l->t('hours')); ?></div>
                </div>
                <div class="stat-card">
                    <h3><?php p($l->t('Total Events')); ?></h3>
                    <div class="stat-value">{{ report.totalEvents }}</div>
                </div>
                <div class="stat-card">
                    <h3><?php p($l->t('Average Duration')); ?></h3>
                    <div class="stat-value">{{ report.averageHours }} <?php p($l->t('hours')); ?></div>
                </div>
            </div>
            
            <div v-if="report.eventsByCalendar" class="calendar-breakdown">
                <h3><?php p($l->t('Time by Calendar')); ?></h3>
                <div class="calendar-list">
                    <div v-for="(time, calendar) in report.timeByCalendar" :key="calendar" class="calendar-item">
                        <span class="calendar-name">{{ calendar }}</span>
                        <span class="calendar-time">{{ time }} <?php p($l->t('hours')); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div v-else class="no-data">
            <p><?php p($l->t('No calendar data available.')); ?></p>
            <button @click="loadReport" class="load-button">
                <?php p($l->t('Load Report')); ?>
            </button>
        </div>
    </div>
</div>
