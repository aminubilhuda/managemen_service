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
import { Input } from '../../components/Input';
import { CurrencyInput } from '../../components/CurrencyInput';
import { Button } from '../../components/Button';
import { pengeluaranService } from '../../services/pengeluaranService';

interface PengeluaranCreateModalProps {
  visible: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

const KATEGORI_OPTIONS = [
  'operasional',
  'belanja_sparepart',
  'listrik_internet',
  'konsumsi',
  'peralatan_alat_servis',
  'lainnya',
];

export const PengeluaranCreateModal: React.FC<PengeluaranCreateModalProps> = ({
  visible,
  onClose,
  onSuccess,
}) => {
  const [kategori, setKategori] = useState('operasional');
  const [deskripsi, setDeskripsi] = useState('');
  const [jumlah, setJumlah] = useState<number>(0);
  const [tanggal, setTanggal] = useState(new Date().toISOString().split('T')[0]);
  const [buktiUri, setBuktiUri] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const pickBukti = async () => {
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.8,
    });
    if (!result.canceled && result.assets && result.assets[0]) {
      setBuktiUri(result.assets[0].uri);
    }
  };

  const handleSave = async () => {
    if (!deskripsi.trim()) {
      Alert.alert('Perhatian', 'Keterangan atau deskripsi pengeluaran wajib diisi.');
      return;
    }
    if (jumlah <= 0) {
      Alert.alert('Perhatian', 'Nominal pengeluaran harus lebih dari 0.');
      return;
    }

    setLoading(true);
    try {
      const formData = new FormData();
      formData.append('kategori_pengeluaran', kategori);
      formData.append('deskripsi', deskripsi.trim());
      formData.append('jumlah', jumlah.toString());
      formData.append('tanggal', tanggal);

      if (buktiUri) {
        const filename = buktiUri.split('/').pop() || 'struk.jpg';
        const match = /\.(\w+)$/.exec(filename);
        const type = match ? `image/${match[1]}` : `image/jpeg`;

        // @ts-ignore
        formData.append('bukti', {
          uri: buktiUri,
          name: filename,
          type: type,
        });
      }

      await pengeluaranService.createPengeluaran(formData);
      Alert.alert('Sukses', 'Pengeluaran berhasil dicatat!');
      setDeskripsi('');
      setJumlah(0);
      setBuktiUri(null);
      onSuccess();
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal menyimpan pengeluaran.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Catat Kas Keluar / Pengeluaran</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.body} keyboardShouldPersistTaps="handled">
            <Text style={styles.label}>Kategori Pengeluaran:</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.kategoriRow}>
              {KATEGORI_OPTIONS.map((kat) => {
                const isSelected = kategori === kat;
                return (
                  <TouchableOpacity
                    key={kat}
                    style={[styles.kategoriChip, isSelected && styles.kategoriChipActive]}
                    onPress={() => setKategori(kat)}
                  >
                    <Text style={[styles.kategoriChipText, isSelected && styles.kategoriChipTextActive]}>
                      {kat.replace('_', ' ')}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>

            <Input
              label="Keterangan Pengeluaran *"
              placeholder="Contoh: Beli timah solder & alkohol 96%"
              value={deskripsi}
              onChangeText={setDeskripsi}
            />

            <CurrencyInput
              label="Nominal Pengeluaran (Rp) *"
              value={jumlah}
              onChangeValue={setJumlah}
            />

            <Input
              label="Tanggal (YYYY-MM-DD)"
              value={tanggal}
              onChangeText={setTanggal}
            />

            <Text style={styles.label}>Foto Nota / Struk Pembelian (Opsional):</Text>
            {buktiUri ? (
              <View style={styles.previewBox}>
                <Image source={{ uri: buktiUri }} style={styles.previewImage} />
                <TouchableOpacity onPress={() => setBuktiUri(null)} style={styles.deletePhotoBtn}>
                  <Ionicons name="close" size={16} color="#FFFFFF" />
                </TouchableOpacity>
              </View>
            ) : (
              <TouchableOpacity style={styles.uploadBox} onPress={pickBukti}>
                <Ionicons name="camera-outline" size={24} color={Colors.primary} />
                <Text style={styles.uploadText}>Ambil Foto Struk Nota</Text>
              </TouchableOpacity>
            )}

            <Button
              title="Simpan Kas Keluar"
              onPress={handleSave}
              loading={loading}
              size="large"
              style={{ marginTop: 14, marginBottom: 20 }}
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
  label: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 8,
  },
  kategoriRow: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  kategoriChip: {
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    marginRight: 6,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  kategoriChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  kategoriChipText: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontWeight: '500',
    textTransform: 'capitalize',
  },
  kategoriChipTextActive: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  uploadBox: {
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
  uploadText: {
    fontSize: 13,
    color: Colors.primary,
    fontWeight: '600',
    marginLeft: 8,
  },
  previewBox: {
    position: 'relative',
    alignSelf: 'flex-start',
    marginBottom: 14,
  },
  previewImage: {
    width: 100,
    height: 100,
    borderRadius: 8,
  },
  deletePhotoBtn: {
    position: 'absolute',
    top: -6,
    right: -6,
    backgroundColor: Colors.danger,
    width: 22,
    height: 22,
    borderRadius: 11,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
