(function() {
    'use strict';
    
    const { createApp } = window.Vue;
    
    createApp({
        data() {
            return {
                loading: false,
                error: null,
                hasData: false,
                report: {}
            };
        },
        
        mounted() {
            console.log('Activity Time Calculator mounted');
            // Auto-load report on mount
            this.loadReport();
        },
        
        methods: {
            async loadReport() {
                this.loading = true;
                this.error = null;
                
                try {
                    const response = await axios.get(OC.generateUrl('/apps/activitytimecalculator/api/report'));
                    
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
        }
    }).mount('#activity-time-calculator');
})();
