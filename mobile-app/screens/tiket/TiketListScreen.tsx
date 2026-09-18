import React, { useState, useCallback } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  FlatList, 
  TouchableOpacity, 
  RefreshControl,
  ScrollView
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { SearchBar } from '../../components/SearchBar';
import { StatusBadge } from '../../components/StatusBadge';
import { EmptyState } from '../../components/EmptyState';
import { formatRupiah } from '../../components/CurrencyInput';
import { tiketService, TiketListParams } from '../../services/tiketService';
import { TiketServisItem, StatusTiket } from '../../types/api';
import { useAuthStore } from '../../store/useAuthStore';

interface TiketListScreenProps {
  navigation: any;
  route: any;
}

const TABS = [
  { id: 'all', label: 'Semua' },
  { id: 'my', label: 'Tugas Saya' },
  { id: 'dikerjakan', label: 'Dikerjakan' },
  { id: 'menunggu_sparepart', label: 'Tunggu Part' },
  { id: 'dicek', label: 'Dicek' },
  { id: 'diterima', label: 'Diterima' },
  { id: 'selesai', label: 'Selesai' },
  { id: 'diambil', label: 'Diambil' },
];

export const TiketListScreen: React.FC<TiketListScreenProps> = ({ navigation, route }) => {
  const initialStatus = route?.params?.status || (route?.params?.my_tickets ? 'my' : 'all');
  const [activeTab, setActiveTab] = useState<string>(initialStatus);
  const [search, setSearch] = useState<string>('');
  const [page, setPage] = useState<number>(1);

  const user = useAuthStore((state) => state.user);

  const params: TiketListParams = {
    search: search.trim() || undefined,
    page: 1,
    per_page: 30,
  };

  if (activeTab === 'my') {
    params.my_tickets = true;
  } else if (activeTab !== 'all') {
    params.status = activeTab;
  }

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['tiketList', activeTab, search],
    queryFn: () => tiketService.getTiketList(params),
  });

  const tickets = data?.data || [];

  const renderTicketItem = useCallback(({ item }: { item: TiketServisItem }) => {
    return (
      <TouchableOpacity 
        activeOpacity={0.7}
        onPress={() => navigation.navigate('TiketDetail', { id: item.id })}
        style={styles.card}
      >
        <View style={styles.cardHeader}>
          <Text style={styles.ticketNo}>{item.no_tiket}</Text>
          <StatusBadge status={item.status} label={item.status_label} />
        </View>

        <Text style={styles.customerName}>
          {item.pelanggan?.nama_pelanggan || item.nama_pelanggan || 'Pelanggan'}
          {(item.pelanggan?.no_telp || item.no_telp) ? ` • ${item.pelanggan?.no_telp || item.no_telp}` : ''}
        </Text>

        <View style={styles.deviceRow}>
          <Ionicons name="phone-portrait-outline" size={14} color={Colors.textSecondary} style={{ marginRight: 4 }} />
          <Text style={styles.deviceName} numberOfLines={1}>{item.perangkat}</Text>
        </View>

        <Text style={styles.keluhanText} numberOfLines={2}>
          Kerusakan: {item.keluhan}
        </Text>

        <View style={styles.cardFooter}>
          <View style={styles.teknisiBadge}>
            <Ionicons name="person-outline" size={12} color={Colors.textSecondary} style={{ marginRight: 4 }} />
            <Text style={styles.teknisiText}>{item.teknisi?.name || 'Belum ditugaskan'}</Text>
          </View>
          {item.estimasi_biaya ? (
            <Text style={styles.biayaText}>Est: {formatRupiah(item.estimasi_biaya)}</Text>
          ) : null}
        </View>
      </TouchableOpacity>
    );
  }, [navigation]);

  return (
    <SafeAreaView style={styles.container}>
      {/* Top Search & Filter Bar */}
      <View style={styles.topBar}>
        <SearchBar
          value={search}
          onChangeText={setSearch}
          placeholder="Cari tiket, pelanggan, IMEI..."
          onScanPress={() => navigation.navigate('TiketScan')}
        />

        {/* Tab Filters */}
        <ScrollView 
          horizontal 
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.tabsContainer}
        >
          {TABS.map((tab) => {
            const isActive = activeTab === tab.id;
            return (
              <TouchableOpacity
                key={tab.id}
                style={[styles.tabBtn, isActive && styles.tabBtnActive]}
                onPress={() => setActiveTab(tab.id)}
              >
                <Text style={[styles.tabText, isActive && styles.tabTextActive]}>
                  {tab.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </ScrollView>
      </View>

      {/* Ticket List */}
      <FlatList
        data={tickets}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderTicketItem}
        contentContainerStyle={styles.listContent}
        refreshControl={
          <RefreshControl 
            refreshing={isLoading || isRefetching} 
            onRefresh={refetch} 
            colors={[Colors.primary]} 
          />
        }
        ListEmptyComponent={
          !isLoading ? (
            <EmptyState
              title="Tidak Ada Tiket"
              description="Belum ada tiket servis yang sesuai dengan filter atau pencarian Anda."
              icon="ticket-outline"
              actionTitle="+ Buat Tiket Baru"
              onActionPress={() => navigation.navigate('TiketIntake')}
            />
          ) : null
        }
      />

      {/* Floating Action Button */}
      <TouchableOpacity
        style={styles.fab}
        activeOpacity={0.8}
        onPress={() => navigation.navigate('TiketIntake')}
      >
        <Ionicons name="add" size={28} color="#FFFFFF" />
      </TouchableOpacity>
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
  tabsContainer: {
    paddingVertical: 10,
  },
  tabBtn: {
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 20,
    backgroundColor: '#F1F5F9',
    marginRight: 8,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  tabBtnActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  tabText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  tabTextActive: {
    color: '#FFFFFF',
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
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.04,
    shadowRadius: 2,
    elevation: 1,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 6,
  },
  ticketNo: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.primary,
  },
  customerName: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 4,
  },
  deviceRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  deviceName: {
    fontSize: 12,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  keluhanText: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginBottom: 8,
    lineHeight: 16,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F1F5F9',
    paddingTop: 8,
    marginTop: 2,
  },
  teknisiBadge: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  teknisiText: {
    fontSize: 11,
    color: Colors.textSecondary,
  },
  biayaText: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.textPrimary,
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
