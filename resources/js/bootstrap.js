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

// Response interceptor to handle 401 Unauthenticated errors globally
axios.interceptors.response.use((response) => {
    return response;
}, (error) => {
    if (error.response && error.response.status === 401) {
        localStorage.removeItem('chhabra_token');
        sessionStorage.removeItem('chhabra_token');
        localStorage.removeItem('chhabra_user');

        const currentPath = window.location.pathname.toLowerCase();
        if (currentPath.includes('/admin')) {
            window.location.href = '/auth';
        }
    }
    return Promise.reject(error);
});
