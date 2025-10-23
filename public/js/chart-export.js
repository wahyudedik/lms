/**
 * Chart Export Utilities
 * Enables exporting Chart.js charts as PNG or PDF
 */

class ChartExporter {
    constructor() {
        this.charts = {};
    }

    /**
     * Register a chart for export
     */
    registerChart(name, chartInstance) {
        this.charts[name] = chartInstance;
    }

    /**
     * Export chart as PNG
     */
    exportAsPNG(chartName, filename = 'chart.png') {
        const chart = this.charts[chartName];
        if (!chart) {
            console.error(`Chart "${chartName}" not found`);
            return;
        }

        // Get canvas as base64 image
        const url = chart.toBase64Image();

        // Create download link
        const link = document.createElement('a');
        link.download = filename;
        link.href = url;
        link.click();

        // Show success toast
        if (typeof Toast !== 'undefined') {
            Toast.fire({
                icon: 'success',
                title: 'Chart exported successfully!'
            });
        }
    }

    /**
     * Export all charts as PNG (ZIP)
     */
    async exportAllAsPNG(prefix = 'analytics') {
        const zip = new JSZip();
        let count = 0;

        for (const [name, chart] of Object.entries(this.charts)) {
            const base64 = chart.toBase64Image();
            const base64Data = base64.split(',')[1];
            zip.file(`${prefix}_${name}.png`, base64Data, { base64: true });
            count++;
        }

        if (count > 0) {
            const content = await zip.generateAsync({ type: 'blob' });
            const link = document.createElement('a');
            link.download = `${prefix}_${new Date().toISOString().split('T')[0]}.zip`;
            link.href = URL.createObjectURL(content);
            link.click();

            if (typeof Toast !== 'undefined') {
                Toast.fire({
                    icon: 'success',
                    title: `${count} charts exported successfully!`
                });
            }
        }
    }

    /**
     * Export chart as PDF
     */
    async exportAsPDF(chartName, filename = 'chart.pdf', title = '') {
        const chart = this.charts[chartName];
        if (!chart) {
            console.error(`Chart "${chartName}" not found`);
            return;
        }

        // Get canvas as base64 image
        const imgData = chart.toBase64Image();

        // Create PDF
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape');

        // Add title if provided
        if (title) {
            pdf.setFontSize(16);
            pdf.text(title, 15, 15);
        }

        // Calculate dimensions to fit page
        const imgWidth = 280;
        const imgHeight = (chart.height / chart.width) * imgWidth;
        const yOffset = title ? 25 : 15;

        // Add image to PDF
        pdf.addImage(imgData, 'PNG', 10, yOffset, imgWidth, imgHeight);

        // Add footer
        pdf.setFontSize(8);
        pdf.text(`Generated: ${new Date().toLocaleString()}`, 15, pdf.internal.pageSize.height - 10);

        // Save PDF
        pdf.save(filename);

        if (typeof Toast !== 'undefined') {
            Toast.fire({
                icon: 'success',
                title: 'PDF exported successfully!'
            });
        }
    }

    /**
     * Export dashboard as multi-page PDF
     */
    async exportDashboardAsPDF(filename = 'dashboard.pdf', dashboardTitle = 'Analytics Dashboard') {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('landscape');
        let isFirstPage = true;

        // Add cover page
        pdf.setFontSize(24);
        pdf.text(dashboardTitle, 15, 30);
        pdf.setFontSize(12);
        pdf.text(`Generated: ${new Date().toLocaleString()}`, 15, 45);

        // Add each chart on a new page
        for (const [name, chart] of Object.entries(this.charts)) {
            if (!isFirstPage) {
                pdf.addPage();
            } else {
                pdf.addPage();
                isFirstPage = false;
            }

            // Add chart title
            const chartTitle = this.getChartTitle(name);
            pdf.setFontSize(14);
            pdf.text(chartTitle, 15, 15);

            // Get canvas as base64 image
            const imgData = chart.toBase64Image();

            // Calculate dimensions
            const imgWidth = 280;
            const imgHeight = (chart.height / chart.width) * imgWidth;

            // Add image
            pdf.addImage(imgData, 'PNG', 10, 25, imgWidth, imgHeight);

            // Add footer
            pdf.setFontSize(8);
            pdf.text(`Page ${pdf.internal.getNumberOfPages()}`, 15, pdf.internal.pageSize.height - 10);
        }

        // Save PDF
        pdf.save(filename);

        if (typeof Toast !== 'undefined') {
            Toast.fire({
                icon: 'success',
                title: `Dashboard exported as PDF (${pdf.internal.getNumberOfPages()} pages)!`
            });
        }
    }

    /**
     * Get human-readable chart title
     */
    getChartTitle(chartName) {
        const titles = {
            'registration': 'User Registration Trend',
            'courseEnrollment': 'Course Enrollment Statistics',
            'roleDistribution': 'User Role Distribution',
            'examPerformance': 'Exam Performance Overview',
            'monthlyActivity': 'Monthly Activity Statistics',
            'studentPerformance': 'Student Performance by Course',
            'examCompletion': 'Exam Completion Rate',
            'gradeDistribution': 'Grade Distribution',
            'studentEngagement': 'Student Engagement Metrics',
            'performanceTrend': 'Performance Trend',
            'performanceByCourse': 'Performance by Course',
            'passFail': 'Pass/Fail Ratio',
            'recentScores': 'Recent Exam Scores',
            'studyTime': 'Study Time Distribution'
        };
        return titles[chartName] || chartName;
    }

    /**
     * Print chart
     */
    printChart(chartName) {
        const chart = this.charts[chartName];
        if (!chart) {
            console.error(`Chart "${chartName}" not found`);
            return;
        }

        const imgData = chart.toBase64Image();
        const win = window.open('', '_blank');
        win.document.write(`
            <html>
                <head>
                    <title>Print Chart</title>
                    <style>
                        body { margin: 0; padding: 20px; }
                        img { max-width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    <img src="${imgData}" onload="window.print();window.close()" />
                </body>
            </html>
        `);
    }
}

// Create global instance
window.chartExporter = new ChartExporter();

// Helper functions
window.exportChartPNG = (chartName, filename) => window.chartExporter.exportAsPNG(chartName, filename);
window.exportChartPDF = (chartName, filename, title) => window.chartExporter.exportAsPDF(chartName, filename, title);
window.exportDashboardPDF = (filename, title) => window.chartExporter.exportDashboardAsPDF(filename, title);
window.printChart = (chartName) => window.chartExporter.printChart(chartName);

