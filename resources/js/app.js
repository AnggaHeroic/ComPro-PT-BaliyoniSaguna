import Alpine from 'alpinejs';
import cardList from './card-list';
import carousel from './carousel';
import contactForm from './contact-form';
import setupConfirm from './confirm';
import dateFilter from './date-filter';
import galleryLightbox from './gallery-lightbox';
import iconPicker from './icon-picker';
import navbar from './navbar';
import pageEditor from './page-editor';
import photoUpload from './photo-upload';
import reorderList from './reorder-list';
import reveal from './reveal';
import sectionPreview from './section-preview';
import serviceScope from './service-scope';
import titleEditor from './title-editor';
import setupUnsavedGuard from './unsaved-guard';

Alpine.data('navbar', navbar);
Alpine.data('serviceScope', serviceScope);
Alpine.data('carousel', carousel);
Alpine.data('contactForm', contactForm);
Alpine.data('galleryLightbox', galleryLightbox);
Alpine.data('reorderList', reorderList(Alpine));
Alpine.data('iconPicker', iconPicker);
Alpine.data('dateFilter', dateFilter);
Alpine.data('photoUpload', photoUpload);
Alpine.data('pageEditor', pageEditor);
Alpine.data('sectionPreview', sectionPreview);
Alpine.data('titleEditor', titleEditor);
Alpine.data('cardList', cardList);

// Perilaku panel admin; tidak aktif di halaman publik karena hanya bereaksi pada atribut data-confirm-* dan data-unsaved-guard.
setupConfirm(Alpine);
setupUnsavedGuard(Alpine);

window.Alpine = Alpine;
Alpine.start();
reveal();
