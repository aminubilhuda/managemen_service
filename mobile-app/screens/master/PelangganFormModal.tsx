import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  Modal, 
  TouchableOpacity, 
  ScrollView, 
  Alert 
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { pelangganService } from '../../services/pelangganService';
import { Pelanggan } from '../../types/api';

interface PelangganFormModalProps {
  visible: boolean;
  editData?: Pelanggan | null;
  onClose: () => void;
  onSuccess: () => void;
}

export const PelangganFormModal: React.FC<PelangganFormModalProps> = ({
  visible,
  editData,
  onClose,
  onSuccess,
}) => {
  const [nama, setNama] = useState('');
  const [noTelp, setNoTelp] = useState('');
  const [alamat, setAlamat] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (editData) {
      setNama(editData.nama_pelanggan || '');
      setNoTelp(editData.no_telp || '');
      setAlamat(editData.alamat || '');
    } else {
      setNama('');
      setNoTelp('');
      setAlamat('');
    }
  }, [editData, visible]);

  const handleSave = async () => {
    if (!nama.trim() || !noTelp.trim()) {
      Alert.alert('Perhatian', 'Nama dan No. Telepon/WA pelanggan wajib diisi.');
      return;
    }

    setLoading(true);
    try {
      if (editData) {
        await pelangganService.updatePelanggan(editData.id, {
          nama_pelanggan: nama.trim(),
          no_telp: noTelp.trim(),
          alamat: alamat.trim() || undefined,
        });
        Alert.alert('Sukses', 'Data pelanggan berhasil diperbarui!');
      } else {
        await pelangganService.createPelanggan({
          nama_pelanggan: nama.trim(),
          no_telp: noTelp.trim(),
          alamat: alamat.trim() || undefined,
        });
        Alert.alert('Sukses', 'Pelanggan baru berhasil ditambahkan!');
      }

      onSuccess();
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal menyimpan data pelanggan.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>
              {editData ? 'Edit Data Pelanggan' : 'Tambah Pelanggan Baru'}
            </Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.body} keyboardShouldPersistTaps="handled">
            <Input
              label="Nama Pelanggan *"
              placeholder="Contoh: Rina Wijaya"
              value={nama}
              onChangeText={setNama}
            />

            <Input
              label="Nomor WhatsApp / Telepon *"
              placeholder="081234567890"
              keyboardType="phone-pad"
              value={noTelp}
              onChangeText={setNoTelp}
            />

            <Input
              label="Alamat (Opsional)"
              placeholder="Jl. Diponegoro No. 8"
              value={alamat}
              onChangeText={setAlamat}
            />

            <Button
              title={editData ? 'Simpan Perubahan' : 'Tambah Pelanggan'}
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
    maxHeight: '80%',
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
});
