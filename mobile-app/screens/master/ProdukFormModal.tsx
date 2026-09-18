import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  Modal, 
  TouchableOpacity, 
  ScrollView, 
  Alert 
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { CurrencyInput } from '../../components/CurrencyInput';
import { Button } from '../../components/Button';
import { produkService } from '../../services/produkService';
import { KategoriProduk } from '../../types/api';

interface ProdukFormModalProps {
  visible: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export const ProdukFormModal: React.FC<ProdukFormModalProps> = ({
  visible,
  onClose,
  onSuccess,
}) => {
  const [kategoriId, setKategoriId] = useState<number | undefined>();
  const [kodeProduk, setKodeProduk] = useState('');
  const [namaProduk, setNamaProduk] = useState('');
  const [tipe, setTipe] = useState<'sparepart' | 'jasa'>('sparepart');
  const [hargaJual, setHargaJual] = useState<number>(0);
  const [hargaModal, setHargaModal] = useState<number>(0);
  const [garansiHari, setGaransiHari] = useState('30');
  const [stok, setStok] = useState('5');
  const [loading, setLoading] = useState(false);

  const { data: kategoriList = [] } = useQuery({
    queryKey: ['kategoriList'],
    queryFn: () => produkService.getKategoriList(),
    enabled: visible,
  });

  // Set default category if available
  React.useEffect(() => {
    if (kategoriList.length > 0 && !kategoriId) {
      setKategoriId(kategoriList[0].id);
    }
  }, [kategoriList]);

  const handleSave = async () => {
    if (!kodeProduk.trim() || !namaProduk.trim() || !kategoriId) {
      Alert.alert('Perhatian', 'Kategori, Kode Produk, dan Nama Produk wajib diisi.');
      return;
    }
    if (hargaJual <= 0) {
      Alert.alert('Perhatian', 'Harga jual harus lebih dari 0.');
      return;
    }

    setLoading(true);
    try {
      await produkService.createProduk({
        kategori_id: kategoriId,
        kode_produk: kodeProduk.trim(),
        nama_produk: namaProduk.trim(),
        tipe,
        harga_jual: hargaJual,
        harga_modal: hargaModal,
        garansi_hari: garansiHari ? parseInt(garansiHari, 10) : undefined,
        stok: tipe === 'sparepart' ? parseInt(stok || '0', 10) : undefined,
      });

      Alert.alert('Sukses', 'Produk berhasil ditambahkan!');
      setKodeProduk('');
      setNamaProduk('');
      setHargaJual(0);
      setHargaModal(0);
      onSuccess();
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal menyimpan produk.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Tambah Produk / Sparepart</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.body} keyboardShouldPersistTaps="handled">
            {/* Tipe Selector */}
            <Text style={styles.label}>Tipe Item:</Text>
            <View style={styles.tipeRow}>
              <TouchableOpacity
                style={[styles.tipeChip, tipe === 'sparepart' && styles.tipeChipActive]}
                onPress={() => setTipe('sparepart')}
              >
                <Text style={[styles.tipeText, tipe === 'sparepart' && styles.tipeTextActive]}>
                  Suku Cadang (Sparepart)
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.tipeChip, tipe === 'jasa' && styles.tipeChipActive]}
                onPress={() => setTipe('jasa')}
              >
                <Text style={[styles.tipeText, tipe === 'jasa' && styles.tipeTextActive]}>
                  Jasa Servis
                </Text>
              </TouchableOpacity>
            </View>

            {/* Kategori Horizontal Scroll */}
            <Text style={styles.label}>Kategori Produk:</Text>
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.kategoriRow}>
              {kategoriList.map((kat) => {
                const isSelected = kategoriId === kat.id;
                return (
                  <TouchableOpacity
                    key={kat.id}
                    style={[styles.katChip, isSelected && styles.katChipActive]}
                    onPress={() => setKategoriId(kat.id)}
                  >
                    <Text style={[styles.katText, isSelected && styles.katTextActive]}>
                      {kat.nama_kategori}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </ScrollView>

            <Input
              label="Kode Produk / SKU / Barcode *"
              placeholder="Contoh: LCD-SAM-A52"
              value={kodeProduk}
              onChangeText={setKodeProduk}
              autoCapitalize="characters"
            />

            <Input
              label="Nama Produk / Sparepart *"
              placeholder="Contoh: LCD Touchscreen Samsung A52 Original"
              value={namaProduk}
              onChangeText={setNamaProduk}
            />

            <CurrencyInput
              label="Harga Jual ke Pelanggan (Rp) *"
              value={hargaJual}
              onChangeValue={setHargaJual}
            />

            <CurrencyInput
              label="Harga Modal / HPP (Rp)"
              value={hargaModal}
              onChangeValue={setHargaModal}
            />

            {tipe === 'sparepart' && (
              <Input
                label="Jumlah Stok Awal"
                placeholder="5"
                keyboardType="numeric"
                value={stok}
                onChangeText={setStok}
              />
            )}

            <Input
              label="Masa Garansi (Hari)"
              placeholder="30"
              keyboardType="numeric"
              value={garansiHari}
              onChangeText={setGaransiHari}
            />

            <Button
              title="Simpan Produk"
              onPress={handleSave}
              loading={loading}
              size="large"
              style={{ marginTop: 10, marginBottom: 20 }}
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
  tipeRow: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  tipeChip: {
    flex: 1,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    alignItems: 'center',
    marginHorizontal: 3,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  tipeChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  tipeText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  tipeTextActive: {
    color: '#FFFFFF',
  },
  kategoriRow: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  katChip: {
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    marginRight: 6,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  katChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  katText: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  katTextActive: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
});
