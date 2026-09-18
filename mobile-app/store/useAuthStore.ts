import { create } from 'zustand';
import { User, AuthData } from '../types/api';
import { storage, TOKEN_KEY, BASE_URL_KEY, DEFAULT_BASE_URL, setOnUnauthorizedCallback, api } from '../services/api';

const USER_DATA_KEY = 'USER_DATA';

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  serverUrl: string;
  setServerUrl: (url: string) => Promise<void>;
  login: (authData: AuthData) => Promise<void>;
  logout: () => Promise<void>;
  checkAuth: () => Promise<void>;
  setUser: (user: User) => void;
}

export const useAuthStore = create<AuthState>((set, get) => {
  // Set unauthorized callback from api interceptor
  setOnUnauthorizedCallback(() => {
    set({ user: null, token: null, isAuthenticated: false });
  });

  return {
    user: null,
    token: null,
    isAuthenticated: false,
    isLoading: true,
    serverUrl: DEFAULT_BASE_URL,

    setServerUrl: async (url: string) => {
      const trimmed = url.trim().replace(/\/$/, '');
      await storage.setItem(BASE_URL_KEY, trimmed);
      set({ serverUrl: trimmed });
    },

    login: async (authData: AuthData) => {
      await storage.setItem(TOKEN_KEY, authData.token);
      await storage.setItem(USER_DATA_KEY, JSON.stringify(authData.user));
      set({
        user: authData.user,
        token: authData.token,
        isAuthenticated: true,
      });
    },

    logout: async () => {
      try {
        await api.post('/auth/logout');
      } catch {
        // Continue even if network error
      }
      await storage.deleteItem(TOKEN_KEY);
      await storage.deleteItem(USER_DATA_KEY);
      set({
        user: null,
        token: null,
        isAuthenticated: false,
      });
    },

    checkAuth: async () => {
      set({ isLoading: true });
      try {
        const savedUrl = await storage.getItem(BASE_URL_KEY);
        if (savedUrl) {
          set({ serverUrl: savedUrl });
        }

        const token = await storage.getItem(TOKEN_KEY);
        const userJson = await storage.getItem(USER_DATA_KEY);

        if (token && userJson) {
          const user = JSON.parse(userJson) as User;
          set({
            token,
            user,
            isAuthenticated: true,
            isLoading: false,
          });

          // Fetch fresh user profile in background
          try {
            const res = await api.get('/auth/me');
            if (res.data?.data) {
              const freshUser = res.data.data;
              await storage.setItem(USER_DATA_KEY, JSON.stringify(freshUser));
              set({ user: freshUser });
            }
          } catch {
            // keep cached user
          }
          return;
        }
      } catch (e) {
        console.warn('Check auth error:', e);
      } finally {
        set({ isLoading: false });
      }
    },

    setUser: (user: User) => {
      storage.setItem(USER_DATA_KEY, JSON.stringify(user));
      set({ user });
    },
  };
});
