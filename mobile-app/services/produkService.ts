import { api } from './api';
import { ApiResponse, PaginatedResponse, Produk, KategoriProduk } from '../types/api';

export interface ProdukListParams {
  kategori_id?: number;
  tipe?: 'sparepart' | 'jasa';
  stok_menipis?: boolean;
  search?: string;
  page?: number;
  per_page?: number;
}

export const produkService = {
  async getKategoriList(): Promise<KategoriProduk[]> {
    const response = await api.get<ApiResponse<KategoriProduk[]>>('/kategori-produk');
    return response.data.data;
  },

  async getProdukList(params?: ProdukListParams): Promise<PaginatedResponse<Produk>> {
    const response = await api.get<PaginatedResponse<Produk>>('/produk', { params });
    return response.data;
  },

  async searchProduk(q: string): Promise<Produk[]> {
    const response = await api.get<ApiResponse<Produk[]>>('/produk/search', { params: { q } });
    return response.data.data;
  },

  async createProduk(data: {
    kategori_id: number;
    kode_produk: string;
    nama_produk: string;
    tipe: 'sparepart' | 'jasa';
    harga_jual: number;
    harga_modal: number;
    garansi_hari?: number;
    stok?: number;
  }): Promise<Produk> {
    const response = await api.post<ApiResponse<Produk>>('/produk', data);
    return response.data.data;
  },

  async updateProduk(id: number, data: Partial<Produk>): Promise<Produk> {
    const response = await api.put<ApiResponse<Produk>>(`/produk/${id}`, data);
    return response.data.data;
  }
};
