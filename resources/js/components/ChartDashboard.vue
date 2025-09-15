<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-4">Requests Over Time</h2>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

export default {
  name: "ChartDashboard",
 props: {
  totalRequestsData: { type: Array, default: () => [] },
  approvedData: { type: Array, default: () => [] },
  rejectedData: { type: Array, default: () => [] },
  months: { type: Array, default: () => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"] }
},
mounted() {
  this.renderChart();
},
methods: {
  renderChart() {
    const ctx = this.$refs.chartCanvas.getContext("2d");
    if (this.chart) this.chart.destroy();

    this.chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: this.months,
        datasets: [
          {
            label: "Total Requests",
            data: this.totalRequestsData,
            borderColor: "#4ade80",
            backgroundColor: "rgba(74, 222, 128, 0.2)",
            fill: true,
            tension: 0.4,
          },
          {
            label: "Approved",
            data: this.approvedData,
            borderColor: "#94a3b8",
            backgroundColor: "rgba(148, 163, 184, 0.2)",
            fill: true,
            tension: 0.4,
          },
          {
            label: "Rejected",
            data: this.rejectedData,
            borderColor: "#facc15",
            backgroundColor: "rgba(250, 204, 21, 0.2)",
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        plugins: { legend: { position: "bottom" } },
        scales: { y: { beginAtZero: true } },
      },
    });
  }
},
};
</script>
