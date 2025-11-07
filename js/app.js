document.addEventListener('DOMContentLoaded', function() {
    console.log('Activity Time Calculator loaded');
    
    const app = document.getElementById('activity-time-app');
    if (!app) return;
    
    // Get CSRF token from page
    const csrfToken = document.querySelector('meta[name="csp-nonce"]') ? document.querySelector('meta[name="csp-nonce"]').getAttribute('nonce') : '';
    
    app.innerHTML = `
        <div class="section">
            <h2>Activity Time Calculator</h2>
            
            <div class="date-selector">
                <label for="start-date">From:</label>
                <input type="date" id="start-date">
                
                <label for="end-date">To:</label>
                <input type="date" id="end-date">
                
                <button id="calculate-btn">Calculate</button>
            </div>
            
            <div id="results"></div>
        </div>
    `;
    
    // Set default dates
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    
    document.getElementById('start-date').value = startDate.toISOString().split('T')[0];
    document.getElementById('end-date').value = endDate.toISOString().split('T')[0];
    
    // Add event listener
    document.getElementById('calculate-btn').addEventListener('click', loadData);
    
    async function loadData() {
        const btn = document.getElementById('calculate-btn');
        const results = document.getElementById('results');
        
        btn.disabled = true;
        btn.textContent = 'Loading...';
        results.innerHTML = '<p>Loading calendar data...</p>';
        
        try {
            const start = document.getElementById('start-date').value;
            const end = document.getElementById('end-date').value;
            
            const response = await fetch(
                OC.generateUrl('/apps/activitytimecalculator/api/activity-data') + 
                '?startDate=' + start + '&endDate=' + end,
                {
                    headers: {
                        'Content-Type': 'application/json',
                        'requesttoken': OC.requestToken
                    }
                }
            );
            
            const result = await response.json();
            
            if (result.status === 'success') {
                let html = '<div class="results-section"><h3>Results</h3><div class="results-table"><div class="table-header"><span>Category</span><span>Time</span></div>';
                
                for (const [category, seconds] of Object.entries(result.data)) {
                    const hours = Math.floor(seconds / 3600);
                    const minutes = Math.floor((seconds % 3600) / 60);
                    const time = hours > 0 ? hours + 'h ' + minutes + 'm' : minutes + 'm';
                    
                    html += '<div class="table-row"><span class="category-name">' + category + '</span><span class="time-display">' + time + '</span></div>';
                }
                
                html += '</div></div>';
                results.innerHTML = html;
            } else {
                results.innerHTML = '<div class="error-message">Error: ' + (result.message || 'Unknown error') + '</div>';
            }
        } catch (error) {
            console.error('Error:', error);
            results.innerHTML = '<div class="error-message">Failed to load data: ' + error.message + '</div>';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Calculate';
        }
    }
});
