import { api } from './api';
import { ApiResponse, Perusahaan, TeknisiItem, StatusMasterItem } from '../types/api';

export const masterService = {
  async getPerusahaan(): Promise<Perusahaan> {
    const response = await api.get<ApiResponse<Perusahaan>>('/master/perusahaan');
    return response.data.data;
  },

  async getTeknisi(): Promise<TeknisiItem[]> {
    const response = await api.get<ApiResponse<TeknisiItem[]>>('/master/teknisi');
    return response.data.data;
  },

  async getStatusTiket(): Promise<StatusMasterItem[]> {
    const response = await api.get<ApiResponse<StatusMasterItem[]>>('/master/status-tiket');
    return response.data.data;
  },

  async getPajak(): Promise<any[]> {
    const response = await api.get<ApiResponse<any[]>>('/master/pajak');
    return response.data.data;
  }
};
