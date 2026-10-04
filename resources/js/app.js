import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './push-notifications';

// Service worker (/service-worker.js) is registered in layouts/app.blade.php
// and public/js/offline.js — do not register a second worker here.
