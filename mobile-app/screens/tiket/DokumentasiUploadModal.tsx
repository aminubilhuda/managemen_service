import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  Modal, 
  TouchableOpacity, 
  Image, 
  Alert 
} from 'react-native';
import * as ImagePicker from 'expo-image-picker';
import * as ImageManipulator from 'expo-image-manipulator';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { tiketService } from '../../services/tiketService';

interface DokumentasiUploadModalProps {
  visible: boolean;
  tiketId: number;
  onClose: () => void;
  onSuccess: () => void;
}

const DOK_TYPES = [
  { id: 'kondisi_masuk', label: 'Sebelum (Masuk)' },
  { id: 'pengerjaan', label: 'Proses Pengerjaan' },
  { id: 'kondisi_selesai', label: 'Sesudah (Selesai)' },
];

export const DokumentasiUploadModal: React.FC<DokumentasiUploadModalProps> = ({
  visible,
  tiketId,
  onClose,
  onSuccess,
}) => {
  const [selectedType, setSelectedType] = useState<string>('kondisi_masuk');
  const [imageUri, setImageUri] = useState<string | null>(null);
  const [keterangan, setKeterangan] = useState('');
  const [uploading, setUploading] = useState(false);

  const takePhoto = async () => {
    const { status } = await ImagePicker.requestCameraPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Izin Ditolak', 'Izin akses kamera diperlukan untuk mengambil foto.');
      return;
    }

    const result = await ImagePicker.launchCameraAsync({
      mediaTypes: ['images'],
      allowsEditing: true,
      quality: 0.8,
    });

    if (!result.canceled && result.assets && result.assets[0]) {
      processImage(result.assets[0].uri);
    }
  };

  const pickImage = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Izin Ditolak', 'Izin akses galeri diperlukan.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      allowsEditing: true,
      quality: 0.8,
    });

    if (!result.canceled && result.assets && result.assets[0]) {
      processImage(result.assets[0].uri);
    }
  };

  const processImage = async (uri: string) => {
    try {
      // Compress and resize max width 1280px
      const manipResult = await ImageManipulator.manipulateAsync(
        uri,
        [{ resize: { width: 1280 } }],
        { compress: 0.8, format: ImageManipulator.SaveFormat.JPEG }
      );
      setImageUri(manipResult.uri);
    } catch (e) {
      setImageUri(uri);
    }
  };

  const handleUpload = async () => {
    if (!imageUri) {
      Alert.alert('Foto Belum Dipilih', 'Silakan ambil foto dari kamera atau pilih dari galeri.');
      return;
    }

    setUploading(true);
    try {
      const formData = new FormData();
      const filename = imageUri.split('/').pop() || 'photo.jpg';
      const match = /\.(\w+)$/.exec(filename);
      const type = match ? `image/${match[1]}` : `image/jpeg`;

      // @ts-ignore
      formData.append('foto', {
        uri: imageUri,
        name: filename,
        type: type,
      });
      formData.append('tipe_dokumentasi', selectedType);
      if (keterangan.trim()) {
        formData.append('keterangan', keterangan.trim());
      }

      await tiketService.uploadDokumentasi(tiketId, formData);
      Alert.alert('Sukses', 'Foto dokumentasi berhasil diunggah.');
      setImageUri(null);
      setKeterangan('');
      onSuccess();
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal Upload', err?.response?.data?.message || 'Gagal mengunggah foto.');
    } finally {
      setUploading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Unggah Foto Dokumentasi</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <View style={styles.body}>
            {/* Type selector */}
            <Text style={styles.label}>Tipe Foto:</Text>
            <View style={styles.typesRow}>
              {DOK_TYPES.map((t) => {
                const isSelected = selectedType === t.id;
                return (
                  <TouchableOpacity
                    key={t.id}
                    style={[styles.typeChip, isSelected && styles.typeChipActive]}
                    onPress={() => setSelectedType(t.id)}
                  >
                    <Text style={[styles.typeText, isSelected && styles.typeTextActive]}>
                      {t.label}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>

            {/* Photo preview / picker buttons */}
            {imageUri ? (
              <View style={styles.previewContainer}>
                <Image source={{ uri: imageUri }} style={styles.previewImage} />
                <TouchableOpacity 
                  style={styles.retakeBtn} 
                  onPress={() => setImageUri(null)}
                >
                  <Ionicons name="trash-outline" size={16} color="#FFFFFF" style={{ marginRight: 4 }} />
                  <Text style={styles.retakeText}>Ganti Foto</Text>
                </TouchableOpacity>
              </View>
            ) : (
              <View style={styles.pickerContainer}>
                <TouchableOpacity style={styles.pickerOption} onPress={takePhoto}>
                  <View style={styles.pickerIconCircle}>
                    <Ionicons name="camera" size={28} color={Colors.primary} />
                  </View>
                  <Text style={styles.pickerLabel}>Buka Kamera</Text>
                </TouchableOpacity>

                <TouchableOpacity style={styles.pickerOption} onPress={pickImage}>
                  <View style={[styles.pickerIconCircle, { backgroundColor: '#F0FDF4' }]}>
                    <Ionicons name="images" size={28} color={Colors.success} />
                  </View>
                  <Text style={styles.pickerLabel}>Pilih dari Galeri</Text>
                </TouchableOpacity>
              </View>
            )}

            <Input
              label="Keterangan Foto (Opsional)"
              placeholder="Misal: Kondisi LCD retak saat diterima"
              value={keterangan}
              onChangeText={setKeterangan}
            />

            <Button
              title="Unggah Foto"
              onPress={handleUpload}
              loading={uploading}
              disabled={!imageUri}
              style={{ marginTop: 8 }}
              icon={<Ionicons name="cloud-upload-outline" size={18} color="#FFFFFF" />}
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
    paddingBottom: 24,
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
  typesRow: {
    flexDirection: 'row',
    marginBottom: 16,
  },
  typeChip: {
    flex: 1,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    alignItems: 'center',
    marginHorizontal: 3,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  typeChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  typeText: {
    fontSize: 11,
    fontWeight: '600',
    color: Colors.textSecondary,
    textAlign: 'center',
  },
  typeTextActive: {
    color: '#FFFFFF',
  },
  pickerContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 18,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    borderStyle: 'dashed',
    borderRadius: 10,
    marginBottom: 14,
  },
  pickerOption: {
    alignItems: 'center',
  },
  pickerIconCircle: {
    width: 54,
    height: 54,
    borderRadius: 27,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 6,
  },
  pickerLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  previewContainer: {
    alignItems: 'center',
    marginBottom: 14,
  },
  previewImage: {
    width: '100%',
    height: 180,
    borderRadius: 10,
  },
  retakeBtn: {
    position: 'absolute',
    bottom: 8,
    right: 8,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(0,0,0,0.7)',
    paddingVertical: 4,
    paddingHorizontal: 10,
    borderRadius: 6,
  },
  retakeText: {
    color: '#FFFFFF',
    fontSize: 12,
    fontWeight: '600',
  },
});
