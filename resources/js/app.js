import Alpine from 'alpinejs';
import carousel from './carousel';
import contactForm from './contact-form';
import galleryLightbox from './gallery-lightbox';
import navbar from './navbar';
import serviceScope from './service-scope';

Alpine.data('navbar', navbar);
Alpine.data('serviceScope', serviceScope);
Alpine.data('carousel', carousel);
Alpine.data('contactForm', contactForm);
Alpine.data('galleryLightbox', galleryLightbox);

window.Alpine = Alpine;
Alpine.start();
