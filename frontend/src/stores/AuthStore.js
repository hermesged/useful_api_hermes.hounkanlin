import { defineStore } from 'pinia';
import axios from 'axios';
import router from '@/router';

const BASE_URL = import.meta.env.VITE_URL;

export const useAuthStore = defineStore('auth', {
  persiste: true,
  state: () => ({
    user: null,
    success: false,
    loading: false,
  }),

  actions: {
    async register(data) {
      this.loading = true;
      this.success = false;

      try {
        const response = await axios.post(`${BASE_URL}/register`, data);
        this.user = response.data.user;
        this.success = true;
      } finally {
        this.loading = false;
      }
    },

    async login(data) {
      this.loading = true;
      this.success = false;

      try {
        const response = await axios.post(`${BASE_URL}/login`, data);
        this.user = response.data.user;
        this.success = true;
      } finally {
        this.loading = false;
      }
    }
  }
});
