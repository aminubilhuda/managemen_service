export const Colors = {
  // Brand & Primary
  primary: '#2563EB',        // Blue 600
  primaryDark: '#1D4ED8',    // Blue 700
  primaryLight: '#EFF6FF',   // Blue 50
  
  // Neutral / Greyscale
  background: '#F8FAFC',     // Slate 50
  surface: '#FFFFFF',        // Putih murni
  surfaceBorder: '#E2E8F0',  // Slate 200
  
  // Teks
  textPrimary: '#0F172A',    // Slate 900
  textSecondary: '#64748B',  // Slate 500
  textMuted: '#94A3B8',      // Slate 400
  
  // State Status Servis & Badge
  status: {
    diterima: { bg: '#F1F5F9', text: '#475569', border: '#CBD5E1', label: 'Diterima' },
    dicek: { bg: '#EFF6FF', text: '#2563EB', border: '#BFDBFE', label: 'Sedang Dicek' },
    menunggu_sparepart: { bg: '#FFFBEB', text: '#D97706', border: '#FDE68A', label: 'Menunggu Sparepart' },
    dikerjakan: { bg: '#F5F3FF', text: '#7C3AED', border: '#DDD6FE', label: 'Sedang Dikerjakan' },
    selesai: { bg: '#ECFDF5', text: '#059669', border: '#A7F3D0', label: 'Selesai' },
    diambil: { bg: '#E0F2FE', text: '#0284C7', border: '#BAE6FD', label: 'Sudah Diambil' },
    batal: { bg: '#FEF2F2', text: '#DC2626', border: '#FECACA', label: 'Dibatalkan' }
  },
  
  // Status Pembayaran Invoice
  payment: {
    unpaid: { bg: '#FEF2F2', text: '#DC2626', border: '#FECACA', label: 'Belum Lunas' },
    partial: { bg: '#FFFBEB', text: '#D97706', border: '#FDE68A', label: 'Sebagian' },
    paid: { bg: '#ECFDF5', text: '#059669', border: '#A7F3D0', label: 'Lunas' },
    void: { bg: '#F1F5F9', text: '#64748B', border: '#CBD5E1', label: 'Dibatalkan (Void)' }
  },
  
  // Feedback
  success: '#10B981',
  warning: '#F59E0B',
  danger: '#EF4444',
  info: '#3B82F6'
};
