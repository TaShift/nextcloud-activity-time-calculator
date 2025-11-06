// In the loadData function, update the success handler:
if (result.status === 'success') {
    state.data = result.data;
    
    // Show metadata if available
    if (result.metadata) {
        console.log('Processed:', result.metadata.totalEvents, 'events from', 
                   result.metadata.calendarsProcessed, 'calendars');
    }
} else {
    state.error = result.message || 'Unknown error occurred';
}
