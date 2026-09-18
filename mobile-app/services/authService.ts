import { api } from './api';
import { ApiResponse, AuthData, User } from '../types/api';

export const authService = {
  async login(login: string, password: string, deviceName: string = 'Mobile App'): Promise<AuthData> {
    const response = await api.post<ApiResponse<AuthData>>('/auth/login', {
      login,
      password,
      device_name: deviceName,
    });
    return response.data.data;
  },

  async getProfile(): Promise<User> {
    const response = await api.get<ApiResponse<User>>('/auth/me');
    return response.data.data;
  },

  async updateProfile(data: { name: string; email: string; username: string }): Promise<User> {
    const response = await api.put<ApiResponse<User>>('/auth/profile', data);
    return response.data.data;
  },

  async changePassword(data: { current_password: string; password: string; password_confirmation: string }): Promise<void> {
    await api.put('/auth/password', data);
  },

  async logout(): Promise<void> {
    await api.post('/auth/logout');
  }
};
