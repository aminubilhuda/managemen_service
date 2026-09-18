import { api } from './api';
import { ApiResponse, PaginatedResponse, Pengeluaran } from '../types/api';

export interface PengeluaranListParams {
  kategori?: string;
  search?: string;
  dari?: string;
  sampai?: string;
  page?: number;
  per_page?: number;
}

export const pengeluaranService = {
  async getPengeluaranList(params?: PengeluaranListParams): Promise<PaginatedResponse<Pengeluaran>> {
    const response = await api.get<PaginatedResponse<Pengeluaran>>('/pengeluaran', { params });
    return response.data;
  },

  async createPengeluaran(data: FormData): Promise<Pengeluaran> {
    const response = await api.post<ApiResponse<Pengeluaran>>('/pengeluaran', data, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data.data;
  },

  async deletePengeluaran(id: number): Promise<void> {
    await api.delete(`/pengeluaran/${id}`);
  }
};
