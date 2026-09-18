import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  FlatList, 
  TouchableOpacity, 
  RefreshControl,
  Switch 
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { SearchBar } from '../../components/SearchBar';
import { EmptyState } from '../../components/EmptyState';
import { formatRupiah } from '../../components/CurrencyInput';
import { pelangganService } from '../../services/pelangganService';
import { produkService } from '../../services/produkService';
import { Pelanggan, Produk } from '../../types/api';
import { PelangganFormModal } from './PelangganFormModal';
import { ProdukFormModal } from './ProdukFormModal';
import { useAuthStore } from '../../store/useAuthStore';

interface MasterScreenProps {
  navigation: any;
}

export const MasterScreen: React.FC<MasterScreenProps> = ({ navigation }) => {
  const [activeTab, setActiveTab] = useState<'pelanggan' | 'produk'>('pelanggan');
  const [searchQuery, setSearchQuery] = useState('');
  const [stokMenipis, setStokMenipis] = useState(false);

  // Modals
  const [showPelangganModal, setShowPelangganModal] = useState(false);
  const [showProdukModal, setShowProdukModal] = useState(false);

  const user = useAuthStore((state) => state.user);
  const isAdmin = user?.roles?.some(r => ['super-admin', 'admin'].includes(r));

  // Fetch Pelanggan
  const { 
    data: pelangganData, 
    isLoading: loadingPelanggan, 
    refetch: refetchPelanggan, 
    isRefetching: refetchingPelanggan 
  } = useQuery({
    queryKey: ['pelangganList', searchQuery],
    queryFn: () => pelangganService.getPelangganList({ search: searchQuery.trim() || undefined }),
    enabled: activeTab === 'pelanggan',
  });

  // Fetch Produk
  const { 
    data: produkData, 
    isLoading: loadingProduk, 
    refetch: refetchProduk, 
    isRefetching: refetchingProduk 
  } = useQuery({
    queryKey: ['produkList', searchQuery, stokMenipis],
    queryFn: () => produkService.getProdukList({
      search: searchQuery.trim() || undefined,
      stok_menipis: stokMenipis ? true : undefined,
    }),
    enabled: activeTab === 'produk',
  });

  const pelangganList = pelangganData?.data || [];
  const produkList = produkData?.data || [];

  const renderPelangganItem = ({ item }: { item: Pelanggan }) => (
    <TouchableOpacity
      style={styles.card}
      activeOpacity={0.7}
      onPress={() => navigation.navigate('PelangganDetail', { id: item.id })}
    >
      <View style={styles.cardHeader}>
        <Text style={styles.cardTitle}>{item.nama_pelanggan}</Text>
        <Ionicons name="chevron-forward" size={18} color={Colors.textMuted} />
      </View>
      <Text style={styles.cardSubtitle}>
        <Ionicons name="call-outline" size={12} color={Colors.textSecondary} /> {item.no_telp}
      </Text>
      {item.alamat ? (
        <Text style={styles.cardAddress} numberOfLines={1}>
          <Ionicons name="location-outline" size={12} color={Colors.textSecondary} /> {item.alamat}
        </Text>
      ) : null}
    </TouchableOpacity>
  );

  const renderProdukItem = ({ item }: { item: Produk }) => {
    const isLowStock = item.tipe === 'sparepart' && item.stok !== null && item.stok !== undefined && item.stok <= 3;

    return (
      <View style={styles.card}>
        <View style={styles.cardHeader}>
          <View style={{ flex: 1 }}>
            <Text style={styles.skuText}>{item.kode_produk}</Text>
            <Text style={styles.cardTitle}>{item.nama_produk}</Text>
          </View>
          <View style={[styles.tipeBadge, item.tipe === 'jasa' ? styles.jasaBadge : styles.partBadge]}>
            <Text style={[styles.tipeText, item.tipe === 'jasa' ? styles.jasaText : styles.partText]}>
              {item.tipe.toUpperCase()}
            </Text>
          </View>
        </View>

        <View style={styles.produkFooter}>
          <Text style={styles.hargaJual}>{formatRupiah(item.harga_jual)}</Text>
          {item.tipe === 'sparepart' && (
            <View style={[styles.stokBadge, isLowStock && styles.stokLowBadge]}>
              <Text style={[styles.stokText, isLowStock && styles.stokLowText]}>
                Stok: {item.stok ?? 0} unit
              </Text>
            </View>
          )}
        </View>
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      {/* Top Segmented Tab */}
      <View style={styles.topBar}>
        <View style={styles.segmentContainer}>
          <TouchableOpacity
            style={[styles.segmentBtn, activeTab === 'pelanggan' && styles.segmentBtnActive]}
            onPress={() => {
              setActiveTab('pelanggan');
              setSearchQuery('');
            }}
          >
            <Text style={[styles.segmentText, activeTab === 'pelanggan' && styles.segmentTextActive]}>
              Pelanggan
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.segmentBtn, activeTab === 'produk' && styles.segmentBtnActive]}
            onPress={() => {
              setActiveTab('produk');
              setSearchQuery('');
            }}
          >
            <Text style={[styles.segmentText, activeTab === 'produk' && styles.segmentTextActive]}>
              Sparepart & Jasa
            </Text>
          </TouchableOpacity>
        </View>

        <SearchBar
          value={searchQuery}
          onChangeText={setSearchQuery}
          placeholder={activeTab === 'pelanggan' ? 'Cari nama, no HP...' : 'Cari nama sparepart, kode SKU...'}
        />

        {/* Filter Stok Menipis (Only for Produk) */}
        {activeTab === 'produk' && (
          <View style={styles.stokFilterRow}>
            <Text style={styles.stokFilterLabel}>Tampilkan Hanya Stok Menipis (≤ 3)</Text>
            <Switch
              value={stokMenipis}
              onValueChange={setStokMenipis}
              trackColor={{ false: '#CBD5E1', true: Colors.primary }}
              thumbColor="#FFFFFF"
            />
          </View>
        )}
      </View>

      {activeTab === 'pelanggan' ? (
        <FlatList
          data={pelangganList}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderPelangganItem}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl 
              refreshing={loadingPelanggan || refetchingPelanggan} 
              onRefresh={refetchPelanggan} 
              colors={[Colors.primary]} 
            />
          }
          ListEmptyComponent={
            !loadingPelanggan ? (
              <EmptyState
                title="Tidak Ada Pelanggan"
                description="Belum ada data pelanggan yang sesuai dengan pencarian."
                icon="people-outline"
                actionTitle="+ Tambah Pelanggan"
                onActionPress={() => setShowPelangganModal(true)}
              />
            ) : null
          }
        />
      ) : (
        <FlatList
          data={produkList}
          keyExtractor={(item) => item.id.toString()}
          renderItem={renderProdukItem}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl 
              refreshing={loadingProduk || refetchingProduk} 
              onRefresh={refetchProduk} 
              colors={[Colors.primary]} 
            />
          }
          ListEmptyComponent={
            !loadingProduk ? (
              <EmptyState
                title="Tidak Ada Produk"
                description="Belum ada sparepart atau jasa servis yang sesuai."
                icon="cube-outline"
                actionTitle="+ Tambah Produk"
                onActionPress={() => setShowProdukModal(true)}
              />
            ) : null
          }
        />
      )}

      {/* Floating Action Button */}
      {isAdmin && (
        <TouchableOpacity
          style={styles.fab}
          activeOpacity={0.8}
          onPress={() => {
            if (activeTab === 'pelanggan') {
              setShowPelangganModal(true);
            } else {
              setShowProdukModal(true);
            }
          }}
        >
          <Ionicons name="add" size={28} color="#FFFFFF" />
        </TouchableOpacity>
      )}

      {/* Modals */}
      <PelangganFormModal
        visible={showPelangganModal}
        onClose={() => setShowPelangganModal(false)}
        onSuccess={() => refetchPelanggan()}
      />

      <ProdukFormModal
        visible={showProdukModal}
        onClose={() => setShowProdukModal(false)}
        onSuccess={() => refetchProduk()}
      />
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  topBar: {
    backgroundColor: Colors.surface,
    paddingHorizontal: 16,
    paddingTop: 12,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
  },
  segmentContainer: {
    flexDirection: 'row',
    backgroundColor: '#F1F5F9',
    borderRadius: 8,
    padding: 3,
    marginBottom: 10,
  },
  segmentBtn: {
    flex: 1,
    paddingVertical: 8,
    alignItems: 'center',
    borderRadius: 6,
  },
  segmentBtnActive: {
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  segmentText: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  segmentTextActive: {
    color: Colors.primary,
  },
  stokFilterRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
  },
  stokFilterLabel: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  listContent: {
    padding: 16,
    paddingBottom: 80,
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
    alignItems: 'flex-start',
    marginBottom: 6,
  },
  skuText: {
    fontSize: 11,
    color: Colors.textMuted,
    fontWeight: '600',
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  cardSubtitle: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginBottom: 2,
  },
  cardAddress: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  produkFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 6,
    borderTopWidth: 1,
    borderTopColor: '#F8FAFC',
    paddingTop: 8,
  },
  hargaJual: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primary,
  },
  tipeBadge: {
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
  },
  jasaBadge: {
    backgroundColor: '#EFF6FF',
  },
  partBadge: {
    backgroundColor: '#F3E8FF',
  },
  tipeText: {
    fontSize: 10,
    fontWeight: '700',
  },
  jasaText: {
    color: Colors.primary,
  },
  partText: {
    color: '#7E22CE',
  },
  stokBadge: {
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 4,
  },
  stokLowBadge: {
    backgroundColor: '#FEF2F2',
  },
  stokText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#059669',
  },
  stokLowText: {
    color: Colors.danger,
  },
  fab: {
    position: 'absolute',
    right: 20,
    bottom: 20,
    width: 54,
    height: 54,
    borderRadius: 27,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 6,
    elevation: 4,
  },
});
