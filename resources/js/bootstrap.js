import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Dynamically attach Bearer Authorization token to all outgoing Axios requests
axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('chhabra_token') || sessionStorage.getItem('chhabra_token');
    if (token) {
        config.headers['Authorization'] = 'Bearer ' + token;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});
