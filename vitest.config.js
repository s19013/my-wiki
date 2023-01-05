/// <reference types="vitest" />

import { defineConfig } from 'vite'
import Vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [
    Vue(),
  ],
  test: {
    global: true,
    environment: 'happy-dom',
    setupFiles: ['./tests/Flont/setup.js'],
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, "resources/js/")
    },
  },
  css: false,
})
