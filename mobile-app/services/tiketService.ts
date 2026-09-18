import { api } from './api';
import { ApiResponse, PaginatedResponse, TiketServisItem, DokumentasiFoto, StatusTiket } from '../types/api';

export interface TiketListParams {
  status?: string;
  teknisi_id?: number;
  my_tickets?: boolean;
  search?: string;
  dari?: string;
  sampai?: string;
  page?: number;
  per_page?: number;
}

export interface CreateTiketPayload {
  pelanggan_id?: number | null;
  nama_pelanggan?: string;
  no_telp?: string;
  alamat?: string;
  perangkat: string;
  imei_sn?: string;
  kelengkapan?: string;
  keluhan: string;
  kondisi_awal: string;
  estimasi_biaya?: number;
  estimasi_selesai?: string;
  teknisi_id?: number;
}

export const tiketService = {
  async getTiketList(params?: TiketListParams): Promise<PaginatedResponse<TiketServisItem>> {
    const response = await api.get<PaginatedResponse<TiketServisItem>>('/tiket', { params });
    return response.data;
  },

  async getTiketDetail(id: number): Promise<TiketServisItem> {
    const response = await api.get<ApiResponse<TiketServisItem>>(`/tiket/${id}`);
    return response.data.data;
  },

  async createTiket(payload: CreateTiketPayload): Promise<TiketServisItem> {
    const response = await api.post<ApiResponse<TiketServisItem>>('/tiket', payload);
    return response.data.data;
  },

  async updateTiket(id: number, payload: Partial<CreateTiketPayload>): Promise<TiketServisItem> {
    const response = await api.put<ApiResponse<TiketServisItem>>(`/tiket/${id}`, payload);
    return response.data.data;
  },

  async updateStatus(id: number, data: { status: StatusTiket; catatan?: string }): Promise<TiketServisItem> {
    const response = await api.patch<ApiResponse<TiketServisItem>>(`/tiket/${id}/status`, data);
    return response.data.data;
  },

  async scanTiket(code: string): Promise<TiketServisItem> {
    const response = await api.get<ApiResponse<TiketServisItem>>(`/tiket/scan/${encodeURIComponent(code)}`);
    return response.data.data;
  },

  async getTandaTerima(id: number): Promise<{ cetak_url: string; tiket: TiketServisItem }> {
    const response = await api.get<ApiResponse<any>>(`/tiket/${id}/tanda-terima`);
    return response.data.data;
  },

  async uploadDokumentasi(
    tiketId: number,
    formData: FormData
  ): Promise<DokumentasiFoto> {
    const response = await api.post<ApiResponse<DokumentasiFoto>>(
      `/tiket/${tiketId}/dokumentasi`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data.data;
  },

  async deleteDokumentasi(fotoId: number): Promise<void> {
    await api.delete(`/tiket/dokumentasi/${fotoId}`);
  },
};
