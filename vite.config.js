import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '192.168.1.22', // Listen on all network interfaces
        port: 5173,     // Default Vite port
        hmr: {
            host: '192.168.1.22', // Use your specific development PC's IP here for HMR
            clientPort: 5173
        },
        cors: {
            origin: '*',
            methods: ['GET', 'POST', 'PUT', 'DELETE'],
            allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
            credentials: true,
        },
        // --- Add this proxy configuration ---
        proxy: {
            // This regex will match any request that doesn't look like a Vite asset
            // and proxy it to your Laravel backend, including the sub-directory.
            '^/(?!.*(hot.update|vite|@vite|node_modules|resources)).*': {
                target: 'http://192.168.1.22/touchdcloud/public', // **This is the key change!**
                changeOrigin: true,
                secure: false,
                // You might need to rewrite the path if Laravel expects requests without the public/ path
                // For example, if your Laravel routes are defined relative to 'touchdcloud/public/'
                // and Vite is sending 'http://192.168.1.22/some-route'
                // you might need to rewrite it to '/touchdcloud/public/some-route'.
                // However, with 'target' set correctly, this might not be needed for simple cases.
                // rewrite: (path) => path.replace(/^\//, '/touchdcloud/public/') // Example rewrite
            },
        },
    },
});