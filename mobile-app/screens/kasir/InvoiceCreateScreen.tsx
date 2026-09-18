import React, { useState, useEffect } from 'react';
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
import { CurrencyInput, formatRupiah } from '../../components/CurrencyInput';
import { invoiceService, CreateInvoicePayload } from '../../services/invoiceService';
import { tiketService } from '../../services/tiketService';
import { produkService } from '../../services/produkService';
import { TiketServisItem, Produk } from '../../types/api';

interface InvoiceCreateScreenProps {
  navigation: any;
  route: any;
}

interface ItemRow {
  produk_id: number | null;
  deskripsi: string;
  qty: number;
  harga_satuan: number;
  harga_modal_satuan: number;
}

export const InvoiceCreateScreen: React.FC<InvoiceCreateScreenProps> = ({ navigation, route }) => {
  const queryClient = useQueryClient();
  const prefilledTiketId = route?.params?.tiketId;

  const [selectedTiket, setSelectedTiket] = useState<TiketServisItem | null>(null);
  const [searchTiketQuery, setSearchTiketQuery] = useState('');
  const [tiketResults, setTiketResults] = useState<TiketServisItem[]>([]);

  // Item lines state
  const [items, setItems] = useState<ItemRow[]>([]);
  const [diskon, setDiskon] = useState<number>(0);
  const [keterangan, setKeterangan] = useState('');

  // Product search modal / popup
  const [showProductPicker, setShowProductPicker] = useState(false);
  const [searchProductQuery, setSearchProductQuery] = useState('');
  const [productResults, setProductResults] = useState<Produk[]>([]);

  // If prefilledTiketId is passed, fetch ticket
  useEffect(() => {
    if (prefilledTiketId) {
      tiketService.getTiketDetail(prefilledTiketId).then((t) => {
        setSelectedTiket(t);
        // If ticket has estimated cost, add default service row
        if (t.estimasi_biaya && t.estimasi_biaya > 0) {
          setItems([
            {
              produk_id: null,
              deskripsi: `Jasa Perbaikan: ${t.keluhan}`,
              qty: 1,
              harga_satuan: t.estimasi_biaya,
              harga_modal_satuan: 0,
            },
          ]);
        }
      });
    }
  }, [prefilledTiketId]);

  const searchTiket = async (q: string) => {
    setSearchTiketQuery(q);
    if (q.trim().length < 2) {
      setTiketResults([]);
      return;
    }
    try {
      const res = await tiketService.getTiketList({ search: q.trim(), per_page: 5 });
      setTiketResults(res.data || []);
    } catch {
      setTiketResults([]);
    }
  };

  const searchProduct = async (q: string) => {
    setSearchProductQuery(q);
    if (q.trim().length < 2) {
      setProductResults([]);
      return;
    }
    try {
      const res = await produkService.searchProduk(q.trim());
      setProductResults(res || []);
    } catch {
      setProductResults([]);
    }
  };

  const addCustomItem = () => {
    setItems([
      ...items,
      {
        produk_id: null,
        deskripsi: 'Jasa Pengerjaan Servis',
        qty: 1,
        harga_satuan: 50000,
        harga_modal_satuan: 0,
      },
    ]);
  };

  const addProductItem = (produk: Produk) => {
    setItems([
      ...items,
      {
        produk_id: produk.id,
        deskripsi: produk.nama_produk,
        qty: 1,
        harga_satuan: produk.harga_jual,
        harga_modal_satuan: produk.harga_modal || 0,
      },
    ]);
    setShowProductPicker(false);
    setSearchProductQuery('');
    setProductResults([]);
  };

  const updateItemQty = (index: number, newQty: number) => {
    if (newQty < 1) return;
    const newItems = [...items];
    newItems[index].qty = newQty;
    setItems(newItems);
  };

  const updateItemPrice = (index: number, newPrice: number) => {
    const newItems = [...items];
    newItems[index].harga_satuan = newPrice;
    setItems(newItems);
  };

  const removeItem = (index: number) => {
    setItems(items.filter((_, i) => i !== index));
  };

  // Live calculation
  const subtotal = items.reduce((acc, item) => acc + item.qty * item.harga_satuan, 0);
  const total = Math.max(0, subtotal - diskon);

  const createMutation = useMutation({
    mutationFn: (payload: CreateInvoicePayload) => invoiceService.createInvoice(payload),
    onSuccess: (inv) => {
      queryClient.invalidateQueries({ queryKey: ['invoiceList'] });
      queryClient.invalidateQueries({ queryKey: ['tiketList'] });
      queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      Alert.alert('Invoice Dibuat', `Invoice ${inv.no_invoice} berhasil diterbitkan!`, [
        {
          text: 'Buka Detail Invoice',
          onPress: () => navigation.replace('InvoiceDetail', { id: inv.id }),
        },
      ]);
    },
    onError: (err: any) => {
      Alert.alert('Gagal', err?.response?.data?.message || 'Gagal membuat invoice.');
    },
  });

  const handleSubmit = () => {
    if (!selectedTiket) {
      Alert.alert('Perhatian', 'Pilih tiket servis terlebih dahulu.');
      return;
    }
    if (items.length === 0) {
      Alert.alert('Perhatian', 'Tambahkan minimal 1 item suku cadang atau jasa.');
      return;
    }

    const payload: CreateInvoicePayload = {
      tiket_id: selectedTiket.id,
      items: items.map((it) => ({
        produk_id: it.produk_id,
        deskripsi: it.deskripsi,
        qty: it.qty,
        harga_satuan: it.harga_satuan,
        harga_modal_satuan: it.harga_modal_satuan,
      })),
      diskon: diskon > 0 ? diskon : undefined,
      keterangan: keterangan.trim() || undefined,
    };

    createMutation.mutate(payload);
  };

  return (
    <SafeAreaView style={styles.container}>
      <Header title="Buat Invoice Kasir" onBack={() => navigation.goBack()} />

      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        {/* Step 1: Select Ticket */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>1. Pilih Tiket Servis Terkait</Text>
          {selectedTiket ? (
            <View style={styles.selectedTiketBox}>
              <View style={{ flex: 1 }}>
                <Text style={styles.selectedTiketNo}>{selectedTiket.no_tiket}</Text>
                <Text style={styles.selectedCustomer}>
                  {selectedTiket.pelanggan?.nama_pelanggan || selectedTiket.nama_pelanggan}
                </Text>
                <Text style={styles.selectedDevice}>{selectedTiket.perangkat}</Text>
              </View>
              <TouchableOpacity onPress={() => setSelectedTiket(null)}>
                <Ionicons name="close-circle" size={22} color={Colors.danger} />
              </TouchableOpacity>
            </View>
          ) : (
            <View>
              <Input
                placeholder="Cari no tiket atau nama pelanggan..."
                value={searchTiketQuery}
                onChangeText={searchTiket}
                leftIcon={<Ionicons name="search" size={18} color={Colors.textSecondary} />}
              />
              {tiketResults.map((t) => (
                <TouchableOpacity
                  key={t.id}
                  style={styles.searchResultItem}
                  onPress={() => {
                    setSelectedTiket(t);
                    setTiketResults([]);
                    setSearchTiketQuery('');
                    if (items.length === 0 && t.estimasi_biaya) {
                      setItems([
                        {
                          produk_id: null,
                          deskripsi: `Jasa Perbaikan: ${t.keluhan}`,
                          qty: 1,
                          harga_satuan: t.estimasi_biaya,
                          harga_modal_satuan: 0,
                        },
                      ]);
                    }
                  }}
                >
                  <Text style={styles.resultTiketNo}>{t.no_tiket}</Text>
                  <Text style={styles.resultCustomer}>{t.pelanggan?.nama_pelanggan || t.nama_pelanggan} • {t.perangkat}</Text>
                </TouchableOpacity>
              ))}
            </View>
          )}
        </View>

        {/* Step 2: Items List */}
        <View style={styles.card}>
          <View style={styles.cardHeaderRow}>
            <Text style={styles.cardTitle}>2. Item Sparepart & Jasa</Text>
            <View style={{ flexDirection: 'row' }}>
              <TouchableOpacity 
                style={styles.addSmallBtn} 
                onPress={() => setShowProductPicker(true)}
              >
                <Ionicons name="cube-outline" size={14} color={Colors.primary} style={{ marginRight: 4 }} />
                <Text style={styles.addSmallText}>+ Sparepart</Text>
              </TouchableOpacity>
              <TouchableOpacity 
                style={[styles.addSmallBtn, { marginLeft: 6 }]} 
                onPress={addCustomItem}
              >
                <Ionicons name="construct-outline" size={14} color={Colors.primary} style={{ marginRight: 4 }} />
                <Text style={styles.addSmallText}>+ Jasa</Text>
              </TouchableOpacity>
            </View>
          </View>

          {items.length === 0 ? (
            <Text style={styles.emptyItemsText}>Belum ada item ditambahkan. Pilih sparepart atau jasa di atas.</Text>
          ) : (
            items.map((it, idx) => (
              <View key={idx} style={styles.itemRow}>
                <View style={{ flex: 1, marginRight: 8 }}>
                  <Input
                    value={it.deskripsi}
                    onChangeText={(text) => {
                      const newItems = [...items];
                      newItems[idx].deskripsi = text;
                      setItems(newItems);
                    }}
                    placeholder="Deskripsi item..."
                    containerStyle={{ marginBottom: 4 }}
                  />
                  <View style={styles.priceQtyRow}>
                    <View style={styles.qtyControl}>
                      <TouchableOpacity onPress={() => updateItemQty(idx, it.qty - 1)} style={styles.qtyBtn}>
                        <Text style={styles.qtyBtnText}>-</Text>
                      </TouchableOpacity>
                      <Text style={styles.qtyText}>{it.qty}</Text>
                      <TouchableOpacity onPress={() => updateItemQty(idx, it.qty + 1)} style={styles.qtyBtn}>
                        <Text style={styles.qtyBtnText}>+</Text>
                      </TouchableOpacity>
                    </View>
                    <View style={{ flex: 1, marginLeft: 8 }}>
                      <CurrencyInput
                        value={it.harga_satuan}
                        onChangeValue={(val) => updateItemPrice(idx, val)}
                        containerStyle={{ marginBottom: 0 }}
                      />
                    </View>
                  </View>
                </View>
                <TouchableOpacity onPress={() => removeItem(idx)} style={styles.trashBtn}>
                  <Ionicons name="trash-outline" size={18} color={Colors.danger} />
                </TouchableOpacity>
              </View>
            ))
          )}
        </View>

        {/* Step 3: Calculation & Summary */}
        <View style={styles.card}>
          <Text style={styles.cardTitle}>3. Diskon & Ringkasan</Text>

          <CurrencyInput
            label="Diskon Potongan (Rp)"
            value={diskon}
            onChangeValue={setDiskon}
          />

          <Input
            label="Keterangan / Catatan Garansi"
            placeholder="Misal: Garansi berlaku 30 hari untuk kerusakan yang sama"
            value={keterangan}
            onChangeText={setKeterangan}
          />

          <View style={styles.summaryBox}>
            <View style={styles.summaryRow}>
              <Text style={styles.summaryLabel}>Subtotal</Text>
              <Text style={styles.summaryVal}>{formatRupiah(subtotal)}</Text>
            </View>
            {diskon > 0 && (
              <View style={styles.summaryRow}>
                <Text style={[styles.summaryLabel, { color: Colors.danger }]}>Diskon</Text>
                <Text style={[styles.summaryVal, { color: Colors.danger }]}>- {formatRupiah(diskon)}</Text>
              </View>
            )}
            <View style={styles.divider} />
            <View style={styles.summaryRow}>
              <Text style={styles.totalLabelLarge}>Perkiraan Tagihan</Text>
              <Text style={styles.totalValLarge}>{formatRupiah(total)}</Text>
            </View>
            <Text style={styles.pajakHintText}>*Pajak resmi (PPN/PPh) akan dihitung otomatis oleh server saat diterbitkan.</Text>
          </View>

          <Button
            title="Terbitkan Invoice Sekarang"
            onPress={handleSubmit}
            loading={createMutation.isPending}
            size="large"
            style={{ marginTop: 14 }}
            icon={<Ionicons name="receipt-outline" size={20} color="#FFFFFF" />}
          />
        </View>
      </ScrollView>

      {/* Product Search Modal */}
      {showProductPicker && (
        <View style={styles.pickerOverlay}>
          <View style={styles.pickerModal}>
            <View style={styles.pickerHeader}>
              <Text style={styles.pickerTitle}>Pilih Sparepart dari Katalog</Text>
              <TouchableOpacity onPress={() => setShowProductPicker(false)}>
                <Ionicons name="close" size={22} color={Colors.textSecondary} />
              </TouchableOpacity>
            </View>
            <Input
              placeholder="Ketik nama produk atau kode..."
              value={searchProductQuery}
              onChangeText={searchProduct}
              leftIcon={<Ionicons name="search" size={18} color={Colors.textSecondary} />}
            />
            <ScrollView style={{ maxHeight: 260 }}>
              {productResults.map((prod) => (
                <TouchableOpacity
                  key={prod.id}
                  style={styles.productItem}
                  onPress={() => addProductItem(prod)}
                >
                  <View style={{ flex: 1 }}>
                    <Text style={styles.prodName}>{prod.nama_produk}</Text>
                    <Text style={styles.prodStock}>Stok: {prod.stok ?? '-'} • {prod.kode_produk}</Text>
                  </View>
                  <Text style={styles.prodPrice}>{formatRupiah(prod.harga_jual)}</Text>
                </TouchableOpacity>
              ))}
            </ScrollView>
          </View>
        </View>
      )}
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
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 16,
    marginBottom: 14,
  },
  cardTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginBottom: 10,
  },
  cardHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 10,
  },
  selectedTiketBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primaryLight,
    padding: 12,
    borderRadius: 8,
  },
  selectedTiketNo: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primaryDark,
  },
  selectedCustomer: {
    fontSize: 13,
    color: Colors.textPrimary,
    fontWeight: '500',
    marginTop: 2,
  },
  selectedDevice: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  searchResultItem: {
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
  },
  resultTiketNo: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.primary,
  },
  resultCustomer: {
    fontSize: 12,
    color: Colors.textSecondary,
    marginTop: 1,
  },
  addSmallBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primaryLight,
    paddingVertical: 4,
    paddingHorizontal: 8,
    borderRadius: 6,
  },
  addSmallText: {
    fontSize: 11,
    fontWeight: '600',
    color: Colors.primaryDark,
  },
  emptyItemsText: {
    fontSize: 12,
    color: Colors.textMuted,
    fontStyle: 'italic',
    paddingVertical: 8,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
    paddingBottom: 10,
    marginBottom: 10,
  },
  priceQtyRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  qtyControl: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    borderRadius: 6,
    height: 44,
  },
  qtyBtn: {
    width: 32,
    height: '100%',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#F8FAFC',
  },
  qtyBtnText: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  qtyText: {
    paddingHorizontal: 8,
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  trashBtn: {
    padding: 6,
  },
  summaryBox: {
    backgroundColor: '#F8FAFC',
    borderRadius: 8,
    padding: 12,
    marginTop: 8,
  },
  summaryRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 4,
  },
  summaryLabel: {
    fontSize: 13,
    color: Colors.textSecondary,
  },
  summaryVal: {
    fontSize: 13,
    color: Colors.textPrimary,
    fontWeight: '600',
  },
  divider: {
    height: 1,
    backgroundColor: Colors.surfaceBorder,
    marginVertical: 6,
  },
  totalLabelLarge: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  totalValLarge: {
    fontSize: 17,
    fontWeight: '800',
    color: Colors.primary,
  },
  pajakHintText: {
    fontSize: 11,
    color: Colors.textMuted,
    marginTop: 6,
    fontStyle: 'italic',
  },
  pickerOverlay: {
    ...StyleSheet.absoluteFill,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    padding: 20,
  },
  pickerModal: {
    backgroundColor: Colors.surface,
    borderRadius: 14,
    padding: 16,
    maxHeight: 380,
  },
  pickerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  pickerTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  productItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: Colors.surfaceBorder,
  },
  prodName: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
  },
  prodStock: {
    fontSize: 11,
    color: Colors.textSecondary,
    marginTop: 2,
  },
  prodPrice: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.primary,
  },
});
