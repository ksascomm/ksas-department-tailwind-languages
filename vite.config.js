import { defineConfig } from "vite";

export default defineConfig({
  plugins: [
    {
      name: "wordpress-reload",
      handleHotUpdate({ file, server }) {
        // Force full page reload on PHP file or main style.css edits
        if (file.endsWith(".php") || file.endsWith("style.css")) {
          server.ws.send({
            type: "full-reload",
            path: "*",
          });
        }
      },
    },
  ],
  server: {
    // Automatically open your local site URL in browser on npm run dev
    open: "https://krieger.local/modern-languages-literatures",

    // Automatically open your local site URL in browser on npm run dev
    cors: true,
    strictPort: true,
    port: 5174, // Child theme Vite port
    hmr: {
      host: "localhost",
    },
    watch: {
      // Tell Vite to actively watch root style.css, PHP files, and templates
      usePolling: true, // Optional: useful if developing in Local WP or Docker
      include: ["**/*.php", "style.css"],
    },
  },
  build: {
    outDir: "dist",
    manifest: true,
  },
});
