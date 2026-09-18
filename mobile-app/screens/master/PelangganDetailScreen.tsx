import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  TouchableOpacity, 
  Linking, 
  RefreshControl 
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { StatusBadge } from '../../components/StatusBadge';
import { pelangganService } from '../../services/pelangganService';
import { PelangganFormModal } from './PelangganFormModal';

interface PelangganDetailScreenProps {
  navigation: any;
  route: any;
}

export const PelangganDetailScreen: React.FC<PelangganDetailScreenProps> = ({ navigation, route }) => {
  const { id } = route.params;
  const [showEditModal, setShowEditModal] = useState(false);

  const { data: pelanggan, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['pelangganDetail', id],
    queryFn: () => pelangganService.getPelangganDetail(id),
  });

  const handleCall = () => {
    if (pelanggan?.no_telp) {
      Linking.openURL(`tel:${pelanggan.no_telp}`);
    }
  };

  const handleWhatsApp = () => {
    if (pelanggan?.no_telp) {
      let clean = pelanggan.no_telp.replace(/[^0-9]/g, '');
      if (clean.startsWith('0')) clean = '62' + clean.substring(1);
      Linking.openURL(`https://wa.me/${clean}`);
    }
  };

  if (isLoading || !pelanggan) {
    return (
      <SafeAreaView style={styles.container}>
        <Header title="Detail Pelanggan" onBack={() => navigation.goBack()} />
        <View style={styles.centerBox}>
          <Text style={styles.loadingText}>Memuat data pelanggan...</Text>
        </View>
      </SafeAreaView>
    );
  }

  const riwayat = pelanggan.riwayat_servis || [];

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title="Detail Pelanggan" 
        onBack={() => navigation.goBack()}
        rightAction={
          <TouchableOpacity onPress={() => setShowEditModal(true)} style={styles.editBtn}>
            <Ionicons name="create-outline" size={20} color={Colors.primary} />
          </TouchableOpacity>
        }
      />

      <ScrollView 
        contentContainerStyle={styles.content}
        refreshControl={<RefreshControl refreshing={isRefetching} onRefresh={refetch} colors={[Colors.primary]} />}
      >
        {/* Customer Profile Card */}
        <View style={styles.profileCard}>
          <View style={styles.avatarCircle}>
            <Ionicons name="person" size={32} color={Colors.primary} />
          </View>
          <Text style={styles.customerName}>{pelanggan.nama_pelanggan}</Text>
          <Text style={styles.customerPhone}>{pelanggan.no_telp}</Text>
          {pelanggan.alamat ? <Text style={styles.customerAddress}>{pelanggan.alamat}</Text> : null}

          <View style={styles.contactRow}>
            <TouchableOpacity style={styles.contactBtn} onPress={handleWhatsApp}>
              <Ionicons name="logo-whatsapp" size={18} color="#059669" style={{ marginRight: 6 }} />
              <Text style={[styles.contactText, { color: '#059669' }]}>WhatsApp</Text>
            </TouchableOpacity>

            <TouchableOpacity style={[styles.contactBtn, { marginLeft: 10 }]} onPress={handleCall}>
              <Ionicons name="call-outline" size={18} color={Colors.primary} style={{ marginRight: 6 }} />
              <Text style={[styles.contactText, { color: Colors.primary }]}>Telepon</Text>
            </TouchableOpacity>
          </View>
        </View>

        {/* Riwayat Servis Section */}
        <Text style={styles.sectionTitle}>Riwayat Servis ({riwayat.length})</Text>
        {riwayat.length === 0 ? (
          <View style={styles.emptyBox}>
            <Text style={styles.emptyText}>Pelanggan ini belum memiliki riwayat servis.</Text>
          </View>
        ) : (
          riwayat.map((t) => (
            <TouchableOpacity
              key={t.id}
              style={styles.ticketCard}
              activeOpacity={0.7}
              onPress={() => navigation.navigate('TiketDetail', { id: t.id })}
            >
              <View style={styles.ticketHeader}>
                <Text style={styles.ticketNo}>{t.no_tiket}</Text>
                <StatusBadge status={t.status} label={t.status_label} />
              </View>
              <Text style={styles.deviceName}>{t.perangkat}</Text>
              <Text style={styles.keluhan} numberOfLines={1}>{t.keluhan}</Text>
              <Text style={styles.ticketDate}>{t.created_at?.substring(0, 10)}</Text>
            </TouchableOpacity>
          ))
        )}
      </ScrollView>

      <PelangganFormModal
        visible={showEditModal}
        editData={pelanggan}
        onClose={() => setShowEditModal(false)}
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
  editBtn: {
    padding: 6,
  },
  profileCard: {
    backgroundColor: Colors.surface,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 20,
    alignItems: 'center',
    marginBottom: 16,
  },
  avatarCircle: {
    width: 68,
    height: 68,
    borderRadius: 34,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 10,
  },
  customerName: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  customerPhone: {
    fontSize: 14,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  customerAddress: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 4,
    textAlign: 'center',
  },
  contactRow: {
    flexDirection: 'row',
    marginTop: 14,
  },
  contactBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F1F5F9',
    paddingVertical: 8,
    paddingHorizontal: 16,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  contactText: {
    fontSize: 13,
    fontWeight: '600',
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 10,
  },
  emptyBox: {
    backgroundColor: Colors.surface,
    padding: 20,
    borderRadius: 10,
    alignItems: 'center',
  },
  emptyText: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  ticketCard: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 14,
    marginBottom: 8,
  },
  ticketHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  ticketNo: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.primary,
  },
  deviceName: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  keluhan: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  ticketDate: {
    fontSize: 11,
    color: Colors.textMuted,
    marginTop: 6,
  },
});
