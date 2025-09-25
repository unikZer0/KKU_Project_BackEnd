<template>
  <div class="p-4 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">กราฟคำขอตามรายการอุปกรณ์</h2>
      <div class="flex gap-2">
        <select 
          v-model="selectedEquipment" 
          @change="updateChart"
          class="px-3 py-1 text-sm border border-gray-300 rounded-md"
        >
          <option value="">เลือกอุปกรณ์</option>
          <option 
            v-for="equipment in equipmentList" 
            :key="equipment.name" 
            :value="equipment.name"
          >
            {{ equipment.name }}
          </option>
        </select>
        <button 
          @click="toggleViewMode"
          :class="[
            'px-3 py-1 text-sm rounded-md transition-colors',
            showAllEquipment 
              ? 'bg-blue-500 text-white' 
              : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
          ]"
        >
          {{ showAllEquipment ? 'แสดงทั้งหมด' : 'แสดงรายการ' }}
        </button>
      </div>
    </div>
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";
Chart.register(...registerables);

export default {
  name: "EquipmentChart",
  props: {
    equipmentTimeData: { type: Object, default: () => ({}) }
  },
  data() {
    return {
      chart: null,
      showAllEquipment: true,
      selectedEquipment: '',
      equipmentList: [],
      isTransitioning: false
    }
  },
  mounted() {
    this.initializeData();
    this.renderChart();
  },
  methods: {
    initializeData() {
      // Extract equipment list from datasets with safety checks
      try {
        if (this.equipmentTimeData && this.equipmentTimeData.datasets) {
          this.equipmentList = this.equipmentTimeData.datasets.map(dataset => ({
            name: dataset.label || 'Unknown',
            total: dataset.data ? Math.max(...dataset.data) : 0
          }));
        } else {
          this.equipmentList = [];
        }
      } catch (error) {
        console.error('Error initializing equipment data:', error);
        this.equipmentList = [];
      }
    },
    updateChart() {
      // Prevent rapid switching
      if (this.isTransitioning) {
        return;
      }
      this.isTransitioning = true;
      
      // Debounce the chart update
      setTimeout(() => {
        this.renderChart();
        this.isTransitioning = false;
      }, 150);
    },
    toggleViewMode() {
      // Prevent rapid switching
      if (this.isTransitioning) {
        return;
      }
      this.isTransitioning = true;
      
      // Debounce the chart update
      setTimeout(() => {
        this.showAllEquipment = !this.showAllEquipment;
        this.renderChart();
        this.isTransitioning = false;
      }, 150);
    },
    renderChart() {
      // Prevent multiple simultaneous renders
      if (this.isTransitioning) {
        return;
      }

      const ctx = this.$refs.chartCanvas.getContext("2d");
      
      // Safely destroy existing chart
      if (this.chart) {
        try {
          this.chart.destroy();
        } catch (error) {
          console.warn('Chart destruction error:', error);
        }
        this.chart = null;
      }

      // Wait for next tick to ensure DOM is ready
      this.$nextTick(() => {
        try {
          // Safety check for data
          if (!this.equipmentTimeData || !this.equipmentTimeData.labels || !this.equipmentTimeData.datasets) {
            console.error('Invalid equipment time data:', this.equipmentTimeData);
            return;
          }

          const { labels, datasets } = this.equipmentTimeData;
          
          let displayDatasets;
          
          if (this.selectedEquipment) {
            // Show only selected equipment
            const selectedDataset = datasets.find(dataset => dataset.label === this.selectedEquipment);
            if (selectedDataset) {
              displayDatasets = [{
                ...selectedDataset,
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: '#3b82f6',
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#3b82f6',
                pointRadius: 6,
                pointHoverRadius: 8,
              }];
            } else {
              displayDatasets = [];
            }
          } else if (this.showAllEquipment) {
            // Show all equipment lines
            displayDatasets = datasets.map((dataset, index) => ({
              ...dataset,
              backgroundColor: 'rgba(0,0,0,0)',
              borderWidth: 3,
              fill: false,
              tension: 0.4,
              pointBackgroundColor: dataset.borderColor,
              pointBorderColor: dataset.borderColor,
              pointRadius: 5,
              pointHoverRadius: 7,
            }));
          } else {
            // Show only the top equipment (first one)
            displayDatasets = [{
              ...datasets[0],
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              borderColor: '#3b82f6',
              borderWidth: 3,
              fill: false,
              tension: 0.4,
              pointBackgroundColor: '#3b82f6',
              pointBorderColor: '#3b82f6',
              pointRadius: 6,
              pointHoverRadius: 8,
            }];
          }

          this.chart = new Chart(ctx, {
            type: 'line',
            data: {
              labels: labels,
              datasets: displayDatasets
            },
            options: {
              responsive: true,
              plugins: {
                legend: { 
                  position: 'bottom',
                  display: !this.selectedEquipment && this.showAllEquipment
                },
                tooltip: {
                  filter: (tooltipItem) => {
                    try {
                      // Only show top 3 most requested items in tooltip
                      if (this.selectedEquipment || !this.showAllEquipment) {
                        return true; // Show all when equipment is selected or in single view
                      }
                      
                      // Get top 3 datasets by maximum value
                      const sortedDatasets = datasets
                        .map((dataset, index) => ({
                          index,
                          maxValue: dataset.data ? Math.max(...dataset.data) : 0,
                          label: dataset.label
                        }))
                        .sort((a, b) => b.maxValue - a.maxValue)
                        .slice(0, 3);
                      
                      const top3Indices = sortedDatasets.map(item => item.index);
                      return top3Indices.includes(tooltipItem.datasetIndex);
                    } catch (error) {
                      console.error('Tooltip filter error:', error);
                      return true; // Fallback to show all
                    }
                  },
                  callbacks: {
                    title: function(context) {
                      return context[0].label;
                    },
                    label: function(context) {
                      return `${context.dataset.label}: ${context.parsed.y}`;
                    }
                  }
                }
              },
              scales: {
                y: { beginAtZero: true }
              },
              interaction: {
                intersect: false,
                mode: 'index'
              }
            }
          });
        } catch (error) {
          console.error('Chart rendering error:', error);
        }
      });
    }
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy();
    }
  }
};
</script>
