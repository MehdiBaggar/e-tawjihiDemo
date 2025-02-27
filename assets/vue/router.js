import { createRouter, createWebHistory } from 'vue-router';


import EditFields from "./views/editFields.vue";
import Index from "./views/index.vue";
import Personnes from "./views/personnes.vue";
import ContratChineFormPdf from "./views/contratChineFormPdf.vue";
// Assuming you will create this view

// Define your routes
const routes = [
    {
        path: '/',
        component: Personnes,
        name: 'home',
    },
    {
        path: '/fields/:id',
        component: EditFields,
        name: 'field',
    },
    {
        path: '/contrat',
        component: ContratChineFormPdf ,
        name: 'contrat',
    },
];

// Create the Vue Router instance
const router = createRouter({
    history: createWebHistory(), // For SPAs, this uses the HTML5 History API
    routes, // Register your routes here
});

export default router;
