/**
 * Admin Dashboard Scripts
 * Handles charts, real-time updates, and interactive elements
 */

(function() {
    'use strict';

    // Dashboard configuration
    const config = {
        colors: {
            primary: '#696cff',
            secondary: '#8592a3',
            success: '#71dd37',
            info: '#03c3ec',
            warning: '#ffab00',
            danger: '#ff3e1d'
        }
    };

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        initializeTables();
        initializeRealTimeUpdates();
        initializeTooltips();
    });

    /**
     * Initialize ApexCharts
     */
    function initializeCharts() {
        initMonthlyRevenueChart();
    }

    /**
     * Monthly Revenue Chart
     */
    function initMonthlyRevenueChart() {
        const chartElement = document.querySelector('#monthlyRevenueChart');
        if (!chartElement) return;

        // Get data from window.chartData set in the blade template
        const revenueData = window.chartData?.monthlyRevenue || [];
        const labels = window.chartData?.monthlyLabels || [];

        const chartOptions = {
            series: [{
                name: 'Umsatz (€)',
                data: revenueData
            }],
            chart: {
                type: 'area',
                height: 350,
                fontFamily: 'Public Sans, sans-serif',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            colors: [config.colors.primary],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: labels,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        fontSize: '13px',
                        colors: config.colors.secondary
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '13px',
                        colors: config.colors.secondary
                    },
                    formatter: function(value) {
                        return '€' + value.toLocaleString();
                    }
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            },
            tooltip: {
                y: {
                    formatter: function(value) {
                        return '€' + value.toLocaleString('de-DE', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }
        };

        const chart = new ApexCharts(chartElement, chartOptions);
        chart.render();
    }

    /**
     * Initialize DataTables for any tables
     */
    function initializeTables() {
        // Future enhancement: Initialize DataTables for recent activity tables
        const tables = document.querySelectorAll('.admin-data-table');
        tables.forEach(table => {
            // Initialize DataTable if needed
        });
    }

    /**
     * Initialize real-time updates
     */
    function initializeRealTimeUpdates() {
        // Update dashboard statistics every 30 seconds
        setInterval(updateDashboardStats, 30000);
        
        // Update system health every 60 seconds
        setInterval(updateSystemHealth, 60000);
    }

    /**
     * Update dashboard statistics via AJAX
     */
    function updateDashboardStats() {
        fetch('/admin/api/dashboard-stats', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatCards(data.stats);
            }
        })
        .catch(error => {
            console.warn('Dashboard stats update failed:', error);
        });
    }

    /**
     * Update system health indicators
     */
    function updateSystemHealth() {
        fetch('/admin/api/system-health', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSystemHealthUI(data.health);
            }
        })
        .catch(error => {
            console.warn('System health update failed:', error);
        });
    }

    /**
     * Update stat cards with new data
     */
    function updateStatCards(stats) {
        // Update user count
        const userCountElement = document.querySelector('[data-stat="total_users"]');
        if (userCountElement && stats.total_users) {
            userCountElement.textContent = stats.total_users.toLocaleString();
        }

        // Update vendor count
        const vendorCountElement = document.querySelector('[data-stat="total_vendors"]');
        if (vendorCountElement && stats.total_vendors) {
            vendorCountElement.textContent = stats.total_vendors.toLocaleString();
        }

        // Update rental count
        const rentalCountElement = document.querySelector('[data-stat="total_rentals"]');
        if (rentalCountElement && stats.total_rentals) {
            rentalCountElement.textContent = stats.total_rentals.toLocaleString();
        }

        // Update revenue
        const revenueElement = document.querySelector('[data-stat="total_revenue"]');
        if (revenueElement && stats.total_revenue) {
            revenueElement.textContent = '€' + stats.total_revenue.toLocaleString('de-DE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    /**
     * Update system health UI
     */
    function updateSystemHealthUI(health) {
        // Update storage usage
        const storageBar = document.querySelector('[data-health="storage_usage"]');
        if (storageBar && health.storage_usage_percentage) {
            storageBar.style.width = health.storage_usage_percentage + '%';
            storageBar.setAttribute('aria-valuenow', health.storage_usage_percentage);
        }

        // Update active users
        const activeUsersElement = document.querySelector('[data-health="active_users_24h"]');
        if (activeUsersElement && health.active_users_24h) {
            activeUsersElement.textContent = health.active_users_24h;
        }

        // Update DB connections
        const dbConnectionsElement = document.querySelector('[data-health="db_connections"]');
        if (dbConnectionsElement && health.db_connections) {
            dbConnectionsElement.textContent = health.db_connections;
        }
    }

    /**
     * Initialize tooltips
     */
    function initializeTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Export functions for external access if needed
     */
    window.AdminDashboard = {
        updateStats: updateDashboardStats,
        updateHealth: updateSystemHealth
    };

})();

/**
 * Additional utility functions for admin dashboard
 */

// Format numbers for display
function formatNumber(num) {
    return num.toLocaleString('de-DE');
}

// Format currency for display
function formatCurrency(amount) {
    return '€' + amount.toLocaleString('de-DE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Format percentage for display
function formatPercentage(percentage) {
    return percentage.toFixed(1) + '%';
}

// Show success notification
function showSuccessNotification(message) {
    // Implementation depends on your notification system
    console.log('Success:', message);
}

// Show error notification
function showErrorNotification(message) {
    // Implementation depends on your notification system
    console.error('Error:', message);
}
