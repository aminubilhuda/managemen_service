import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  Modal, 
  TouchableOpacity, 
  Alert 
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { authService } from '../../services/authService';

interface GantiPasswordModalProps {
  visible: boolean;
  onClose: () => void;
}

export const GantiPasswordModal: React.FC<GantiPasswordModalProps> = ({ visible, onClose }) => {
  const [currentPassword, setCurrentPassword] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSave = async () => {
    if (!currentPassword || !newPassword || !confirmPassword) {
      Alert.alert('Perhatian', 'Semua kolom kata sandi wajib diisi.');
      return;
    }
    if (newPassword !== confirmPassword) {
      Alert.alert('Perhatian', 'Konfirmasi kata sandi baru tidak cocok.');
      return;
    }
    if (newPassword.length < 6) {
      Alert.alert('Perhatian', 'Kata sandi baru minimal 6 karakter.');
      return;
    }

    setLoading(true);
    try {
      await authService.changePassword({
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
      });
      Alert.alert('Sukses', 'Kata sandi berhasil diubah!');
      setCurrentPassword('');
      setNewPassword('');
      setConfirmPassword('');
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal', err?.response?.data?.message || 'Kata sandi saat ini salah.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Ganti Kata Sandi</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <View style={styles.body}>
            <Input
              label="Kata Sandi Saat Ini *"
              value={currentPassword}
              onChangeText={setCurrentPassword}
              isPassword
            />
            <Input
              label="Kata Sandi Baru *"
              value={newPassword}
              onChangeText={setNewPassword}
              isPassword
            />
            <Input
              label="Konfirmasi Kata Sandi Baru *"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
              isPassword
            />

            <Button
              title="Perbarui Kata Sandi"
              onPress={handleSave}
              loading={loading}
              size="large"
              style={{ marginTop: 10 }}
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
});
