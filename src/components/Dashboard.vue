async loadReport() {
    this.loading = true;
    this.error = null;
    
    try {
        console.log('Loading calendar report...');
        
        const response = await axios.get(generateBaseUrl('activitytimecalculator/api/report'));
        console.log('Report response:', response.data);
        
        if (response.data.success) {
            this.report = response.data.report;
            this.hasData = true;
        } else {
            throw new Error(response.data.error || 'Failed to generate report');
        }
    } catch (error) {
        console.error('Error loading report:', error);
        this.error = error.response?.data?.error || error.message || 'Failed to load report';
        this.hasData = false;
    } finally {
        this.loading = false;
    }
}
