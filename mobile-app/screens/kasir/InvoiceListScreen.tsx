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
import { invoiceService, InvoiceListParams } from '../../services/invoiceService';
import { InvoiceDetail } from '../../types/api';

interface InvoiceListScreenProps {
  navigation: any;
}

const INVOICE_TABS = [
  { id: 'all', label: 'Semua' },
  { id: 'unpaid', label: 'Belum Lunas' },
  { id: 'partial', label: 'Sebagian' },
  { id: 'paid', label: 'Lunas' },
  { id: 'void', label: 'Dibatalkan' },
];

export const InvoiceListScreen: React.FC<InvoiceListScreenProps> = ({ navigation }) => {
  const [activeTab, setActiveTab] = useState<string>('all');
  const [search, setSearch] = useState<string>('');

  const params: InvoiceListParams = {
    search: search.trim() || undefined,
    status: activeTab !== 'all' ? (activeTab as any) : undefined,
    per_page: 30,
  };

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['invoiceList', activeTab, search],
    queryFn: () => invoiceService.getInvoiceList(params),
  });

  const invoices = data?.data || [];

  const renderInvoiceItem = useCallback(({ item }: { item: InvoiceDetail }) => {
    return (
      <TouchableOpacity
        activeOpacity={0.7}
        onPress={() => navigation.navigate('InvoiceDetail', { id: item.id })}
        style={styles.card}
      >
        <View style={styles.cardHeader}>
          <Text style={styles.invoiceNo}>{item.no_invoice}</Text>
          <StatusBadge status={item.status} type="invoice" />
        </View>

        <Text style={styles.customerName}>{item.nama_pelanggan || 'Pelanggan'}</Text>
        <Text style={styles.subText}>No. Tiket: SRV-#{item.tiket_id}</Text>

        <View style={styles.cardFooter}>
          <View>
            <Text style={styles.totalLabel}>Total Tagihan</Text>
            <Text style={styles.totalValue}>{formatRupiah(item.total_tagihan)}</Text>
          </View>
          {item.status !== 'paid' && item.status !== 'void' && (
            <View style={{ alignItems: 'flex-end' }}>
              <Text style={styles.sisaLabel}>Sisa Bayar</Text>
              <Text style={styles.sisaValue}>{formatRupiah(item.sisa_tagihan)}</Text>
            </View>
          )}
        </View>
      </TouchableOpacity>
    );
  }, [navigation]);

  return (
    <SafeAreaView style={styles.container}>
      {/* Top Header & Search */}
      <View style={styles.topBar}>
        <View style={styles.headerRow}>
          <Text style={styles.title}>Kasir & Invoice</Text>
          <TouchableOpacity 
            style={styles.pengeluaranBtn}
            onPress={() => navigation.navigate('Pengeluaran')}
          >
            <Ionicons name="receipt-outline" size={16} color={Colors.primary} style={{ marginRight: 4 }} />
            <Text style={styles.pengeluaranText}>Kas Keluar</Text>
          </TouchableOpacity>
        </View>

        <SearchBar
          value={search}
          onChangeText={setSearch}
          placeholder="Cari nomor invoice, nama..."
        />

        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.tabsContainer}>
          {INVOICE_TABS.map((tab) => {
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

      <FlatList
        data={invoices}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderInvoiceItem}
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
              title="Tidak Ada Invoice"
              description="Belum ada transaksi invoice pada kategori ini."
              icon="receipt-outline"
              actionTitle="+ Buat Invoice Baru"
              onActionPress={() => navigation.navigate('InvoiceCreate')}
            />
          ) : null
        }
      />

      {/* Floating Action Button */}
      <TouchableOpacity
        style={styles.fab}
        activeOpacity={0.8}
        onPress={() => navigation.navigate('InvoiceCreate')}
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
    paddingTop: 14,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
  },
  headerRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  title: {
    fontSize: 20,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  pengeluaranBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primaryLight,
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderRadius: 8,
  },
  pengeluaranText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.primaryDark,
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
  invoiceNo: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primary,
  },
  customerName: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  subText: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
    marginBottom: 8,
  },
  cardFooter: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F1F5F9',
    paddingTop: 8,
    marginTop: 4,
  },
  totalLabel: {
    fontSize: 11,
    color: Colors.textSecondary,
  },
  totalValue: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  sisaLabel: {
    fontSize: 11,
    color: Colors.danger,
  },
  sisaValue: {
    fontSize: 13,
    fontWeight: '700',
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
