import './bootstrap';

import Alpine from 'alpinejs';
import { initMessaging } from './messaging';

window.Alpine = Alpine;
window.initMessaging = initMessaging;

Alpine.start();
