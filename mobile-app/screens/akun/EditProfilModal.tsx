import React, { useState, useEffect } from 'react';
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
import { useAuthStore } from '../../store/useAuthStore';

interface EditProfilModalProps {
  visible: boolean;
  onClose: () => void;
}

export const EditProfilModal: React.FC<EditProfilModalProps> = ({ visible, onClose }) => {
  const user = useAuthStore((state) => state.user);
  const setUser = useAuthStore((state) => state.setUser);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [username, setUsername] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (user) {
      setName(user.name || '');
      setEmail(user.email || '');
      setUsername(user.username || '');
    }
  }, [user, visible]);

  const handleSave = async () => {
    if (!name.trim() || !email.trim() || !username.trim()) {
      Alert.alert('Perhatian', 'Nama, email, dan username wajib diisi.');
      return;
    }

    setLoading(true);
    try {
      const updatedUser = await authService.updateProfile({
        name: name.trim(),
        email: email.trim(),
        username: username.trim(),
      });
      setUser(updatedUser);
      Alert.alert('Sukses', 'Profil berhasil diperbarui!');
      onClose();
    } catch (err: any) {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal mengubah profil.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal visible={visible} transparent animationType="slide" onRequestClose={onClose}>
      <View style={styles.overlay}>
        <View style={styles.modalBox}>
          <View style={styles.header}>
            <Text style={styles.headerTitle}>Ubah Profil Saya</Text>
            <TouchableOpacity onPress={onClose}>
              <Ionicons name="close" size={22} color={Colors.textSecondary} />
            </TouchableOpacity>
          </View>

          <View style={styles.body}>
            <Input
              label="Nama Lengkap *"
              value={name}
              onChangeText={setName}
            />
            <Input
              label="Alamat Email *"
              value={email}
              onChangeText={setEmail}
              keyboardType="email-address"
              autoCapitalize="none"
            />
            <Input
              label="Username Akun *"
              value={username}
              onChangeText={setUsername}
              autoCapitalize="none"
            />

            <Button
              title="Simpan Perubahan"
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
