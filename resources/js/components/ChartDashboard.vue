<template>
  <!-- Breadcrumb -->
  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
  </nav>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <h2 class="text-lg font-semibold mb-4">กราฟคำขอ</h2>
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
    months: {
      type: Array,
      default: () => [
        "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
        "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
      ]
    }
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
              label: "คำขอทั้งหมด",
              data: this.totalRequestsData,
              borderColor: "#4ade80",
              backgroundColor: "rgba(74, 222, 128, 0.2)",
              fill: true,
              tension: 0.4,
            },
            {
              label: "คำขอที่อนุมัติ",
              data: this.approvedData,
              borderColor: "#3b82f6",
              backgroundColor: "rgba(59, 130, 246, 0.2)",
              fill: true,
              tension: 0.4,
            },
            {
              label: "คำขอที่ถูกปฏิเสธ",
              data: this.rejectedData,
              borderColor: "#ef4444",
              backgroundColor: "rgba(239, 68, 68, 0.2)",
              fill: true,
              tension: 0.4,
            },
          ],
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: "bottom" }
          },
          scales: {
            y: { beginAtZero: true }
          },
        },
      });
    }
  },
};
</script>
