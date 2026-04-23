import TextStyleFieldtype from './components/fieldtypes/TextStyle.vue';

Statamic.booting(() => {
    Statamic.$components.register('text_style-fieldtype', TextStyleFieldtype);
});
