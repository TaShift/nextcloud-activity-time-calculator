/**
 * Activity Time Calculator - Plain JavaScript Version
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Activity Time Calculator loaded');
    
    const appContainer = document.getElementById('activity-time-app');
    if (!appContainer) return;
    
    // State management
    const state = {
        startDate: getDefaultStartDate(),
        endDate: getDefaultEndDate(),
        data: {},
        loading: false,
        error: null
    };
    
    function render() {
        appContainer.innerHTML = `
            <div class="section">
                <h2>Activity Time Calculator</h2>
                
                <div class="date-selector">
                    <label for="start-date">From:</label>
                    <input type="date" id="start-date" value="${state.startDate}">
                    
                    <label for="end-date">To:</label>
                    <input type="date" id="end-date" value="${state.endDate}">
                    
                    <button id="calculate-btn" ${state.loading ? 'disabled' : ''}>
                        ${state.loading ? 'Loading...' : 'Calculate'}
                    </button>
                </div>

                ${state.error ? `<div class="error-message">${state.error}</div>` : ''}
                
                ${Object.keys(state.data).length > 0 ? `
                <div class="results-section">
                    <h3>Results</h3>
                    <div class="results-table">
                        <div class="table-header">
                            <span>Category</span>
                            <span>Time</span>
                        </div>
                        ${Object.entries(state.data).map(([category, seconds]) => `
                            <div class="table-row">
                                <span class="category-name">${category}</span>
                                <span class="time-display">${formatTime(seconds)}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ` : '<div class="welcome-message"><p>Select a date range and click "Calculate" to analyze your calendar activities.</p></div>'}
            </div>
        `;
        
        // Add event listeners
        document.getElementById('calculate-btn').addEventListener('click', loadData);
        document.getElementById('start-date').addEventListener('change', (e) => {
            state.startDate = e.target.value;
        });
        document.getElementById('end-date').addEventListener('change', (e) => {
            state.endDate = e.target.value;
        });
    }
    
    async function loadData() {
        state.loading = true;
        state.error = null;
        render();
        
        try {
            const response = await fetch(
                OC.generateUrl('/apps/activitytimecalculator/api/activity-data') +
                `?startDate=${state.startDate}&endDate=${state.endDate}`
            );
            
            const result = await response.json();
            
            if (result.status === 'success') {
                state.data = result.data;
            } else {
                state.error = result.message || 'Unknown error occurred';
            }
        } catch (error) {
            console.error('Error loading data:', error);
            state.error = 'Failed to load data. Please try again.';
        } finally {
            state.loading = false;
            render();
        }
    }
    
    function getDefaultStartDate() {
        const date = new Date();
        date.setDate(date.getDate() - 30);
        return date.toISOString().split('T')[0];
    }
    
    function getDefaultEndDate() {
        return new Date().toISOString().split('T')[0];
    }
    
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        
        if (hours > 0) {
            return `${hours}h ${minutes}m`;
        } else {
            return `${minutes}m`;
        }
    }
    
    // Initial render
    render();
});
