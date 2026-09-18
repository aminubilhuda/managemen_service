import { api } from './api';
import { ApiResponse, PaginatedResponse, Pelanggan } from '../types/api';

export const pelangganService = {
  async getPelangganList(params?: { search?: string; page?: number; per_page?: number }): Promise<PaginatedResponse<Pelanggan>> {
    const response = await api.get<PaginatedResponse<Pelanggan>>('/pelanggan', { params });
    return response.data;
  },

  async searchPelanggan(q: string): Promise<Pelanggan[]> {
    const response = await api.get<ApiResponse<Pelanggan[]>>('/pelanggan/search', { params: { q } });
    return response.data.data;
  },

  async getPelangganDetail(id: number): Promise<Pelanggan> {
    const response = await api.get<ApiResponse<Pelanggan>>(`/pelanggan/${id}`);
    return response.data.data;
  },

  async createPelanggan(data: { nama_pelanggan: string; no_telp: string; alamat?: string }): Promise<Pelanggan> {
    const response = await api.post<ApiResponse<Pelanggan>>('/pelanggan', data);
    return response.data.data;
  },

  async updatePelanggan(id: number, data: { nama_pelanggan: string; no_telp: string; alamat?: string }): Promise<Pelanggan> {
    const response = await api.put<ApiResponse<Pelanggan>>(`/pelanggan/${id}`, data);
    return response.data.data;
  }
};
