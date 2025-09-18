import { createApp } from 'vue';
import EquipmentReport from './components/tables/reports/EquipmentReport.vue';
import CategoryReport from './components/tables/reports/CategoryReport.vue';
import UserReport from './components/tables/reports/UserReport.vue';

const el = document.getElementById('report-app');

if (el) {
    const type = el.dataset.reportType;
    const components = {
        equipment: EquipmentReport,
        category: CategoryReport,
        user: UserReport
    };

    const SelectedComponent = components[type] || EquipmentReport;
    const app = createApp(SelectedComponent);
    app.mount('#report-app');
}