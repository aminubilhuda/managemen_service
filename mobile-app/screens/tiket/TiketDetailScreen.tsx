import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  TouchableOpacity, 
  Image, 
  Alert, 
  Linking, 
  RefreshControl 
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import * as Sharing from 'expo-sharing';
import * as FileSystem from 'expo-file-system/legacy';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { StatusBadge } from '../../components/StatusBadge';
import { Button } from '../../components/Button';
import { formatRupiah } from '../../components/CurrencyInput';
import { tiketService } from '../../services/tiketService';
import { StatusUpdateModal } from './StatusUpdateModal';
import { DokumentasiUploadModal } from './DokumentasiUploadModal';
import { useAuthStore } from '../../store/useAuthStore';

interface TiketDetailScreenProps {
  navigation: any;
  route: any;
}

export const TiketDetailScreen: React.FC<TiketDetailScreenProps> = ({ navigation, route }) => {
  const { id } = route.params;
  const queryClient = useQueryClient();
  const user = useAuthStore((state) => state.user);
  const isAdminOrKasir = user?.roles?.some(r => ['super-admin', 'admin'].includes(r));

  const [showStatusModal, setShowStatusModal] = useState(false);
  const [showUploadModal, setShowUploadModal] = useState(false);
  const [previewImage, setPreviewImage] = useState<string | null>(null);

  const { data: tiket, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['tiketDetail', id],
    queryFn: () => tiketService.getTiketDetail(id),
  });

  const deletePhotoMutation = useMutation({
    mutationFn: (fotoId: number) => tiketService.deleteDokumentasi(fotoId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['tiketDetail', id] });
      Alert.alert('Sukses', 'Foto dokumentasi berhasil dihapus.');
    },
    onError: (err: any) => {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal menghapus foto.');
    },
  });

  const handleCall = (phone?: string) => {
    if (!phone) return;
    Linking.openURL(`tel:${phone}`);
  };

  const handleWhatsApp = (phone?: string) => {
    if (!phone) return;
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
      cleanPhone = '62' + cleanPhone.substring(1);
    }
    const message = encodeURIComponent(
      `Halo Kak ${tiket?.pelanggan?.nama_pelanggan || tiket?.nama_pelanggan}, kami dari Wahyu Teknik Indotama menginformasikan progres servis unit ${tiket?.perangkat} (No. Tiket: ${tiket?.no_tiket}). Status saat ini: ${tiket?.status_label}.`
    );
    Linking.openURL(`https://wa.me/${cleanPhone}?text=${message}`);
  };

  const handleShareTandaTerima = async () => {
    try {
      const res = await tiketService.getTandaTerima(id);
      if (res?.cetak_url) {
        if (await Sharing.isAvailableAsync()) {
          // Download temporary file and share
          const localUri = FileSystem.cacheDirectory + `Tanda-Terima-${tiket?.no_tiket}.pdf`;
          const downloadRes = await FileSystem.downloadAsync(res.cetak_url, localUri);
          await Sharing.shareAsync(downloadRes.uri, {
            mimeType: 'application/pdf',
            dialogTitle: `Bagikan Tanda Terima ${tiket?.no_tiket}`,
          });
        } else {
          // Open web view or browser
          Linking.openURL(res.cetak_url);
        }
      }
    } catch (err: any) {
      Alert.alert('Gagal', 'Tidak dapat memuat tanda terima PDF.');
    }
  };

  if (isLoading || !tiket) {
    return (
      <SafeAreaView style={styles.container}>
        <Header title="Detail Tiket Servis" onBack={() => navigation.goBack()} />
        <View style={styles.loadingContainer}>
          <Text style={styles.loadingText}>Memuat data tiket...</Text>
        </View>
      </SafeAreaView>
    );
  }

  const customerName = tiket.pelanggan?.nama_pelanggan || tiket.nama_pelanggan || 'Pelanggan';
  const customerPhone = tiket.pelanggan?.no_telp || tiket.no_telp || '';
  const customerAddress = tiket.pelanggan?.alamat || '';

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title={tiket.no_tiket}
        subtitle={tiket.status_label}
        onBack={() => navigation.goBack()} 
        rightAction={
          <TouchableOpacity onPress={handleShareTandaTerima} style={styles.headerShareBtn}>
            <Ionicons name="share-social-outline" size={20} color={Colors.primary} />
          </TouchableOpacity>
        }
      />

      <ScrollView 
        contentContainerStyle={styles.content}
        refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} colors={[Colors.primary]} />}
      >
        {/* Status Card Banner */}
        <View style={styles.statusBanner}>
          <View style={{ flex: 1 }}>
            <Text style={styles.statusBannerLabel}>Status Pengerjaan Saat Ini:</Text>
            <Text style={styles.statusBannerValue}>{tiket.status_label}</Text>
          </View>
          <StatusBadge status={tiket.status} label={tiket.status_label} />
        </View>

        {/* Action Buttons for Technician */}
        <View style={styles.mainActionRow}>
          <Button
            title="Update Status"
            onPress={() => setShowStatusModal(true)}
            icon={<Ionicons name="sync-outline" size={18} color="#FFFFFF" />}
            style={{ flex: 1, marginRight: 8 }}
          />
          <Button
            title="Tambah Foto"
            variant="secondary"
            onPress={() => setShowUploadModal(true)}
            icon={<Ionicons name="camera-outline" size={18} color={Colors.primary} />}
            style={{ flex: 1 }}
          />
        </View>

        {/* Customer Information Card */}
        <View style={styles.card}>
          <View style={styles.cardHeader}>
            <Text style={styles.cardTitle}>Data Pelanggan</Text>
            <View style={styles.contactActions}>
              {customerPhone ? (
                <>
                  <TouchableOpacity 
                    style={[styles.contactCircle, { backgroundColor: '#ECFDF5' }]} 
                    onPress={() => handleWhatsApp(customerPhone)}
                  >
                    <Ionicons name="logo-whatsapp" size={18} color="#059669" />
                  </TouchableOpacity>
                  <TouchableOpacity 
                    style={[styles.contactCircle, { backgroundColor: '#EFF6FF', marginLeft: 8 }]} 
                    onPress={() => handleCall(customerPhone)}
                  >
                    <Ionicons name="call-outline" size={18} color={Colors.primary} />
                  </TouchableOpacity>
                </>
              ) : null}
            </View>
          </View>
          <Text style={styles.infoName}>{customerName}</Text>
          <Text style={styles.infoText}>{customerPhone || 'Tidak ada nomor telepon'}</Text>
          {customerAddress ? <Text style={styles.infoSubText}>{customerAddress}</Text> : null}
        </View>

        {/* Device Information Card */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Informasi Perangkat & Kerusakan</Text>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Tipe HP</Text>
            <Text style={styles.infoVal}>{tiket.perangkat}</Text>
          </View>

          {tiket.imei_sn ? (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>IMEI / SN</Text>
              <Text style={styles.infoVal}>{tiket.imei_sn}</Text>
            </View>
          ) : null}

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Kelengkapan</Text>
            <Text style={styles.infoVal}>{tiket.kelengkapan || '-'}</Text>
          </View>

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Teknisi</Text>
            <Text style={styles.infoVal}>{tiket.teknisi?.name || 'Belum ditugaskan'}</Text>
          </View>

          <View style={styles.divider} />

          <Text style={styles.subTitle}>Keluhan Kerusakan:</Text>
          <Text style={styles.boxText}>{tiket.keluhan}</Text>

          <Text style={[styles.subTitle, { marginTop: 10 }]}>Kondisi Awal Fisik:</Text>
          <Text style={styles.boxText}>{tiket.kondisi_awal || '-'}</Text>

          <View style={styles.divider} />

          <View style={styles.infoRow}>
            <Text style={styles.infoLabel}>Estimasi Biaya</Text>
            <Text style={[styles.infoVal, { fontWeight: '700', color: Colors.primary }]}>
              {tiket.estimasi_biaya ? formatRupiah(tiket.estimasi_biaya) : '-'}
            </Text>
          </View>

          {tiket.estimasi_selesai && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Estimasi Selesai</Text>
              <Text style={styles.infoVal}>{tiket.estimasi_selesai}</Text>
            </View>
          )}

          {tiket.garansi_sampai && (
            <View style={styles.infoRow}>
              <Text style={styles.infoLabel}>Garansi Aktif Sampai</Text>
              <Text style={[styles.infoVal, { color: Colors.success, fontWeight: '600' }]}>
                {tiket.garansi_sampai}
              </Text>
            </View>
          )}
        </View>

        {/* Documentation Gallery Card */}
        <View style={styles.card}>
          <View style={styles.cardHeader}>
            <Text style={styles.cardTitle}>Foto Dokumentasi Unit ({tiket.dokumentasi?.length || 0})</Text>
            <TouchableOpacity onPress={() => setShowUploadModal(true)}>
              <Text style={styles.addPhotoLink}>+ Ambil Foto</Text>
            </TouchableOpacity>
          </View>

          {!tiket.dokumentasi || tiket.dokumentasi.length === 0 ? (
            <Text style={styles.emptyGalleryText}>Belum ada foto dokumentasi sebelum/sesudah pengerjaan.</Text>
          ) : (
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.photoScroll}>
              {tiket.dokumentasi.map((foto) => (
                <View key={foto.id} style={styles.photoContainer}>
                  <TouchableOpacity onPress={() => setPreviewImage(foto.foto_url)}>
                    <Image source={{ uri: foto.foto_url }} style={styles.photoThumb} />
                  </TouchableOpacity>
                  <View style={styles.photoInfoBadge}>
                    <Text style={styles.photoTypeText}>{foto.tipe_dokumentasi}</Text>
                  </View>
                  <TouchableOpacity 
                    style={styles.deletePhotoBtn}
                    onPress={() => {
                      Alert.alert(
                        'Hapus Foto',
                        'Apakah Anda yakin ingin menghapus foto dokumentasi ini?',
                        [
                          { text: 'Batal', style: 'cancel' },
                          { text: 'Hapus', style: 'destructive', onPress: () => deletePhotoMutation.mutate(foto.id) }
                        ]
                      );
                    }}
                  >
                    <Ionicons name="trash" size={14} color="#FFFFFF" />
                  </TouchableOpacity>
                </View>
              ))}
            </ScrollView>
          )}
        </View>

        {/* Invoice & Billing Section */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Status Invoice Kasir</Text>
          {tiket.invoice_id ? (
            <View style={styles.invoiceBox}>
              <View style={{ flex: 1 }}>
                <Text style={styles.invoiceNoText}>{tiket.no_invoice}</Text>
                <Text style={styles.invoiceStatusDesc}>
                  Status: {tiket.status_invoice?.toUpperCase() || 'UNPAID'}
                </Text>
              </View>
              <Button
                title="Lihat Invoice"
                size="small"
                onPress={() => navigation.navigate('InvoiceDetail', { id: tiket.invoice_id })}
              />
            </View>
          ) : (
            <View style={styles.noInvoiceBox}>
              <Text style={styles.noInvoiceText}>Belum ada invoice kasir untuk servis ini.</Text>
              {isAdminOrKasir && (
                <Button
                  title="Buat Invoice Billing"
                  size="small"
                  variant="outline"
                  onPress={() => navigation.navigate('InvoiceCreate', { tiketId: tiket.id })}
                  style={{ marginTop: 8 }}
                />
              )}
            </View>
          )}
        </View>

        {/* Timeline Status History */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Riwayat Perubahan Status (Timeline)</Text>
          {!tiket.riwayat_status || tiket.riwayat_status.length === 0 ? (
            <Text style={styles.emptyTimelineText}>Belum ada catatan riwayat status.</Text>
          ) : (
            <View style={styles.timelineList}>
              {tiket.riwayat_status.map((item, idx) => (
                <View key={item.id || idx} style={styles.timelineItem}>
                  <View style={styles.timelineDot} />
                  {idx < (tiket.riwayat_status?.length || 0) - 1 && <View style={styles.timelineLine} />}
                  <View style={styles.timelineContent}>
                    <Text style={styles.timelineStatus}>{item.status_label}</Text>
                    {item.catatan ? <Text style={styles.timelineCatatan}>"{item.catatan}"</Text> : null}
                    <Text style={styles.timelineMeta}>
                      Oleh {item.diubah_oleh} • {item.created_at?.substring(0, 16).replace('T', ' ')}
                    </Text>
                  </View>
                </View>
              ))}
            </View>
          )}
        </View>
      </ScrollView>

      {/* Modals */}
      <StatusUpdateModal
        visible={showStatusModal}
        tiketId={tiket.id}
        currentStatus={tiket.status}
        onClose={() => setShowStatusModal(false)}
        onSuccess={() => refetch()}
      />

      <DokumentasiUploadModal
        visible={showUploadModal}
        tiketId={tiket.id}
        onClose={() => setShowUploadModal(false)}
        onSuccess={() => refetch()}
      />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  content: {
    padding: 16,
    paddingBottom: 40,
  },
  loadingContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingText: {
    fontSize: 14,
    color: Colors.textSecondary,
  },
  headerShareBtn: {
    padding: 6,
  },
  statusBanner: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 14,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  statusBannerLabel: {
    fontSize: 11,
    color: Colors.textSecondary,
  },
  statusBannerValue: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginTop: 2,
  },
  mainActionRow: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 14,
    marginBottom: 12,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 8,
  },
  contactActions: {
    flexDirection: 'row',
  },
  contactCircle: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
  },
  infoName: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  infoText: {
    fontSize: 13,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  infoSubText: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 5,
  },
  infoLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  infoVal: {
    fontSize: 13,
    color: Colors.textPrimary,
    fontWeight: '500',
  },
  divider: {
    height: 1,
    backgroundColor: '#F1F5F9',
    marginVertical: 10,
  },
  subTitle: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginBottom: 4,
  },
  boxText: {
    fontSize: 13,
    color: Colors.textPrimary,
    backgroundColor: '#F8FAFC',
    padding: 8,
    borderRadius: 6,
    lineHeight: 18,
  },
  addPhotoLink: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.primary,
  },
  emptyGalleryText: {
    fontSize: 12,
    color: Colors.textMuted,
    fontStyle: 'italic',
  },
  photoScroll: {
    flexDirection: 'row',
  },
  photoContainer: {
    marginRight: 10,
    position: 'relative',
  },
  photoThumb: {
    width: 100,
    height: 100,
    borderRadius: 8,
  },
  photoInfoBadge: {
    position: 'absolute',
    bottom: 4,
    left: 4,
    backgroundColor: 'rgba(0,0,0,0.6)',
    paddingHorizontal: 4,
    paddingVertical: 2,
    borderRadius: 4,
  },
  photoTypeText: {
    fontSize: 9,
    color: '#FFFFFF',
    textTransform: 'capitalize',
  },
  deletePhotoBtn: {
    position: 'absolute',
    top: 4,
    right: 4,
    backgroundColor: Colors.danger,
    width: 22,
    height: 22,
    borderRadius: 11,
    alignItems: 'center',
    justifyContent: 'center',
  },
  invoiceBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primaryLight,
    padding: 12,
    borderRadius: 8,
  },
  invoiceNoText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primaryDark,
  },
  invoiceStatusDesc: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  noInvoiceBox: {
    paddingVertical: 8,
  },
  noInvoiceText: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  emptyTimelineText: {
    fontSize: 12,
    color: Colors.textMuted,
    fontStyle: 'italic',
  },
  timelineList: {
    paddingLeft: 6,
    marginTop: 4,
  },
  timelineItem: {
    flexDirection: 'row',
    paddingBottom: 14,
    position: 'relative',
  },
  timelineDot: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: Colors.primary,
    marginTop: 4,
    marginRight: 12,
  },
  timelineLine: {
    position: 'absolute',
    left: 4,
    top: 14,
    bottom: 0,
    width: 2,
    backgroundColor: '#E2E8F0',
  },
  timelineContent: {
    flex: 1,
  },
  timelineStatus: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  timelineCatatan: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontStyle: 'italic',
    marginTop: 2,
  },
  timelineMeta: {
    fontSize: 11,
    color: Colors.textMuted,
    marginTop: 2,
  },
});
