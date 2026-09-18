import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  Modal, 
  TouchableOpacity, 
  ScrollView, 
  Image, 
  Alert 
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { CurrencyInput, formatRupiah } from '../../components/CurrencyInput';
import { Button } from '../../components/Button';
import { invoiceService } from '../../services/invoiceService';

interface PembayaranModalProps {
  visible: boolean;
  invoiceId: number;
  sisaTagihan: number;
  onClose: () => void;
  onSuccess: () => void;
}

const METODE_OPTIONS: Array<{ id: 'tunai' | 'transfer' | 'qris' | 'debit' | 'kredit'; label: string; icon: string }> = [
  { id: 'tunai', label: 'Tunai (Cash)', icon: 'cash-outline' },
  { id: 'qris', label: 'QRIS', icon: 'qr-code-outline' },
  { id: 'transfer', label: 'Transfer Bank', icon: 'swap-horizontal-outline' },
  { id: 'debit', label: 'Kartu Debit', icon: 'card-outline' },
  { id: 'kredit', label: 'Kartu Kredit', icon: 'card-outline' },
];

export const PembayaranModal: React.FC<PembayaranModalProps> = ({
  visible,
  invoiceId,
  sisaTagihan,
  onClose,
  onSuccess,
}) => {
  const [jumlah, setJumlah] = useState<number>(sisaTagihan || 0);
  const [metode, setMetode] = useState<'tunai' | 'transfer' | 'qris' | 'debit' | 'kredit'>('tunai');
  const [buktiUri, setBuktiUri] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  // Sync initial amount whenever modal becomes visible
  React.useEffect(() => {
    if (visible) {
      setJumlah(sisaTagihan);
      setBuktiUri(null);
    }
  }, [visible, sisaTagihan]);

  const pickBukti = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.8,
    });
    if (!result.canceled && result.assets && result.assets[0]) {
      setBuktiUri(result.assets[0].uri);
    }
  };

  const handlePay = async () => {
    if (jumlah <= 0) {
      Alert.alert('Perhatian', 'Jumlah pembayaran harus lebih dari 0.');
      return;
    }
    if (jumlah > sisaTagihan) {
      Alert.alert('Perhatian', `Jumlah dibayar (${formatRupiah(jumlah)}) melebihi sisa tagihan (${formatRupiah(sisaTagihan)}).`);
      return;
    }

    setLoading(true);
    try {
      if (buktiUri) {
        const formData = new FormData();
        formData.append('jumlah_dibayar', jumlah.toString());
        formData.append('metode_bayar', metode);

        const filename = buktiUri.split('/').pop() || 'bukti.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image/jpeg`;

        // @ts-ignore
        formData.append('bukti_bayar', {
          uri: buktiUri,
          name: filename,
          type: type,
        });

        await invoiceService.catatBayar(invoiceId, formData);
      } else {
        await invoiceService.catatBayar(invoiceId, {
          jumlah_dibayar: jumlah,
          metode_bayar: metode,
        });
      }

      Alert.alert('Sukses', 'Pembayaran berhasil dicatat!');
      onSuccess();
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal Bayar', err?.response?.data?.message || 'Terjadi kesalahan saat memproses pembayaran.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Catat Pembayaran Kasir</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.body}>
            <View style={styles.sisaBox}>
              <Text style={styles.sisaLabel}>Sisa Tagihan Belum Lunas:</Text>
              <Text style={styles.sisaValue}>{formatRupiah(sisaTagihan)}</Text>
            </View>

            <CurrencyInput
              label="Nominal Dibayar (Rp) *"
              value={jumlah}
              onChangeValue={setJumlah}
            />

            {/* Quick full pay button */}
            <TouchableOpacity 
              style={styles.fullPayBtn} 
              onPress={() => setJumlah(sisaTagihan)}
            >
              <Text style={styles.fullPayText}>Bayar Lunas Penuh ({formatRupiah(sisaTagihan)})</Text>
            </TouchableOpacity>

            <Text style={styles.label}>Pilih Metode Pembayaran:</Text>
            <View style={styles.metodeList}>
              {METODE_OPTIONS.map((m) => {
                const isSelected = metode === m.id;
                return (
                  <TouchableOpacity
                    key={m.id}
                    style={[styles.metodeChip, isSelected && styles.metodeChipActive]}
                    onPress={() => setMetode(m.id)}
                  >
                    <Ionicons 
                      name={m.icon as any} 
                      size={18} 
                      color={isSelected ? '#FFFFFF' : Colors.textPrimary} 
                      style={{ marginRight: 8 }}
                    />
                    <Text style={[styles.metodeText, isSelected && styles.metodeTextActive]}>
                      {m.label}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>

            {/* Receipt upload (optional) */}
            <Text style={styles.label}>Bukti Struk EDC / Transfer (Opsional):</Text>
            {buktiUri ? (
              <View style={styles.buktiPreview}>
                <Image source={{ uri: buktiUri }} style={styles.buktiThumb} />
                <TouchableOpacity onPress={() => setBuktiUri(null)} style={styles.removeBukti}>
                  <Ionicons name="close-circle" size={20} color={Colors.danger} />
                </TouchableOpacity>
              </View>
            ) : (
              <TouchableOpacity style={styles.uploadBuktiBox} onPress={pickBukti}>
                <Ionicons name="camera-outline" size={24} color={Colors.primary} />
                <Text style={styles.uploadBuktiText}>Unggah Foto Bukti Transfer</Text>
              </TouchableOpacity>
            )}

            <Button
              title="Konfirmasi Pembayaran"
              onPress={handlePay}
              loading={loading}
              size="large"
              style={{ marginTop: 14, marginBottom: 20 }}
              icon={<Ionicons name="checkmark-circle-outline" size={20} color="#FFFFFF" />}
            />
          </ScrollView>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalBox: {
    backgroundColor: Colors.surface,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    maxHeight: '85%',
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  body: {
    padding: 16,
  },
  sisaBox: {
    backgroundColor: '#FEF2F2',
    borderColor: '#FECACA',
    borderWidth: 1,
    padding: 12,
    borderRadius: 8,
    marginBottom: 14,
  },
  sisaLabel: {
    fontSize: 12,
    color: Colors.danger,
  },
  sisaValue: {
    fontSize: 18,
    fontWeight: '800',
    color: Colors.danger,
    marginTop: 2,
  },
  fullPayBtn: {
    alignSelf: 'flex-start',
    backgroundColor: Colors.primaryLight,
    paddingVertical: 4,
    paddingHorizontal: 8,
    borderRadius: 6,
    marginTop: -8,
    marginBottom: 14,
  },
  fullPayText: {
    fontSize: 11,
    fontWeight: '600',
    color: Colors.primaryDark,
  },
  label: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 8,
  },
  metodeList: {
    marginBottom: 14,
  },
  metodeChip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    marginBottom: 6,
    backgroundColor: '#FFFFFF',
  },
  metodeChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  metodeText: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  metodeTextActive: {
    color: '#FFFFFF',
  },
  uploadBuktiBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    padding: 14,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    borderStyle: 'dashed',
    marginBottom: 14,
  },
  uploadBuktiText: {
    fontSize: 13,
    color: Colors.primary,
    fontWeight: '600',
    marginLeft: 8,
  },
  buktiPreview: {
    position: 'relative',
    alignSelf: 'flex-start',
    marginBottom: 14,
  },
  buktiThumb: {
    width: 100,
    height: 100,
    borderRadius: 8,
  },
  removeBukti: {
    position: 'absolute',
    top: -6,
    right: -6,
    backgroundColor: '#FFFFFF',
    borderRadius: 10,
  },
});
