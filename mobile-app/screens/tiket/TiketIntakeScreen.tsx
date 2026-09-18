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
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { CurrencyInput } from '../../components/CurrencyInput';
import { tiketService, CreateTiketPayload } from '../../services/tiketService';
import { pelangganService } from '../../services/pelangganService';
import { masterService } from '../../services/masterService';
import { Pelanggan, TeknisiItem } from '../../types/api';

interface TiketIntakeScreenProps {
  navigation: any;
  route: any;
}

export const TiketIntakeScreen: React.FC<TiketIntakeScreenProps> = ({ navigation, route }) => {
  const queryClient = useQueryClient();

  // Mode: 'new' (pelanggan baru) or 'existing' (pelanggan terdaftar)
  const [customerMode, setCustomerMode] = useState<'new' | 'existing'>('new');
  const [selectedPelanggan, setSelectedPelanggan] = useState<Pelanggan | null>(null);
  const [searchCustomerQuery, setSearchCustomerQuery] = useState('');
  const [searchResults, setSearchResults] = useState<Pelanggan[]>([]);
  const [isSearchingCustomer, setIsSearchingCustomer] = useState(false);

  // Form states
  const [namaPelanggan, setNamaPelanggan] = useState('');
  const [noTelp, setNoTelp] = useState('');
  const [alamat, setAlamat] = useState('');

  const [perangkat, setPerangkat] = useState('');
  const [imeiSn, setImeiSn] = useState(route?.params?.scannedCode || '');
  const [kelengkapan, setKelengkapan] = useState('Unit HP saja');
  const [keluhan, setKeluhan] = useState('');
  const [kondisiAwal, setKondisiAwal] = useState('');
  const [estimasiBiaya, setEstimasiBiaya] = useState<number>(0);
  const [estimasiSelesai, setEstimasiSelesai] = useState('');
  const [selectedTeknisiId, setSelectedTeknisiId] = useState<number | undefined>();

  // Fetch teknisi list
  const { data: teknisiList = [] } = useQuery({
    queryKey: ['teknisiList'],
    queryFn: () => masterService.getTeknisi(),
  });

  // Autocomplete search customer
  const handleSearchCustomer = async (query: string) => {
    setSearchCustomerQuery(query);
    if (query.trim().length < 2) {
      setSearchResults([]);
      return;
    }
    setIsSearchingCustomer(true);
    try {
      const res = await pelangganService.searchPelanggan(query.trim());
      setSearchResults(res || []);
    } catch {
      setSearchResults([]);
    } finally {
      setIsSearchingCustomer(false);
    }
  };

  const createMutation = useMutation({
    mutationFn: (payload: CreateTiketPayload) => tiketService.createTiket(payload),
    onSuccess: (newTiket) => {
      queryClient.invalidateQueries({ queryKey: ['tiketList'] });
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      Alert.alert(
        'Tiket Diterbitkan!',
        `No. Tiket: ${newTiket.no_tiket} berhasil dibuat.`,
        [
          {
            text: 'Lihat Detail Tiket',
            onPress: () => navigation.replace('TiketDetail', { id: newTiket.id }),
          },
        ]
      );
    },
    onError: (err: any) => {
      const msg = err?.response?.data?.message || 'Gagal membuat tiket servis.';
      Alert.alert('Gagal', msg);
    },
  });

  const handleSubmit = () => {
    if (customerMode === 'new') {
      if (!namaPelanggan.trim()) {
        Alert.alert('Validasi Gagal', 'Nama pelanggan wajib diisi.');
        return;
      }
      if (!noTelp.trim()) {
        Alert.alert('Validasi Gagal', 'Nomor telepon/WhatsApp pelanggan wajib diisi.');
        return;
      }
    } else {
      if (!selectedPelanggan) {
        Alert.alert('Validasi Gagal', 'Pilih pelanggan terdaftar terlebih dahulu.');
        return;
      }
    }

    if (!perangkat.trim()) {
      Alert.alert('Validasi Gagal', 'Tipe perangkat/HP wajib diisi.');
      return;
    }
    if (!keluhan.trim()) {
      Alert.alert('Validasi Gagal', 'Keluhan kerusakan wajib diisi.');
      return;
    }
    if (!kondisiAwal.trim()) {
      Alert.alert('Validasi Gagal', 'Kondisi awal fisik unit saat masuk wajib diisi.');
      return;
    }

    const payload: CreateTiketPayload = {
      perangkat: perangkat.trim(),
      imei_sn: imeiSn.trim() || undefined,
      kelengkapan: kelengkapan.trim() || undefined,
      keluhan: keluhan.trim(),
      kondisi_awal: kondisiAwal.trim(),
      estimasi_biaya: estimasiBiaya > 0 ? estimasiBiaya : undefined,
      estimasi_selesai: estimasiSelesai.trim() || undefined,
      teknisi_id: selectedTeknisiId,
    };

    if (customerMode === 'existing' && selectedPelanggan) {
      payload.pelanggan_id = selectedPelanggan.id;
    } else {
      payload.nama_pelanggan = namaPelanggan.trim();
      payload.no_telp = noTelp.trim();
      payload.alamat = alamat.trim() || undefined;
    }

    createMutation.mutate(payload);
  };

  return (
    <SafeAreaView style={styles.container}>
      <Header title="Penerimaan Servis Baru" onBack={() => navigation.goBack()} />

      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {/* Section 1: Customer Data */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionTitle}>1. Data Pelanggan</Text>

          {/* Mode Selector Chips */}
          <View style={styles.chipRow}>
            <TouchableOpacity
              style={[styles.modeChip, customerMode === 'new' && styles.modeChipActive]}
              onPress={() => {
                setCustomerMode('new');
                setSelectedPelanggan(null);
              }}
            >
              <Ionicons 
                name="person-add-outline" 
                size={16} 
                color={customerMode === 'new' ? '#FFFFFF' : Colors.textSecondary} 
                style={{ marginRight: 6 }} 
              />
              <Text style={[styles.modeChipText, customerMode === 'new' && styles.modeChipTextActive]}>
                Pelanggan Baru
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.modeChip, customerMode === 'existing' && styles.modeChipActive]}
              onPress={() => setCustomerMode('existing')}
            >
              <Ionicons 
                name="people-outline" 
                size={16} 
                color={customerMode === 'existing' ? '#FFFFFF' : Colors.textSecondary} 
                style={{ marginRight: 6 }} 
              />
              <Text style={[styles.modeChipText, customerMode === 'existing' && styles.modeChipTextActive]}>
                Pelanggan Terdaftar
              </Text>
            </TouchableOpacity>
          </View>

          {customerMode === 'existing' ? (
            <View>
              {selectedPelanggan ? (
                <View style={styles.selectedCustomerBox}>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.selectedCustomerName}>{selectedPelanggan.nama_pelanggan}</Text>
                    <Text style={styles.selectedCustomerTelp}>{selectedPelanggan.no_telp}</Text>
                    {selectedPelanggan.alamat ? (
                      <Text style={styles.selectedCustomerAddress}>{selectedPelanggan.alamat}</Text>
                    ) : null}
                  </View>
                  <TouchableOpacity onPress={() => setSelectedPelanggan(null)}>
                    <Ionicons name="close-circle" size={22} color={Colors.danger} />
                  </TouchableOpacity>
                </View>
              ) : (
                <View>
                  <Input
                    placeholder="Ketik nama atau no HP pelanggan..."
                    value={searchCustomerQuery}
                    onChangeText={handleSearchCustomer}
                    leftIcon={<Ionicons name="search" size={18} color={Colors.textSecondary} />}
                  />
                  {searchResults.map((cust) => (
                    <TouchableOpacity
                      key={cust.id}
                      style={styles.searchResultItem}
                      onPress={() => {
                        setSelectedPelanggan(cust);
                        setSearchResults([]);
                        setSearchCustomerQuery('');
                      }}
                    >
                      <Text style={styles.resultName}>{cust.nama_pelanggan}</Text>
                      <Text style={styles.resultTelp}>{cust.no_telp}</Text>
                    </TouchableOpacity>
                  ))}
                </View>
              )}
            </View>
          ) : (
            <View>
              <Input
                label="Nama Pelanggan *"
                placeholder="Contoh: Budi Santoso"
                value={namaPelanggan}
                onChangeText={setNamaPelanggan}
              />
              <Input
                label="Nomor WhatsApp / HP *"
                placeholder="081234567890"
                keyboardType="phone-pad"
                value={noTelp}
                onChangeText={setNoTelp}
              />
              <Input
                label="Alamat (Opsional)"
                placeholder="Jl. Merdeka No. 10"
                value={alamat}
                onChangeText={setAlamat}
              />
            </View>
          )}
        </View>

        {/* Section 2: Device Specs */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionTitle}>2. Identitas Perangkat HP</Text>
          <Input
            label="Tipe / Merk Perangkat *"
            placeholder="Contoh: Samsung Galaxy A52 / iPhone 11"
            value={perangkat}
            onChangeText={setPerangkat}
          />
          <Input
            label="Nomor IMEI / Serial Number"
            placeholder="15 digit angka IMEI"
            value={imeiSn}
            onChangeText={setImeiSn}
            rightIcon={
              <TouchableOpacity onPress={() => navigation.navigate('TiketScan')}>
                <Ionicons name="qr-code-outline" size={20} color={Colors.primary} />
              </TouchableOpacity>
            }
          />
          <Input
            label="Kelengkapan Unit"
            placeholder="Unit saja, Dus, Charger, SIM card tray..."
            value={kelengkapan}
            onChangeText={setKelengkapan}
          />
        </View>

        {/* Section 3: Damage & Initial Condition */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionTitle}>3. Kerusakan & Kondisi Fisik</Text>
          <Input
            label="Keluhan Kerusakan *"
            placeholder="Layar retak sentuh mati, tidak bisa dicas, kena air..."
            value={keluhan}
            onChangeText={setKeluhan}
            multiline
            numberOfLines={3}
            style={{ height: 72, textAlignVertical: 'top', paddingTop: 8 }}
          />
          <Input
            label="Kondisi Fisik Saat Masuk *"
            placeholder="Casing baret di sudut kanan, tombol volume normal, segel utuh..."
            value={kondisiAwal}
            onChangeText={setKondisiAwal}
            multiline
            numberOfLines={3}
            style={{ height: 72, textAlignVertical: 'top', paddingTop: 8 }}
          />
        </View>

        {/* Section 4: Estimation & Assignment */}
        <View style={styles.sectionCard}>
          <Text style={styles.sectionTitle}>4. Estimasi & Penugasan Teknisi</Text>
          <CurrencyInput
            label="Estimasi Biaya Servis (Rp)"
            value={estimasiBiaya}
            onChangeValue={setEstimasiBiaya}
          />
          <Input
            label="Estimasi Tanggal Selesai (YYYY-MM-DD)"
            placeholder="Contoh: 2026-09-20"
            value={estimasiSelesai}
            onChangeText={setEstimasiSelesai}
          />

          <Text style={styles.subLabel}>Pilih Teknisi Penanggung Jawab:</Text>
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.teknisiRow}>
            {teknisiList.map((tek) => {
              const isSelected = selectedTeknisiId === tek.id;
              return (
                <TouchableOpacity
                  key={tek.id}
                  style={[styles.teknisiChip, isSelected && styles.teknisiChipActive]}
                  onPress={() => setSelectedTeknisiId(isSelected ? undefined : tek.id)}
                >
                  <Text style={[styles.teknisiChipText, isSelected && styles.teknisiChipTextActive]}>
                    {tek.name}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>
        </View>

        {/* Submit Button */}
        <Button
          title="Simpan & Terbitkan Tiket"
          onPress={handleSubmit}
          loading={createMutation.isPending}
          size="large"
          style={styles.submitBtn}
          icon={<Ionicons name="checkmark-circle-outline" size={20} color="#FFFFFF" />}
        />
      </ScrollView>
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
  sectionCard: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 16,
    marginBottom: 14,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 12,
  },
  chipRow: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  modeChip: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    marginHorizontal: 4,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  modeChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  modeChipText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
  },
  modeChipTextActive: {
    color: '#FFFFFF',
  },
  selectedCustomerBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primaryLight,
    padding: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#BFDBFE',
  },
  selectedCustomerName: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primaryDark,
  },
  selectedCustomerTelp: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  selectedCustomerAddress: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  searchResultItem: {
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
    backgroundColor: Colors.surface,
  },
  resultName: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  resultTelp: {
    fontSize: 12,
    color: Colors.textSecondary,
  },
  subLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 8,
    marginTop: 4,
  },
  teknisiRow: {
    flexDirection: 'row',
  },
  teknisiChip: {
    paddingVertical: 8,
    paddingHorizontal: 14,
    borderRadius: 8,
    backgroundColor: '#F1F5F9',
    marginRight: 8,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  teknisiChipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  teknisiChipText: {
    fontSize: 13,
    fontWeight: '500',
    color: Colors.textSecondary,
  },
  teknisiChipTextActive: {
    color: '#FFFFFF',
    fontWeight: '600',
  },
  submitBtn: {
    marginTop: 6,
  },
});
