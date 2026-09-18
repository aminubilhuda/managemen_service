import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

export const DEFAULT_BASE_URL = Platform.OS === 'android' 
  ? 'http://10.0.2.2:8000/api/v1' 
  : 'http://localhost:8000/api/v1';

const TOKEN_KEY = 'AUTH_TOKEN';
const BASE_URL_KEY = 'API_BASE_URL';

// Safe storage wrapper
export const storage = {
  async getItem(key: string): Promise<string | null> {
    try {
      if (Platform.OS === 'web') {
        return typeof localStorage !== 'undefined' ? localStorage.getItem(key) : null;
      }
      return await SecureStore.getItemAsync(key);
    } catch {
      return null;
    }
  },
  async setItem(key: string, value: string): Promise<void> {
    try {
      if (Platform.OS === 'web') {
        if (typeof localStorage !== 'undefined') {
          localStorage.setItem(key, value);
        }
        return;
      }
      await SecureStore.setItemAsync(key, value);
    } catch (e) {
      console.warn('Storage set error:', e);
    }
  },
  async deleteItem(key: string): Promise<void> {
    try {
      if (Platform.OS === 'web') {
        if (typeof localStorage !== 'undefined') {
          localStorage.removeItem(key);
        }
        return;
      }
      await SecureStore.deleteItemAsync(key);
    } catch (e) {
      console.warn('Storage delete error:', e);
    }
  }
};

export const api = axios.create({
  timeout: 20000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

let onUnauthorizedCallback: (() => void) | null = null;

export const setOnUnauthorizedCallback = (callback: () => void) => {
  onUnauthorizedCallback = callback;
};

// Request Interceptor: inject dynamic baseURL and Bearer token
api.interceptors.request.use(async (config: InternalAxiosRequestConfig) => {
  let customBaseUrl = await storage.getItem(BASE_URL_KEY);
  let activeBaseUrl = customBaseUrl || DEFAULT_BASE_URL;
  if (!activeBaseUrl.includes('/api/v1')) {
    activeBaseUrl = `${activeBaseUrl.replace(/\/$/, '')}/api/v1`;
  }
  config.baseURL = activeBaseUrl;

  const token = await storage.getItem(TOKEN_KEY);
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Response Interceptor: handle 401 unauthenticated
api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      await storage.deleteItem(TOKEN_KEY);
      if (onUnauthorizedCallback) {
        onUnauthorizedCallback();
      }
    }
    return Promise.reject(error);
  }
);

export { TOKEN_KEY, BASE_URL_KEY };
