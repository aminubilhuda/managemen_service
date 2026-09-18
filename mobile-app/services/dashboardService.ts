import { api } from './api';
import { ApiResponse, DashboardSummary } from '../types/api';

export const dashboardService = {
  async getDashboard(): Promise<DashboardSummary> {
    const response = await api.get<ApiResponse<DashboardSummary>>('/dashboard');
    return response.data.data;
  }
};
