import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Load Chart.js only on pages that actually contain a chart (separate chunk).
if (document.querySelector('canvas[data-chart]')) {
    import('./charts');
}
