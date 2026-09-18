import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  TouchableOpacity, 
  Alert 
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { useAuthStore } from '../../store/useAuthStore';
import { masterService } from '../../services/masterService';
import { EditProfilModal } from './EditProfilModal';
import { GantiPasswordModal } from './GantiPasswordModal';

interface AkunScreenProps {
  navigation: any;
}

export const AkunScreen: React.FC<AkunScreenProps> = ({ navigation }) => {
  const user = useAuthStore((state) => state.user);
  const serverUrl = useAuthStore((state) => state.serverUrl);
  const logout = useAuthStore((state) => state.logout);

  const [showEditProfil, setShowEditProfil] = useState(false);
  const [showGantiPass, setShowGantiPass] = useState(false);

  const { data: perusahaan } = useQuery({
    queryKey: ['perusahaanInfo'],
    queryFn: () => masterService.getPerusahaan(),
  });

  const handleLogout = () => {
    Alert.alert(
      'Konfirmasi Keluar',
      'Apakah Anda yakin ingin keluar dari akun ini?',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Keluar',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ]
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        {/* Profile Card */}
        <View style={styles.profileCard}>
          <View style={styles.avatar}>
            <Ionicons name="person" size={36} color={Colors.primary} />
          </View>
          <Text style={styles.name}>{user?.name || 'Pengguna'}</Text>
          <Text style={styles.email}>{user?.email}</Text>
          <View style={styles.roleBadge}>
            <Text style={styles.roleText}>{user?.roles?.[0] || 'teknisi'}</Text>
          </View>
        </View>

        {/* Store Info Card */}
        {perusahaan && (
          <View style={styles.storeCard}>
            <View style={styles.storeHeader}>
              <Ionicons name="storefront-outline" size={18} color={Colors.primary} style={{ marginRight: 8 }} />
              <Text style={styles.storeName}>{perusahaan.nama_perusahaan}</Text>
            </View>
            {perusahaan.alamat ? <Text style={styles.storeDesc}>{perusahaan.alamat}</Text> : null}
            {perusahaan.telp ? <Text style={styles.storeDesc}>Telp/WA: {perusahaan.telp}</Text> : null}
          </View>
        )}

        {/* Menu Section */}
        <Text style={styles.sectionTitle}>Pengaturan Akun</Text>
        <View style={styles.menuCard}>
          <TouchableOpacity 
            style={styles.menuItem}
            onPress={() => setShowEditProfil(true)}
          >
            <View style={styles.menuIconCircle}>
              <Ionicons name="person-outline" size={18} color={Colors.primary} />
            </View>
            <Text style={styles.menuLabel}>Ubah Profil Saya</Text>
            <Ionicons name="chevron-forward" size={18} color={Colors.textMuted} />
          </TouchableOpacity>

          <View style={styles.divider} />

          <TouchableOpacity 
            style={styles.menuItem}
            onPress={() => setShowGantiPass(true)}
          >
            <View style={[styles.menuIconCircle, { backgroundColor: '#FEF3C7' }]}>
              <Ionicons name="lock-closed-outline" size={18} color="#D97706" />
            </View>
            <Text style={styles.menuLabel}>Ganti Kata Sandi</Text>
            <Ionicons name="chevron-forward" size={18} color={Colors.textMuted} />
          </TouchableOpacity>

          <View style={styles.divider} />

          <TouchableOpacity 
            style={styles.menuItem}
            onPress={() => navigation.navigate('ServerConfig')}
          >
            <View style={[styles.menuIconCircle, { backgroundColor: '#EFF6FF' }]}>
              <Ionicons name="server-outline" size={18} color={Colors.primary} />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.menuLabel}>Alamat Server Backend</Text>
              <Text style={styles.menuSubLabel} numberOfLines={1}>{serverUrl}</Text>
            </View>
            <Ionicons name="chevron-forward" size={18} color={Colors.textMuted} />
          </TouchableOpacity>
        </View>

        {/* Logout Button */}
        <TouchableOpacity style={styles.logoutBtn} onPress={handleLogout}>
          <Ionicons name="log-out-outline" size={20} color={Colors.danger} style={{ marginRight: 8 }} />
          <Text style={styles.logoutText}>Keluar dari Akun</Text>
        </TouchableOpacity>

        <Text style={styles.footerVersion}>
          Wahyu Teknik Indotama Mobile • v1.0.0
        </Text>
      </ScrollView>

      <EditProfilModal
        visible={showEditProfil}
        onClose={() => setShowEditProfil(false)}
      />

      <GantiPasswordModal
        visible={showGantiPass}
        onClose={() => setShowGantiPass(false)}
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
  profileCard: {
    backgroundColor: Colors.surface,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 20,
    alignItems: 'center',
    marginBottom: 14,
  },
  avatar: {
    width: 72,
    height: 72,
    borderRadius: 36,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
    borderWidth: 1,
    borderColor: '#BFDBFE',
  },
  name: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  email: {
    fontSize: 13,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  roleBadge: {
    backgroundColor: Colors.primaryLight,
    paddingHorizontal: 10,
    paddingVertical: 3,
    borderRadius: 6,
    marginTop: 8,
  },
  roleText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.primary,
    textTransform: 'uppercase',
  },
  storeCard: {
    backgroundColor: '#EFF6FF',
    borderRadius: 12,
    padding: 14,
    borderWidth: 1,
    borderColor: '#BFDBFE',
    marginBottom: 16,
  },
  storeHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  storeName: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primaryDark,
  },
  storeDesc: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 8,
  },
  menuCard: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    marginBottom: 20,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 14,
  },
  menuIconCircle: {
    width: 34,
    height: 34,
    borderRadius: 17,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  menuLabel: {
    flex: 1,
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  menuSubLabel: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  divider: {
    height: 1,
    backgroundColor: '#F8FAFC',
    marginLeft: 60,
  },
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FEF2F2',
    borderWidth: 1,
    borderColor: '#FECACA',
    borderRadius: 10,
    paddingVertical: 12,
  },
  logoutText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.danger,
  },
  footerVersion: {
    textAlign: 'center',
    fontSize: 12,
    color: Colors.textMuted,
    marginTop: 20,
  },
});
