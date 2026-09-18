import { api } from './api';
import { ApiResponse, PaginatedResponse, InvoiceDetail, InvoiceItem } from '../types/api';

export interface InvoiceListParams {
  status?: 'unpaid' | 'partial' | 'paid' | 'void';
  search?: string;
  dari?: string;
  sampai?: string;
  page?: number;
  per_page?: number;
}

export interface CreateInvoicePayload {
  tiket_id: number;
  items: Array<{
    produk_id: number | null;
    deskripsi: string;
    qty: number;
    harga_satuan: number;
    harga_modal_satuan: number;
  }>;
  diskon?: number;
  pajak_id?: number;
  keterangan?: string;
}

export interface CatatBayarPayload {
  jumlah_dibayar: number;
  metode_bayar: 'tunai' | 'transfer' | 'qris' | 'debit' | 'kredit';
  tanggal_bayar?: string;
}

export const invoiceService = {
  async getInvoiceList(params?: InvoiceListParams): Promise<PaginatedResponse<InvoiceDetail>> {
    const response = await api.get<PaginatedResponse<InvoiceDetail>>('/invoice', { params });
    return response.data;
  },

  async getInvoiceDetail(id: number): Promise<InvoiceDetail> {
    const response = await api.get<ApiResponse<InvoiceDetail>>(`/invoice/${id}`);
    return response.data.data;
  },

  async createInvoice(payload: CreateInvoicePayload): Promise<InvoiceDetail> {
    const response = await api.post<ApiResponse<InvoiceDetail>>('/invoice', payload);
    return response.data.data;
  },

  async catatBayar(id: number, data: FormData | CatatBayarPayload): Promise<InvoiceDetail> {
    const isFormData = typeof FormData !== 'undefined' && data instanceof FormData;
    const response = await api.post<ApiResponse<InvoiceDetail>>(
      `/invoice/${id}/bayar`,
      data,
      isFormData
        ? {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          }
        : undefined
    );
    return response.data.data;
  },

  async voidInvoice(id: number, alasan: string): Promise<InvoiceDetail> {
    const response = await api.put<ApiResponse<InvoiceDetail>>(`/invoice/${id}/void`, { alasan });
    return response.data.data;
  }
};
