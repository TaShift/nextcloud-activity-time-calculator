document.addEventListener('DOMContentLoaded', function() {
    console.log('Activity Time Calculator loaded');
    
    const calculateButton = document.getElementById('calculate-button');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const resultsDiv = document.getElementById('results');
    
    // Set default dates (last 30 days)
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    startDateInput.value = startDate.toISOString().split('T')[0];
    endDateInput.value = endDate.toISOString().split('T')[0];
    
    calculateButton.addEventListener('click', calculateTime);
    
    // Auto-calculate on page load
    calculateTime();
    
    async function calculateTime() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (!startDate || !endDate) {
            showError('Please select both dates');
            return;
        }

        showLoading('Loading calendar data...');

        try {
            const response = await fetch(OC.generateUrl('/apps/activitytimecalculator/api/report') + `?startDate=${startDate}&endDate=${endDate}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                displayResults(data.report);
            } else {
                throw new Error(data.error || 'Failed to generate report');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error loading data: ' + error.message);
        }
    }
    
    function displayResults(report) {
        let html = '';
        
        if (report.totalEvents === 0) {
            html = '<p>No events found in the selected date range.</p>';
        } else {
            html = `
                <div class="report-header">
                    <h3>Time Report</h3>
                    <p>${report.totalEvents} events from ${Object.keys(report.eventsByCalendar || {}).length} calendars</p>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Time</h4>
                        <div class="stat-value">${report.totalHours} hours</div>
                    </div>
                    <div class="stat-card">
                        <h4>Total Events</h4>
                        <div class="stat-value">${report.totalEvents}</div>
                    </div>
                    <div class="stat-card">
                        <h4>Average Duration</h4>
                        <div class="stat-value">${report.averageHours} hours</div>
                    </div>
                </div>
            `;
            
            if (report.timeByCalendar && Object.keys(report.timeByCalendar).length > 0) {
                html += `
                    <div class="calendar-breakdown">
                        <h4>Time by Calendar</h4>
                        <div class="calendar-list">
                `;
                
                for (const [calendar, time] of Object.entries(report.timeByCalendar)) {
                    html += `
                        <div class="calendar-item">
                            <span class="calendar-name">${calendar}</span>
                            <span class="calendar-time">${time} hours</span>
                        </div>
                    `;
                }
                
                html += `
                        </div>
                    </div>
                `;
            }
        }
        
        resultsDiv.innerHTML = html;
        resultsDiv.className = 'results-container has-data';
    }
    
    function showLoading(message) {
        resultsDiv.innerHTML = `<p class="loading">${message}</p>`;
        resultsDiv.className = 'results-container loading';
    }
    
    function showError(message) {
        resultsDiv.innerHTML = `<p class="error">${message}</p>`;
        resultsDiv.className = 'results-container error';
    }
});
