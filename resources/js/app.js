import Alpine from 'alpinejs';
import carousel from './carousel';
import navbar from './navbar';
import serviceScope from './service-scope';

Alpine.data('navbar', navbar);
Alpine.data('serviceScope', serviceScope);
Alpine.data('carousel', carousel);

window.Alpine = Alpine;
Alpine.start();
