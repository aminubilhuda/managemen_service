import React, { useState, useEffect } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  TouchableOpacity, 
  Alert,
  ActivityIndicator 
} from 'react-native';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { tiketService } from '../../services/tiketService';

interface TiketScanScreenProps {
  navigation: any;
}

export const TiketScanScreen: React.FC<TiketScanScreenProps> = ({ navigation }) => {
  const [permission, requestPermission] = useCameraPermissions();
  const [scanned, setScanned] = useState(false);
  const [manualCode, setManualCode] = useState('');
  const [searching, setSearching] = useState(false);
  const [flash, setFlash] = useState(false);

  useEffect(() => {
    if (!permission?.granted) {
      requestPermission();
    }
  }, [permission]);

  const handleBarcodeScanned = async ({ data }: { data: string }) => {
    if (scanned || searching) return;
    setScanned(true);
    lookupTiket(data);
  };

  const lookupTiket = async (code: string) => {
    const trimmed = code.trim();
    if (!trimmed) {
      Alert.alert('Perhatian', 'Ketik nomor tiket atau IMEI yang ingin dicari.');
      return;
    }

    setSearching(true);
    try {
      const tiket = await tiketService.scanTiket(trimmed);
      if (tiket?.id) {
        navigation.replace('TiketDetail', { id: tiket.id });
      } else {
        Alert.alert('Tidak Ditemukan', `Tiket atau IMEI "${trimmed}" tidak terdaftar.`, [
          { text: 'Scan Ulang', onPress: () => setScanned(false) }
        ]);
      }
    } catch (err: any) {
      Alert.alert('Tidak Ditemukan', `Tiket servis atau nomor IMEI "${trimmed}" tidak ditemukan.`, [
        { text: 'Scan Ulang', onPress: () => setScanned(false) }
      ]);
    } finally {
      setSearching(false);
    }
  };

  if (!permission) {
    return (
      <SafeAreaView style={styles.container}>
        <Header title="Pindai Barcode / QR" onBack={() => navigation.goBack()} />
        <View style={styles.centerBox}>
          <ActivityIndicator size="large" color={Colors.primary} />
          <Text style={styles.loadingText}>Menyiapkan kamera...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!permission.granted) {
    return (
      <SafeAreaView style={styles.container}>
        <Header title="Pindai Barcode / QR" onBack={() => navigation.goBack()} />
        <View style={styles.centerBox}>
          <Ionicons name="camera-reverse-outline" size={48} color={Colors.textMuted} />
          <Text style={styles.permTitle}>Izin Kamera Diperlukan</Text>
          <Text style={styles.permDesc}>
            Aplikasi membutuhkan akses kamera untuk memindai barcode nomor tiket atau IMEI.
          </Text>
          <Button title="Berikan Izin Kamera" onPress={requestPermission} />
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title="Pindai Barcode / QR" 
        onBack={() => navigation.goBack()} 
        rightAction={
          <TouchableOpacity onPress={() => setFlash(!flash)} style={styles.flashBtn}>
            <Ionicons 
              name={flash ? 'flash' : 'flash-off-outline'} 
              size={22} 
              color={flash ? Colors.warning : Colors.textPrimary} 
            />
          </TouchableOpacity>
        }
      />

      <View style={styles.cameraWrapper}>
        <CameraView
          style={StyleSheet.absoluteFill}
          facing="back"
          enableTorch={flash}
          barcodeScannerSettings={{
            barcodeTypes: ['qr', 'code128', 'code39', 'ean13', 'upc_a'],
          }}
          onBarcodeScanned={scanned ? undefined : handleBarcodeScanned}
        />

        {/* Overlay Focus Frame */}
        <View style={styles.overlay}>
          <View style={styles.scanFrame}>
            <View style={[styles.corner, styles.topLeft]} />
            <View style={[styles.corner, styles.topRight]} />
            <View style={[styles.corner, styles.bottomLeft]} />
            <View style={[styles.corner, styles.bottomRight]} />
          </View>
          <Text style={styles.instructionText}>
            Arahkan kamera ke Barcode nota atau IMEI perangkat
          </Text>
        </View>

        {searching && (
          <View style={styles.loadingOverlay}>
            <ActivityIndicator size="large" color="#FFFFFF" />
            <Text style={styles.loadingWhiteText}>Mencari data tiket...</Text>
          </View>
        )}
      </View>

      {/* Manual Input Fallback */}
      <View style={styles.manualInputContainer}>
        <Text style={styles.manualTitle}>Atau Masukkan Manual:</Text>
        <View style={styles.inputRow}>
          <View style={{ flex: 1, marginRight: 8 }}>
            <Input
              placeholder="Contoh: SRV-2026-000001"
              value={manualCode}
              onChangeText={setManualCode}
              autoCapitalize="characters"
              containerStyle={{ marginBottom: 0 }}
            />
          </View>
          <Button
            title="Cari"
            onPress={() => lookupTiket(manualCode)}
            loading={searching}
            style={{ width: 80 }}
          />
        </View>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  cameraWrapper: {
    flex: 1,
    position: 'relative',
    backgroundColor: '#000000',
  },
  overlay: {
    ...StyleSheet.absoluteFill,
    alignItems: 'center',
    justifyContent: 'center',
  },
  scanFrame: {
    width: 240,
    height: 240,
    position: 'relative',
  },
  corner: {
    position: 'absolute',
    width: 28,
    height: 28,
    borderColor: Colors.primary,
  },
  topLeft: {
    top: 0,
    left: 0,
    borderTopWidth: 4,
    borderLeftWidth: 4,
  },
  topRight: {
    top: 0,
    right: 0,
    borderTopWidth: 4,
    borderRightWidth: 4,
  },
  bottomLeft: {
    bottom: 0,
    left: 0,
    borderBottomWidth: 4,
    borderLeftWidth: 4,
  },
  bottomRight: {
    bottom: 0,
    right: 0,
    borderBottomWidth: 4,
    borderRightWidth: 4,
  },
  instructionText: {
    color: '#FFFFFF',
    fontSize: 13,
    backgroundColor: 'rgba(0,0,0,0.6)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 6,
    marginTop: 24,
    textAlign: 'center',
  },
  loadingOverlay: {
    ...StyleSheet.absoluteFill,
    backgroundColor: 'rgba(0,0,0,0.7)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  loadingWhiteText: {
    color: '#FFFFFF',
    fontSize: 14,
    marginTop: 10,
    fontWeight: '600',
  },
  manualInputContainer: {
    backgroundColor: Colors.surface,
    padding: 16,
    borderTopWidth: 1,
    borderTopColor: Colors.surfaceBorder,
  },
  manualTitle: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginBottom: 8,
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  flashBtn: {
    padding: 6,
  },
  centerBox: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: 24,
  },
  loadingText: {
    marginTop: 10,
    fontSize: 14,
    color: Colors.textSecondary,
  },
  permTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
    marginTop: 14,
    marginBottom: 6,
  },
  permDesc: {
    fontSize: 13,
    color: Colors.textSecondary,
    textAlign: 'center',
    marginBottom: 20,
    lineHeight: 18,
  },
});
