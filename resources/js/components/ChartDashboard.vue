<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-4">Equipment Status Over Time</h2>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

export default {
  name: "ChartDashboard",
  props: {
    availableData: {
      type: Array,
      default: () => [],
    },
    unavailableData: {
      type: Array,
      default: () => [],
    },
    maintenanceData: {
      type: Array,
      default: () => [],
    },
    months: {
      type: Array,
      default: () => ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
    },
  },
  mounted() {
    this.renderChart();
  },
  methods: {
    renderChart() {
      const ctx = this.$refs.chartCanvas.getContext("2d");

      new Chart(ctx, {
        type: "line",
        data: {
          labels: this.months,
          datasets: [
            {
              label: "Available",
              data: this.availableData,
              borderColor: "#4ade80",
              backgroundColor: "rgba(74, 222, 128, 0.2)",
              tension: 0.4,
              fill: true,
            },
            {
              label: "Unavailable",
              data: this.unavailableData,
              borderColor: "#f87171",
              backgroundColor: "rgba(248, 113, 113, 0.2)",
              tension: 0.4,
              fill: true,
            },
            {
              label: "Maintenance",
              data: this.maintenanceData,
              borderColor: "#facc15",
              backgroundColor: "rgba(250, 204, 21, 0.2)",
              tension: 0.4,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: "bottom",
            },
          },
          scales: {
            y: {
              beginAtZero: true,
            },
          },
        },
      });
    },
  },
};
</script>
