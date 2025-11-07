<?php
// Simple server-side form with AJAX fallback
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
?>

<div class="activity-time-calculator">
    <h2>Activity Time Calculator</h2>
    
    <div class="date-filters">
        <label>Start Date: <input type="date" id="startDate" value="<?php echo htmlspecialchars($startDate) ?>"></label>
        <label>End Date: <input type="date" id="endDate" value="<?php echo htmlspecialchars($endDate) ?>"></label>
        <button onclick="calculateTime()">Calculate Time</button>
    </div>

    <div id="results" style="margin-top: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
        <p>Select date range and click Calculate Time</p>
    </div>
</div>

<script>
function calculateTime() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both dates');
        return;
    }

    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '<p>Loading calendar data...</p>';

    // Update URL without reloading page
    window.history.replaceState({}, '', `?startDate=${startDate}&endDate=${endDate}`);

    fetch(`/apps/activitytimecalculator/api/activity-data?startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayResults(data);
            } else {
                resultsDiv.innerHTML = `<p style="color: red;">Error: ${data.error || data.message}</p>`;
            }
        })
        .catch(error => {
            resultsDiv.innerHTML = `<p style="color: red;">Network error: ${error}</p>`;
        });
}

function displayResults(data) {
    let html = `<h3>Time by Category (${data.eventCount} events from ${data.calendarCount} calendars):</h3>`;
    
    if (Object.keys(data.data).length === 0) {
        html += '<p>No events found in the selected date range.</p>';
    } else {
        html += '<ul>';
        for (const [category, seconds] of Object.entries(data.data)) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            html += `<li><strong>${category}:</strong> ${hours}h ${minutes}m</li>`;
        }
        html += '</ul>';
    }
    
    document.getElementById('results').innerHTML = html;
}
</script>
