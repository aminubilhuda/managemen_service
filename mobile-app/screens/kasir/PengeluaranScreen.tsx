import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  FlatList, 
  TouchableOpacity, 
  Alert, 
  RefreshControl 
} from 'react-native';
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { EmptyState } from '../../components/EmptyState';
import { formatRupiah } from '../../components/CurrencyInput';
import { pengeluaranService } from '../../services/pengeluaranService';
import { Pengeluaran } from '../../types/api';
import { PengeluaranCreateModal } from './PengeluaranCreateModal';

interface PengeluaranScreenProps {
  navigation: any;
}

export const PengeluaranScreen: React.FC<PengeluaranScreenProps> = ({ navigation }) => {
  const queryClient = useQueryClient();
  const [showCreateModal, setShowCreateModal] = useState(false);

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['pengeluaranList'],
    queryFn: () => pengeluaranService.getPengeluaranList({ per_page: 50 }),
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => pengeluaranService.deletePengeluaran(id),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['pengeluaranList'] });
      Alert.alert('Sukses', 'Catatan pengeluaran berhasil dihapus.');
    },
    onError: (err: any) => {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal menghapus pengeluaran.');
    },
  });

  const pengeluaranList = data?.data || [];
  const totalPengeluaran = pengeluaranList.reduce((sum, item) => sum + Number(item.jumlah || 0), 0);

  const handleDelete = (id: number) => {
    Alert.alert(
      'Hapus Pengeluaran',
      'Apakah Anda yakin ingin menghapus catatan kas keluar ini?',
      [
        { text: 'Batal', style: 'cancel' },
        { text: 'Hapus', style: 'destructive', onPress: () => deleteMutation.mutate(id) },
      ]
    );
  };

  const renderItem = ({ item }: { item: Pengeluaran }) => (
    <View style={styles.card}>
      <View style={styles.cardHeader}>
        <View style={styles.kategoriBadge}>
          <Text style={styles.kategoriText}>{item.kategori_pengeluaran.toUpperCase()}</Text>
        </View>
        <TouchableOpacity onPress={() => handleDelete(item.id)} style={styles.deleteBtn}>
          <Ionicons name="trash-outline" size={16} color={Colors.danger} />
        </TouchableOpacity>
      </View>

      <Text style={styles.deskripsi}>{item.deskripsi}</Text>

      <View style={styles.cardFooter}>
        <Text style={styles.tanggal}>{item.tanggal}</Text>
        <Text style={styles.jumlah}>{formatRupiah(item.jumlah)}</Text>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title="Kas Keluar / Pengeluaran" 
        onBack={() => navigation.goBack()}
        rightAction={
          <TouchableOpacity onPress={() => setShowCreateModal(true)} style={styles.addHeaderBtn}>
            <Ionicons name="add" size={24} color={Colors.primary} />
          </TouchableOpacity>
        }
      />

      {/* Summary Total Banner */}
      <View style={styles.totalBanner}>
        <Text style={styles.totalBannerLabel}>Total Pengeluaran Tercatat</Text>
        <Text style={styles.totalBannerVal}>{formatRupiah(totalPengeluaran)}</Text>
      </View>

      <FlatList
        data={pengeluaranList}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl refreshing={isLoading || isRefetching} onRefresh={refetch} colors={[Colors.primary]} />
        }
        ListEmptyComponent={
          !isLoading ? (
            <EmptyState
              title="Belum Ada Pengeluaran"
              description="Catat pengeluaran operasional toko atau belanja sparepart di sini."
              icon="cash-outline"
              actionTitle="+ Catat Kas Keluar"
              onActionPress={() => setShowCreateModal(true)}
            />
          ) : null
        }
      />

      <PengeluaranCreateModal
        visible={showCreateModal}
        onClose={() => setShowCreateModal(false)}
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
  addHeaderBtn: {
    padding: 6,
  },
  totalBanner: {
    backgroundColor: Colors.surface,
    paddingHorizontal: 16,
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  totalBannerLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  totalBannerVal: {
    fontSize: 16,
    fontWeight: '800',
    color: Colors.danger,
  },
  listContent: {
    padding: 16,
    paddingBottom: 40,
  },
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 14,
    marginBottom: 10,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  kategoriBadge: {
    backgroundColor: '#F1F5F9',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
  },
  kategoriText: {
    fontSize: 10,
    fontWeight: '700',
    color: Colors.textSecondary,
  },
  deleteBtn: {
    padding: 4,
  },
  deskripsi: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 8,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F8FAFC',
    paddingTop: 8,
  },
  tanggal: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  jumlah: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.danger,
  },
});
