<?php
/** @var \OCP\IURLGenerator $urlGenerator */
/** @var array $_ */

script('activitytimecalculator', 'app');
style('activitytimecalculator', 'style');
?>

<div id="activity-time-app" class="section">
    <h2>Activity Time Calculator</h2>
    
    <div class="date-selector">
        <label for="start-date">From:</label>
        <input type="date" id="start-date" v-model="startDate">
        
        <label for="end-date">To:</label>
        <input type="date" id="end-date" v-model="endDate">
        
        <button @click="loadData" :disabled="loading" class="primary">
            {{ loading ? 'Loading...' : 'Calculate' }}
        </button>
    </div>

    <div v-if="error" class="error-message">{{ error }}</div>
    
    <div v-if="Object.keys(data).length > 0" class="results-section">
        <h3>Results</h3>
        <div class="results-table">
            <div class="table-header">
                <span>Category</span>
                <span>Time</span>
            </div>
            <div v-for="(seconds, category) in data" :key="category" class="table-row">
                <span class="category-name">{{ category }}</span>
                <span class="time-display">{{ formatTime(seconds) }}</span>
            </div>
        </div>
    </div>

    <div v-else-if="!loading" class="welcome-message">
        <p>Select a date range and click "Calculate" to analyze your calendar activities.</p>
    </div>
</div>
