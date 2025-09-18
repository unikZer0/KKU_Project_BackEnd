import { createApp } from 'vue';
import EquipmentReport from './components/tables/reports/EquipmentReport.vue';
import CategoryReport from './components/tables/reports/CategoryReport.vue';
import UserReport from './components/tables/reports/UserReport.vue';
import RequestReport from './components/tables/reports/RequestReport.vue';

const el = document.getElementById('report-app');

if (el) {
    const type = el.dataset.reportType;
    const components = {
        equipment: EquipmentReport,
        category: CategoryReport,
        user: UserReport,
        request: RequestReport
    };

    const SelectedComponent = components[type] || EquipmentReport;
    const app = createApp(SelectedComponent);
    app.mount('#report-app');
}