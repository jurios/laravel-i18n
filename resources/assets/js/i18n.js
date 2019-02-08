window.Vue = require('vue');

window.events = new Vue();

window.flash = function (message, level = 'info', icon = true, dismissible = true) {
    window.events.$emit('flash', message, level, icon, dismissible);
};

Vue.component('vue-select', require('./components/vue-select/Select.vue').default);
Vue.component('flash', require('./components/flash/FlashMessageComponent.vue').default);

const app = new Vue({
    el: '#app'
});