import React from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  RefreshControl, 
  TouchableOpacity 
} from 'react-native';
import { useQuery } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Card } from '../../components/Card';
import { StatusBadge } from '../../components/StatusBadge';
import { formatRupiah } from '../../components/CurrencyInput';
import { dashboardService } from '../../services/dashboardService';
import { useAuthStore } from '../../store/useAuthStore';

interface DashboardScreenProps {
  navigation: any;
}

export const DashboardScreen: React.FC<DashboardScreenProps> = ({ navigation }) => {
  const user = useAuthStore((state) => state.user);
  const isAdminOrOwner = user?.roles?.some(r => ['super-admin', 'admin', 'owner'].includes(r));
  const isTeknisi = user?.roles?.includes('teknisi');

  const { data, isLoading, refetch, isRefetching } = useQuery({
    queryKey: ['dashboard'],
    queryFn: () => dashboardService.getDashboard(),
  });

  const statusCounts = data?.status_counts;
  const pendapatan = data?.pendapatan;
  const recentTickets = data?.tiket_terbaru || [];

  return (
    <SafeAreaView style={styles.container}>
      {/* Top Bar Header */}
      <View style={styles.header}>
        <View>
          <Text style={styles.greetingText}>Halo, {user?.name || 'Pengguna'} 👋</Text>
          <View style={styles.roleRow}>
            <View style={styles.roleBadge}>
              <Text style={styles.roleText}>{user?.roles?.[0] || 'teknisi'}</Text>
            </View>
            <Text style={styles.shopName}>Wahyu Teknik Indotama</Text>
          </View>
        </View>

        <TouchableOpacity 
          style={styles.scanBtnHeader}
          onPress={() => navigation.navigate('TiketScan')}
        >
          <Ionicons name="qr-code-outline" size={22} color={Colors.primary} />
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl 
            refreshing={isLoading || isRefetching} 
            onRefresh={refetch} 
            colors={[Colors.primary]} 
          />
        }
      >
        {/* Omset Ringkasan (Admin / Super Admin / Owner) */}
        {isAdminOrOwner && pendapatan && (
          <View style={styles.omsetCard}>
            <View style={styles.omsetHeader}>
              <View style={styles.omsetIcon}>
                <Ionicons name="wallet-outline" size={20} color="#FFFFFF" />
              </View>
              <Text style={styles.omsetTitle}>Ringkasan Pendapatan</Text>
            </View>
            <View style={styles.omsetGrid}>
              <View style={styles.omsetItem}>
                <Text style={styles.omsetLabel}>Hari Ini</Text>
                <Text style={styles.omsetValue}>{formatRupiah(pendapatan.hari_ini)}</Text>
              </View>
              <View style={styles.omsetDivider} />
              <View style={styles.omsetItem}>
                <Text style={styles.omsetLabel}>Bulan Ini</Text>
                <Text style={styles.omsetValue}>{formatRupiah(pendapatan.bulan_ini)}</Text>
              </View>
            </View>
          </View>
        )}

        {/* Banner Tugas Teknisi */}
        {isTeknisi && data?.my_tasks_count !== undefined && (
          <TouchableOpacity 
            activeOpacity={0.8}
            onPress={() => navigation.navigate('ServisTab', { screen: 'TiketList', params: { my_tickets: true } })}
            style={styles.taskBanner}
          >
            <View style={styles.taskIconCircle}>
              <Ionicons name="hammer-outline" size={20} color="#FFFFFF" />
            </View>
            <View style={{ flex: 1 }}>
              <Text style={styles.taskTitle}>Tugas Servis Saya</Text>
              <Text style={styles.taskDesc}>{data.my_tasks_count} unit sedang ditugaskan ke Anda</Text>
            </View>
            <Ionicons name="chevron-forward" size={20} color={Colors.primary} />
          </TouchableOpacity>
        )}

        {/* Status Servis Grid */}
        <Text style={styles.sectionTitle}>Status Servis Unit</Text>
        <View style={styles.statusGrid}>
          <TouchableOpacity 
            style={[styles.statusBox, { backgroundColor: '#F8FAFC', borderColor: '#CBD5E1' }]}
            onPress={() => navigation.navigate('ServisTab', { screen: 'TiketList' })}
          >
            <Text style={styles.statusNum}>{statusCounts?.total_aktif || 0}</Text>
            <Text style={styles.statusLabel}>Total Aktif</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.statusBox, { backgroundColor: Colors.status.dikerjakan.bg, borderColor: Colors.status.dikerjakan.border }]}
            onPress={() => navigation.navigate('ServisTab', { screen: 'TiketList', params: { status: 'dikerjakan' } })}
          >
            <Text style={[styles.statusNum, { color: Colors.status.dikerjakan.text }]}>
              {statusCounts?.dikerjakan || 0}
            </Text>
            <Text style={[styles.statusLabel, { color: Colors.status.dikerjakan.text }]}>Dikerjakan</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.statusBox, { backgroundColor: Colors.status.menunggu_sparepart.bg, borderColor: Colors.status.menunggu_sparepart.border }]}
            onPress={() => navigation.navigate('ServisTab', { screen: 'TiketList', params: { status: 'menunggu_sparepart' } })}
          >
            <Text style={[styles.statusNum, { color: Colors.status.menunggu_sparepart.text }]}>
              {statusCounts?.menunggu_sparepart || 0}
            </Text>
            <Text style={[styles.statusLabel, { color: Colors.status.menunggu_sparepart.text }]}>Tunggu Part</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[styles.statusBox, { backgroundColor: Colors.status.selesai.bg, borderColor: Colors.status.selesai.border }]}
            onPress={() => navigation.navigate('ServisTab', { screen: 'TiketList', params: { status: 'selesai' } })}
          >
            <Text style={[styles.statusNum, { color: Colors.status.selesai.text }]}>
              {statusCounts?.selesai || 0}
            </Text>
            <Text style={[styles.statusLabel, { color: Colors.status.selesai.text }]}>Selesai</Text>
          </TouchableOpacity>
        </View>

        {/* Quick Actions */}
        <Text style={styles.sectionTitle}>Aksi Cepat</Text>
        <View style={styles.quickActionsGrid}>
          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('TiketIntake')}
          >
            <View style={[styles.actionIconCircle, { backgroundColor: '#EFF6FF' }]}>
              <Ionicons name="add-circle" size={24} color={Colors.primary} />
            </View>
            <Text style={styles.actionLabel}>Servis Baru</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('TiketScan')}
          >
            <View style={[styles.actionIconCircle, { backgroundColor: '#F0FDF4' }]}>
              <Ionicons name="barcode" size={24} color={Colors.success} />
            </View>
            <Text style={styles.actionLabel}>Scan Tiket</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('InvoiceCreate')}
          >
            <View style={[styles.actionIconCircle, { backgroundColor: '#FEF3C7' }]}>
              <Ionicons name="receipt" size={24} color="#D97706" />
            </View>
            <Text style={styles.actionLabel}>Buat Invoice</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('PengeluaranModal')}
          >
            <View style={[styles.actionIconCircle, { backgroundColor: '#FEE2E2' }]}>
              <Ionicons name="cash-outline" size={24} color={Colors.danger} />
            </View>
            <Text style={styles.actionLabel}>Kas Keluar</Text>
          </TouchableOpacity>
        </View>

        {/* Recent Tickets Section */}
        <View style={styles.sectionHeaderRow}>
          <Text style={styles.sectionTitle}>Tiket Servis Terbaru</Text>
          <TouchableOpacity onPress={() => navigation.navigate('ServisTab')}>
            <Text style={styles.seeAllText}>Lihat Semua</Text>
          </TouchableOpacity>
        </View>

        {recentTickets.length === 0 ? (
          <Card style={styles.emptyRecentCard}>
            <Text style={styles.emptyRecentText}>Belum ada tiket servis terbaru.</Text>
          </Card>
        ) : (
          recentTickets.map((t) => (
            <Card 
              key={t.id} 
              onPress={() => navigation.navigate('TiketDetail', { id: t.id })}
              style={styles.ticketCard}
            >
              <View style={styles.ticketCardHeader}>
                <Text style={styles.ticketNo}>{t.no_tiket}</Text>
                <StatusBadge status={t.status} label={t.status_label} />
              </View>
              <Text style={styles.customerName}>{t.nama_pelanggan}</Text>
              <View style={styles.deviceRow}>
                <Ionicons name="phone-portrait-outline" size={14} color={Colors.textSecondary} style={{ marginRight: 4 }} />
                <Text style={styles.deviceName}>{t.perangkat}</Text>
              </View>
            </Card>
          ))
        )}
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  header: {
    backgroundColor: Colors.surface,
    paddingHorizontal: 16,
    paddingVertical: 14,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  greetingText: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  roleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 3,
  },
  roleBadge: {
    backgroundColor: Colors.primaryLight,
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 4,
    marginRight: 6,
  },
  roleText: {
    fontSize: 10,
    fontWeight: '700',
    color: Colors.primary,
    textTransform: 'uppercase',
  },
  shopName: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  scanBtnHeader: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
  },
  scrollContent: {
    padding: 16,
  },
  omsetCard: {
    backgroundColor: Colors.primary,
    borderRadius: 14,
    padding: 16,
    marginBottom: 16,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 6,
    elevation: 3,
  },
  omsetHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
  },
  omsetIcon: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(255,255,255,0.2)',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 8,
  },
  omsetTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: '#FFFFFF',
  },
  omsetGrid: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  omsetItem: {
    flex: 1,
  },
  omsetLabel: {
    fontSize: 11,
    color: '#BFDBFE',
    marginBottom: 2,
  },
  omsetValue: {
    fontSize: 17,
    fontWeight: '700',
    color: '#FFFFFF',
  },
  omsetDivider: {
    width: 1,
    height: 34,
    backgroundColor: 'rgba(255,255,255,0.2)',
    marginHorizontal: 12,
  },
  taskBanner: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: '#BFDBFE',
    padding: 12,
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 16,
  },
  taskIconCircle: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: Colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  taskTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  taskDesc: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 10,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 8,
    marginBottom: 10,
  },
  seeAllText: {
    fontSize: 13,
    color: Colors.primary,
    fontWeight: '600',
  },
  statusGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  statusBox: {
    flex: 1,
    borderRadius: 10,
    borderWidth: 1,
    paddingVertical: 12,
    alignItems: 'center',
    marginHorizontal: 3,
  },
  statusNum: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  statusLabel: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 2,
    fontWeight: '500',
  },
  quickActionsGrid: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    paddingVertical: 14,
    paddingHorizontal: 8,
    marginBottom: 16,
  },
  actionBtn: {
    flex: 1,
    alignItems: 'center',
  },
  actionIconCircle: {
    width: 44,
    height: 44,
    borderRadius: 22,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 6,
  },
  actionLabel: {
    fontSize: 11,
    color: Colors.textPrimary,
    fontWeight: '600',
  },
  ticketCard: {
    marginBottom: 8,
  },
  ticketCardHeader: {
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
  },
  deviceName: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  emptyRecentCard: {
    padding: 20,
    alignItems: 'center',
  },
  emptyRecentText: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
});
