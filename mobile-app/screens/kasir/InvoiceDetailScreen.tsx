import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  TouchableOpacity, 
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
import { invoiceService } from '../../services/invoiceService';
import { PembayaranModal } from './PembayaranModal';

interface InvoiceDetailScreenProps {
  navigation: any;
  route: any;
}

export const InvoiceDetailScreen: React.FC<InvoiceDetailScreenProps> = ({ navigation, route }) => {
  const { id } = route.params;
  const queryClient = useQueryClient();

  const [showPayModal, setShowPayModal] = useState(false);

  const { data: invoice, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['invoiceDetail', id],
    queryFn: () => invoiceService.getInvoiceDetail(id),
  });

  const voidMutation = useMutation({
    mutationFn: (alasan: string) => invoiceService.voidInvoice(id, alasan),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['invoiceDetail', id] });
      queryClient.invalidateQueries({ queryKey: ['invoiceList'] });
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      Alert.alert('Sukses', 'Invoice berhasil dibatalkan (void). Stok sparepart telah dikembalikan.');
    },
    onError: (err: any) => {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal membatalkan invoice.');
    },
  });

  const handleSharePdf = async () => {
    if (!invoice?.pdf_url) {
      Alert.alert('Perhatian', 'URL cetak PDF tidak tersedia.');
      return;
    }
    try {
      if (await Sharing.isAvailableAsync()) {
        const localUri = FileSystem.cacheDirectory + `Invoice-${invoice.no_invoice}.pdf`;
        const downloadRes = await FileSystem.downloadAsync(invoice.pdf_url, localUri);
        await Sharing.shareAsync(downloadRes.uri, {
          mimeType: 'application/pdf',
          dialogTitle: `Kirim Invoice ${invoice.no_invoice}`,
        });
      } else {
        Linking.openURL(invoice.pdf_url);
      }
    } catch {
      Alert.alert('Gagal', 'Tidak dapat membuka PDF invoice.');
    }
  };

  const handleVoidPrompt = () => {
    Alert.prompt
      ? Alert.prompt(
          'Batalkan (Void) Invoice',
          'Tuliskan alasan pembatalan transaksi ini:',
          [
            { text: 'Tutup', style: 'cancel' },
            {
              text: 'Ya, Batalkan',
              style: 'destructive',
              onPress: (text?: string) => {
                if (!text?.trim()) {
                  Alert.alert('Gagal', 'Alasan pembatalan wajib diisi.');
                  return;
                }
                voidMutation.mutate(text.trim());
              },
            },
          ]
        )
      : Alert.alert(
          'Batalkan (Void) Invoice',
          'Apakah Anda yakin ingin membatalkan transaksi ini? Stok sparepart akan dikembalikan ke sistem.',
          [
            { text: 'Batal', style: 'cancel' },
            {
              text: 'Ya, Batalkan',
              style: 'destructive',
              onPress: () => voidMutation.mutate('Pembatalan transaksi oleh kasir/pelanggan'),
            },
          ]
        );
  };

  if (isLoading || !invoice) {
    return (
      <SafeAreaView style={styles.container}>
        <Header title="Detail Invoice" onBack={() => navigation.goBack()} />
        <View style={styles.centerBox}>
          <Text style={styles.loadingText}>Memuat rincian invoice...</Text>
        </View>
      </SafeAreaView>
    );
  }

  const isLunas = invoice.status === 'paid';
  const isVoid = invoice.status === 'void';

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title={invoice.no_invoice} 
        onBack={() => navigation.goBack()}
        rightAction={
          <TouchableOpacity onPress={handleSharePdf} style={styles.headerBtn}>
            <Ionicons name="print-outline" size={20} color={Colors.primary} />
          </TouchableOpacity>
        }
      />

      <ScrollView
        contentContainerStyle={styles.content}
        refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} colors={[Colors.primary]} />}
      >
        {/* Status Banner */}
        <View style={styles.statusBanner}>
          <View>
            <Text style={styles.bannerCustomer}>{invoice.nama_pelanggan}</Text>
            <Text style={styles.bannerTiket}>Tiket Servis: SRV-#{invoice.tiket_id}</Text>
          </View>
          <StatusBadge status={invoice.status} type="invoice" />
        </View>

        {/* Action Buttons */}
        {!isLunas && !isVoid && (
          <View style={styles.actionRow}>
            <Button
              title="Catat Pembayaran"
              onPress={() => setShowPayModal(true)}
              style={{ flex: 1, marginRight: 8 }}
              icon={<Ionicons name="card-outline" size={18} color="#FFFFFF" />}
            />
            <Button
              title="Void"
              variant="danger"
              onPress={handleVoidPrompt}
              style={{ width: 80 }}
            />
          </View>
        )}

        {/* Items Table Card */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Rincian Item Tagihan</Text>
          {invoice.detail_items?.map((item, idx) => (
            <View key={item.id || idx} style={styles.itemRow}>
              <View style={{ flex: 1 }}>
                <Text style={styles.itemDesc}>{item.deskripsi}</Text>
                <Text style={styles.itemQtyPrice}>
                  {item.qty} x {formatRupiah(item.harga_satuan)}
                </Text>
              </View>
              <Text style={styles.itemSubtotal}>
                {formatRupiah((item.jumlah !== undefined ? item.jumlah : item.qty * item.harga_satuan))}
              </Text>
            </View>
          ))}

          <View style={styles.divider} />

          {/* Breakdown calculation */}
          <View style={styles.calcRow}>
            <Text style={styles.calcLabel}>Subtotal</Text>
            <Text style={styles.calcVal}>{formatRupiah(invoice.subtotal)}</Text>
          </View>

          {invoice.diskon_nominal > 0 && (
            <View style={styles.calcRow}>
              <Text style={[styles.calcLabel, { color: Colors.danger }]}>Diskon</Text>
              <Text style={[styles.calcVal, { color: Colors.danger }]}>- {formatRupiah(invoice.diskon_nominal)}</Text>
            </View>
          )}

          {invoice.pajak_nominal > 0 && (
            <View style={styles.calcRow}>
              <Text style={styles.calcLabel}>Pajak ({invoice.pajak_persen}%)</Text>
              <Text style={styles.calcVal}>+ {formatRupiah(invoice.pajak_nominal)}</Text>
            </View>
          )}

          <View style={styles.divider} />

          <View style={styles.calcRow}>
            <Text style={styles.totalLabel}>Total Tagihan</Text>
            <Text style={styles.totalVal}>{formatRupiah(invoice.total_tagihan)}</Text>
          </View>

          <View style={styles.calcRow}>
            <Text style={styles.calcLabel}>Total Sudah Dibayar</Text>
            <Text style={[styles.calcVal, { color: Colors.success, fontWeight: '700' }]}>
              {formatRupiah(invoice.total_dibayar)}
            </Text>
          </View>

          {!isLunas && !isVoid && (
            <View style={styles.calcRow}>
              <Text style={[styles.totalLabel, { color: Colors.danger }]}>Sisa Tagihan</Text>
              <Text style={[styles.totalVal, { color: Colors.danger }]}>
                {formatRupiah(invoice.sisa_tagihan)}
              </Text>
            </View>
          )}
        </View>

        {/* Payments History Card */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Histori Pembayaran Masuk</Text>
          {!invoice.pembayaran || invoice.pembayaran.length === 0 ? (
            <Text style={styles.emptyText}>Belum ada pembayaran yang tercatat.</Text>
          ) : (
            invoice.pembayaran.map((p, idx) => (
              <View key={p.id || idx} style={styles.paymentItem}>
                <View style={{ flex: 1 }}>
                  <Text style={styles.payMethod}>Metode: {p.metode_bayar.toUpperCase()}</Text>
                  <Text style={styles.payDate}>
                    {p.tanggal_bayar?.substring(0, 16).replace('T', ' ')}
                    {p.dicatat_oleh ? ` • oleh ${p.dicatat_oleh}` : ''}
                  </Text>
                </View>
                <Text style={styles.payAmount}>{formatRupiah(p.jumlah_dibayar)}</Text>
              </View>
            ))
          )}
        </View>

        {/* Action Share PDF */}
        <Button
          title="Cetak / Bagikan Struk PDF"
          variant="outline"
          onPress={handleSharePdf}
          icon={<Ionicons name="share-outline" size={18} color={Colors.primary} />}
          style={{ marginTop: 8 }}
        />
      </ScrollView>

      {/* Payment Modal */}
      <PembayaranModal
        visible={showPayModal}
        invoiceId={invoice.id}
        sisaTagihan={invoice.sisa_tagihan}
        onClose={() => setShowPayModal(false)}
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
  centerBox: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingText: {
    fontSize: 14,
    color: Colors.textSecondary,
  },
  headerBtn: {
    padding: 6,
  },
  statusBanner: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 14,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  bannerCustomer: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  bannerTiket: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  actionRow: {
    flexDirection: 'row',
    marginBottom: 12,
  },
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 16,
    marginBottom: 12,
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 10,
  },
  itemRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 6,
  },
  itemDesc: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  itemQtyPrice: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  itemSubtotal: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  divider: {
    height: 1,
    backgroundColor: '#F1F5F9',
    marginVertical: 8,
  },
  calcRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 3,
  },
  calcLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  calcVal: {
    fontSize: 13,
    color: Colors.textPrimary,
    fontWeight: '500',
  },
  totalLabel: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  totalVal: {
    fontSize: 17,
    fontWeight: '800',
    color: Colors.primary,
  },
  paymentItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
    paddingVertical: 8,
  },
  payMethod: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  payDate: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  payAmount: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.success,
  },
  emptyText: {
    fontSize: 12,
    color: Colors.textMuted,
    fontStyle: 'italic',
  },
});
