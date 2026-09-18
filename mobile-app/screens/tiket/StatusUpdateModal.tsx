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
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { tiketService } from '../../services/tiketService';
import { StatusTiket } from '../../types/api';

interface StatusUpdateModalProps {
  visible: boolean;
  tiketId: number;
  currentStatus: StatusTiket;
  onClose: () => void;
  onSuccess: () => void;
}

const STATUS_OPTIONS: Array<{ value: StatusTiket; label: string; desc: string }> = [
  { value: 'diterima', label: 'Diterima', desc: 'Unit baru diterima di meja intake' },
  { value: 'dicek', label: 'Sedang Dicek', desc: 'Pengecekan mendalam oleh teknisi' },
  { value: 'menunggu_sparepart', label: 'Menunggu Sparepart', desc: 'Menunggu kiriman komponen/LCD dari distributor' },
  { value: 'dikerjakan', label: 'Sedang Dikerjakan', desc: 'Proses pembongkaran & perbaikan aktif' },
  { value: 'selesai', label: 'Selesai (Siap Diambil)', desc: 'Servis rampung, unit siap diserahkan (garansi aktif)' },
  { value: 'diambil', label: 'Sudah Diambil Pelanggan', desc: 'Pelanggan telah mengambil unit di toko' },
  { value: 'batal', label: 'Dibatalkan', desc: 'Servis dibatalkan oleh pelanggan/tidak dapat diperbaiki' },
];

export const StatusUpdateModal: React.FC<StatusUpdateModalProps> = ({
  visible,
  tiketId,
  currentStatus,
  onClose,
  onSuccess,
}) => {
  const queryClient = useQueryClient();
  const [selectedStatus, setSelectedStatus] = useState<StatusTiket>(currentStatus);
  const [catatan, setCatatan] = useState('');

  const updateMutation = useMutation({
    mutationFn: () => tiketService.updateStatus(tiketId, {
      status: selectedStatus,
      catatan: catatan.trim() || undefined,
    }),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['tiketDetail', tiketId] });
      queryClient.invalidateQueries({ queryKey: ['tiketList'] });
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      Alert.alert('Sukses', 'Status servis berhasil diperbarui.');
      onSuccess();
      onClose();
    },
    onError: (err: any) => {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal mengubah status.');
    },
  });

  return (
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Perbarui Status Pengerjaan</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView style={styles.body} showsVerticalScrollIndicator={false}>
            <Text style={styles.label}>Pilih Status Baru:</Text>
            {STATUS_OPTIONS.map((opt) => {
              const isSelected = selectedStatus === opt.value;
              return (
                <TouchableOpacity
                  key={opt.value}
                  style={[styles.statusOption, isSelected && styles.statusOptionActive]}
                  onPress={() => setSelectedStatus(opt.value)}
                >
                  <View style={styles.optionRadio}>
                    {isSelected && <View style={styles.radioInner} />}
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={[styles.optionLabel, isSelected && styles.optionLabelActive]}>
                      {opt.label}
                    </Text>
                    <Text style={styles.optionDesc}>{opt.desc}</Text>
                  </View>
                </TouchableOpacity>
              );
            })}

            <Input
              label="Catatan Perkembangan (Opsional)"
              placeholder="Misal: Selesai ganti LCD, running test baterai..."
              value={catatan}
              onChangeText={setCatatan}
              multiline
              numberOfLines={2}
              style={{ height: 60, textAlignVertical: 'top', paddingTop: 8 }}
            />
          </ScrollView>

          <View style={styles.footer}>
            <Button
              title="Simpan Perubahan"
              onPress={() => updateMutation.mutate()}
              loading={updateMutation.isPending}
            />
          </View>
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
    paddingBottom: 20,
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
  statusOption: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    marginBottom: 8,
    backgroundColor: '#FFFFFF',
  },
  statusOptionActive: {
    borderColor: Colors.primary,
    backgroundColor: Colors.primaryLight,
  },
  optionRadio: {
    width: 18,
    height: 18,
    borderRadius: 9,
    borderWidth: 2,
    borderColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  radioInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: Colors.primary,
  },
  optionLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  optionLabelActive: {
    color: Colors.primaryDark,
  },
  optionDesc: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  footer: {
    paddingHorizontal: 16,
    paddingTop: 8,
  },
});
