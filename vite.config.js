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
        // host: 'localhost', // Listen on all network interfaces
        // port: 5173,      // Default Vite port
        // // hmr: {
        // //     host: 'localhost', // Use your specific development PC's IP here for HMR (Hot Module Replacement)
        // //                           // This is crucial for web sockets if using Alpine.js live reload etc.
        // // },
        // cors: {
        //     origin: '*', // Allows all origins. For production, you'd specify your domain.
        //                  // For development, '*' is often fine to quickly resolve CORS issues.
        //     methods: ['GET', 'POST', 'PUT', 'DELETE'], // Allowed HTTP methods
        //     allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'], // Allowed headers
        //     credentials: true, // Allow cookies and authentication headers to be sent
        // },
        // If you're using HTTPS locally, you might need:
        // https: false, // Set to true if you set up HTTPS for Vite
    },
});
