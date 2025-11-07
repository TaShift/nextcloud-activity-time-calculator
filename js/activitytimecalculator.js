document.addEventListener('DOMContentLoaded', function() {
    // Aggiungi event listener al bottone
    const calculateBtn = document.getElementById('calculateBtn');
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateTime);
    }
});

function calculateTime() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        alert('Please select both dates');
        return;
    }

    fetch(`/apps/activitytimecalculator/api/activity-data?startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displayResults(data.data);
            } else {
                document.getElementById('results').innerHTML = `<p>Error: ${data.error || data.message}</p>`;
            }
        })
        .catch(error => {
            document.getElementById('results').innerHTML = `<p>Error: ${error}</p>`;
        });
}

function displayResults(data) {
    let html = '<h3>Time by Category:</h3><ul>';
    
    for (const [category, seconds] of Object.entries(data)) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        html += `<li><strong>${category}:</strong> ${hours}h ${minutes}m</li>`;
    }
    
    html += '</ul>';
    document.getElementById('results').innerHTML = html;
}
