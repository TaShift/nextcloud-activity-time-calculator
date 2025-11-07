<?php
// Simple form processing
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$results = '';

if ($startDate && $endDate) {
    // Mock data for now - we'll add real calendar later
    $results = "
        <h3>Time by Category (Mock Data):</h3>
        <ul>
            <li><strong>Work:</strong> 34h 45m</li>
            <li><strong>Meetings:</strong> 12h 20m</li>
            <li><strong>Personal:</strong> 8h 15m</li>
            <li><strong>Development:</strong> 22h 30m</li>
        </ul>
        <p><em>Calendar integration coming soon for dates: $startDate to $endDate</em></p>
    ";
}
?>

<div class="activity-time-calculator">
    <h2>Activity Time Calculator</h2>
    
    <div class="date-filters">
        <form method="GET" action="">
            <label>Start Date: <input type="date" name="startDate" value="<?php echo htmlspecialchars($startDate) ?>"></label>
            <label>End Date: <input type="date" name="endDate" value="<?php echo htmlspecialchars($endDate) ?>"></label>
            <button type="submit">Calculate Time</button>
        </form>
    </div>

    <div id="results" style="margin-top: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
        <?php echo $results ?: '<p>Select date range and click Calculate Time</p>'; ?>
    </div>
</div>
