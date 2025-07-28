import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

import './bootstrap'; // Or your existing bootstrap.js/alpine.js setup
import Chart from 'chart.js/auto'; // Import Chart.js

window.Chart = Chart;

Alpine.start();
