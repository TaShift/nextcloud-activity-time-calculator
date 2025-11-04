/**
 * @copyright Copyright (c) 2024 Il Tuo Nome
 * @license GNU AGPL version 3 or any later version
 */

(function() {
    'use strict';

    const { createApp } = Vue;

    createApp({
        data() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - 30); // Last 30 days

            return {
                startDate: startDate.toISOString().split('T')[0],
                endDate: endDate.toISOString().split('T')[0],
                data: {},
                loading: false,
                error: null
            };
        },
        
        methods: {
            async loadData() {
                this.loading = true;
                this.error = null;
                
                try {
                    const response = await fetch(
                        OC.generateUrl('/apps/activitytimecalculator/api/activity-data') +
                        `?startDate=${this.startDate}&endDate=${this.endDate}`
                    );
                    
                    const result = await response.json();
                    
                    if (result.status === 'success') {
                        this.data = result.data;
                    } else {
                        this.error = result.message || 'Unknown error occurred';
                    }
                } catch (error) {
                    console.error('Error loading data:', error);
                    this.error = 'Failed to load data. Please try again.';
                } finally {
                    this.loading = false;
                }
            },
            
            formatTime(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                
                if (hours > 0) {
                    return `${hours}h ${minutes}m`;
                } else {
                    return `${minutes}m`;
                }
            }
        },
        
        mounted() {
            console.log('Activity Time Calculator app mounted');
            // Auto-load data on app start
            this.loadData();
        }
    }).mount('#activity-time-app');
})();
