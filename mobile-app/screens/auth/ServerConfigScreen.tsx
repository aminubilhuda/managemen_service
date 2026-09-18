import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  ScrollView, 
  Alert,
  TouchableOpacity
} from 'react-native';
import axios from 'axios';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Header } from '../../components/Header';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { useAuthStore } from '../../store/useAuthStore';

interface ServerConfigScreenProps {
  navigation: any;
}

export const ServerConfigScreen: React.FC<ServerConfigScreenProps> = ({ navigation }) => {
  const currentUrl = useAuthStore((state) => state.serverUrl);
  const setServerUrl = useAuthStore((state) => state.setServerUrl);

  const [url, setUrl] = useState(currentUrl);
  const [testing, setTesting] = useState(false);
  const [testResult, setTestResult] = useState<{ success: boolean; msg: string } | null>(null);

  const testConnection = async () => {
    let trimmed = url.trim().replace(/\/$/, '');
    if (!trimmed.startsWith('http://') && !trimmed.startsWith('https://')) {
      Alert.alert('Format Salah', 'URL harus diawali dengan http:// atau https://');
      return;
    }

    // Auto-append /api/v1 if user omitted it
    if (!trimmed.includes('/api/v1') && !trimmed.endsWith('/api')) {
      trimmed = `${trimmed}/api/v1`;
      setUrl(trimmed);
    }

    setTesting(true);
    setTestResult(null);

    try {
      // 1. Coba ping endpoint publik /ping
      const pingUrl = trimmed.endsWith('/api/v1') ? `${trimmed}/ping` : `${trimmed}/api/v1/ping`;
      const res = await axios.get(pingUrl, {
        timeout: 6000,
        headers: { Accept: 'application/json' },
      });

      if (res.status >= 200 && res.status < 300) {
        setTestResult({
          success: true,
          msg: `Koneksi berhasil! Server aktif (${res.data?.message || 'OK'}).`,
        });
        return;
      }
    } catch (err: any) {
      // HTTP 401 berarti server backend MERESPONS dengan normal (hanya butuh login)
      if (err.response && (err.response.status === 401 || err.response.status === 200 || err.response.status === 405)) {
        setTestResult({
          success: true,
          msg: `Koneksi berhasil! Server merespons (HTTP ${err.response.status}). Anda dapat langsung login.`,
        });
        return;
      }

      setTestResult({
        success: false,
        msg: `Gagal terhubung: ${err.message}. Pastikan HP dan laptop terhubung di WiFi yang sama, dan port 8000 tidak diblokir firewall.`,
      });
    } finally {
      setTesting(false);
    }
  };

  const handleSave = async () => {
    let trimmed = url.trim().replace(/\/$/, '');
    if (!trimmed.startsWith('http://') && !trimmed.startsWith('https://')) {
      Alert.alert('Format Salah', 'URL harus diawali dengan http:// atau https://');
      return;
    }

    if (!trimmed.includes('/api/v1') && !trimmed.endsWith('/api')) {
      trimmed = `${trimmed}/api/v1`;
      setUrl(trimmed);
    }

    await setServerUrl(trimmed);
    Alert.alert('Sukses', `Alamat server berhasil disimpan!\n\n${trimmed}`, [
      { text: 'OK', onPress: () => navigation.goBack() }
    ]);
  };

  return (
    <SafeAreaView style={styles.container}>
      <Header 
        title="Pengaturan Server IP" 
        onBack={() => navigation.goBack()} 
      />

      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.card}>
          <Text style={styles.cardTitle}>Base URL REST API</Text>
          <Text style={styles.cardDesc}>
            Aplikasi mobile berkomunikasi dengan backend Laravel melalui REST API v1. Masukkan alamat IP komputer backend Anda.
          </Text>

          <Input
            label="URL Endpoint Backend"
            value={url}
            onChangeText={(text) => {
              setUrl(text);
              setTestResult(null);
            }}
            placeholder="http://192.168.18.225:8000/api/v1"
            autoCapitalize="none"
            autoCorrect={false}
          />

          <Text style={styles.presetsLabel}>Preset Cepat:</Text>
          <View style={styles.presetsRow}>
            <TouchableOpacity 
              style={styles.presetChip}
              onPress={() => {
                setUrl('http://192.168.18.225:8000/api/v1');
                setTestResult(null);
              }}
            >
              <Text style={styles.presetChipText}>HP Fisik WiFi (192.168.18.225)</Text>
            </TouchableOpacity>

            <TouchableOpacity 
              style={styles.presetChip}
              onPress={() => {
                setUrl('http://10.0.2.2:8000/api/v1');
                setTestResult(null);
              }}
            >
              <Text style={styles.presetChipText}>Android Emulator (10.0.2.2)</Text>
            </TouchableOpacity>

            <TouchableOpacity 
              style={styles.presetChip}
              onPress={() => {
                setUrl('http://localhost:8000/api/v1');
                setTestResult(null);
              }}
            >
              <Text style={styles.presetChipText}>Localhost (Web / Browser)</Text>
            </TouchableOpacity>
          </View>

          {testResult && (
            <View style={[
              styles.resultBox, 
              testResult.success ? styles.resultSuccess : styles.resultError
            ]}>
              <Ionicons 
                name={testResult.success ? 'checkmark-circle' : 'alert-circle'} 
                size={20} 
                color={testResult.success ? Colors.success : Colors.danger} 
                style={{ marginRight: 8 }}
              />
              <Text style={[
                styles.resultText,
                { color: testResult.success ? '#065F46' : '#991B1B' }
              ]}>
                {testResult.msg}
              </Text>
            </View>
          )}

          <View style={styles.actionButtons}>
            <Button
              title="Uji Koneksi Server"
              variant="outline"
              onPress={testConnection}
              loading={testing}
              style={{ marginBottom: 10 }}
              icon={<Ionicons name="flash-outline" size={18} color={Colors.primary} />}
            />

            <Button
              title="Simpan & Terapkan"
              onPress={handleSave}
              icon={<Ionicons name="save-outline" size={18} color="#FFFFFF" />}
            />
          </View>
        </View>

        <View style={styles.hintCard}>
          <Ionicons name="information-circle-outline" size={20} color={Colors.primary} style={{ marginRight: 8 }} />
          <Text style={styles.hintText}>
            Tips pengujian HP fisik: Pastikan smartphone dan komputer backend berada dalam satu jaringan WiFi yang sama, lalu gunakan IP lokal komputer (misal: 192.168.1.xxx).
          </Text>
        </View>
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
  },
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    padding: 18,
    marginBottom: 16,
  },
  cardTitle: {
    fontSize: 16,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  cardDesc: {
    fontSize: 13,
    color: Colors.textSecondary,
    marginTop: 4,
    marginBottom: 16,
    lineHeight: 18,
  },
  presetsLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginBottom: 6,
  },
  presetsRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginBottom: 16,
  },
  presetChip: {
    backgroundColor: Colors.primaryLight,
    borderRadius: 6,
    paddingVertical: 6,
    paddingHorizontal: 10,
    marginRight: 8,
    marginBottom: 8,
    borderWidth: 1,
    borderColor: '#BFDBFE',
  },
  presetChipText: {
    fontSize: 12,
    color: Colors.primaryDark,
    fontWeight: '500',
  },
  resultBox: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 12,
    borderRadius: 8,
    borderWidth: 1,
    marginBottom: 16,
  },
  resultSuccess: {
    backgroundColor: '#ECFDF5',
    borderColor: '#A7F3D0',
  },
  resultError: {
    backgroundColor: '#FEF2F2',
    borderColor: '#FECACA',
  },
  resultText: {
    fontSize: 12,
    flex: 1,
    lineHeight: 16,
  },
  actionButtons: {
    marginTop: 6,
  },
  hintCard: {
    flexDirection: 'row',
    backgroundColor: '#EFF6FF',
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#DBEAFE',
    padding: 14,
    alignItems: 'flex-start',
  },
  hintText: {
    flex: 1,
    fontSize: 12,
    color: '#1E40AF',
    lineHeight: 17,
  },
});
