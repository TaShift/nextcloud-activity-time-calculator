<?php
script('activitytimecalculator', 'dashboard');
style('activitytimecalculator', 'dashboard');
?>

<div id="activity-time-calculator" class="section">
    <h2><?php p($l->t('Activity Time Calculator')); ?></h2>
    
    <div class="calculator-container">
        <div class="date-filters">
            <label>
                <?php p($l->t('Start Date:')); ?> 
                <input type="date" id="startDate">
            </label>
            <label>
                <?php p($l->t('End Date:')); ?> 
                <input type="date" id="endDate">
            </label>
            <button id="calculate-button" class="primary">
                <?php p($l->t('Calculate Time')); ?>
            </button>
        </div>

        <div id="results" class="results-container">
            <p><?php p($l->t('Select date range and click Calculate Time')); ?></p>
        </div>
    </div>
</div>
