<template>
  <div class="w-full max-w-md mx-auto">
    <canvas id="statusChart"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
  name: "StatusChart",
  props: {
    available: { type: Number, required: true },
    retired: { type: Number, required: true },
    maintenance: { type: Number, required: true },
  },
  mounted() {
    new Chart(this.$el.querySelector("#statusChart"), {
      type: "doughnut", // You can use "pie" too
      data: {
        labels: ["Available", "Retired", "Maintenance"],
        datasets: [
          {
            data: [this.available, this.retired, this.maintenance],
            backgroundColor: ["#22c55e", "#ef4444", "#f59e0b"], // green, red, yellow
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
      },
    });
  },
};
</script>
