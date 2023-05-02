require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import 'select2/dist/js/select2';
import 'select2/dist/css/select2.css';
import CKEditor from '@ckeditor/ckeditor5-vue';

import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import store from './Store/index'

const el = document.getElementById('app');
const  app = createApp({});

createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => require(`./Pages/${name}`).default,
        }),
    }
)
.mixin({ methods: { route } })
.use(InertiaPlugin)
.use(store)
.use(CKEditor)
.mount(el);


InertiaProgress.init({ color: '#4B5563' });
