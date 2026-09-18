import React, { useState } from 'react';
import { 
  View, 
  Text, 
  StyleSheet, 
  SafeAreaView, 
  KeyboardAvoidingView, 
  Platform, 
  ScrollView, 
  TouchableOpacity,
  Alert
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/Colors';
import { Input } from '../../components/Input';
import { Button } from '../../components/Button';
import { authService } from '../../services/authService';
import { useAuthStore } from '../../store/useAuthStore';

interface LoginScreenProps {
  navigation: any;
}

export const LoginScreen: React.FC<LoginScreenProps> = ({ navigation }) => {
  const [login, setLogin] = useState('admin');
  const [password, setPassword] = useState('password');
  const [loading, setLoading] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');

  const loginStore = useAuthStore((state) => state.login);
  const serverUrl = useAuthStore((state) => state.serverUrl);

  const handleLogin = async () => {
    if (!login.trim() || !password.trim()) {
      setErrorMsg('Email/Username dan kata sandi wajib diisi');
      return;
    }

    setLoading(true);
    setErrorMsg('');

    try {
      const authData = await authService.login(login.trim(), password);
      await loginStore(authData);
    } catch (error: any) {
      console.log('Login error:', error?.response?.data || error.message);
      if (error?.response?.status === 422) {
        const errors = error.response.data.errors;
        const firstError = errors ? Object.values(errors)[0] : error.response.data.message;
        setErrorMsg(Array.isArray(firstError) ? firstError[0] : firstError);
      } else if (error?.response?.status === 401) {
        setErrorMsg('Email/Username atau kata sandi tidak valid.');
      } else if (error?.message?.includes('Network Error') || !error?.response) {
        setErrorMsg(`Gagal terhubung ke server (${serverUrl}). Pastikan koneksi dan IP server benar.`);
      } else {
        setErrorMsg(error?.response?.data?.message || 'Terjadi kesalahan saat masuk.');
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardView}
      >
        <ScrollView 
          contentContainerStyle={styles.scrollContent} 
          keyboardShouldPersistTaps="handled"
        >
          {/* Header Branding */}
          <View style={styles.brandContainer}>
            <View style={styles.logoCircle}>
              <Ionicons name="build" size={36} color={Colors.primary} />
            </View>
            <Text style={styles.brandTitle}>Wahyu Teknik Indotama</Text>
            <Text style={styles.brandSubtitle}>Sistem Manajemen Servis & POS Kasir</Text>
          </View>

          {/* Form Box */}
          <View style={styles.card}>
            <Text style={styles.formTitle}>Masuk Akun</Text>
            <Text style={styles.formSubtitle}>Silakan masukkan akun teknisi atau admin Anda</Text>

            {!!errorMsg && (
              <View style={styles.errorBox}>
                <Ionicons name="alert-circle" size={18} color={Colors.danger} style={{ marginRight: 6 }} />
                <Text style={styles.errorText}>{errorMsg}</Text>
              </View>
            )}

            <Input
              label="Email atau Username"
              placeholder="admin@cekatcell.com"
              value={login}
              onChangeText={(text) => {
                setLogin(text);
                setErrorMsg('');
              }}
              autoCapitalize="none"
              leftIcon={<Ionicons name="person-outline" size={18} color={Colors.textSecondary} />}
            />

            <Input
              label="Kata Sandi"
              placeholder="••••••••"
              value={password}
              onChangeText={(text) => {
                setPassword(text);
                setErrorMsg('');
              }}
              isPassword
              leftIcon={<Ionicons name="lock-closed-outline" size={18} color={Colors.textSecondary} />}
            />

            <Button
              title="Masuk Sekarang"
              onPress={handleLogin}
              loading={loading}
              size="large"
              style={styles.submitBtn}
            />

            <TouchableOpacity 
              style={styles.configServerLink}
              onPress={() => navigation.navigate('ServerConfig')}
            >
              <Ionicons name="settings-outline" size={15} color={Colors.textSecondary} style={{ marginRight: 5 }} />
              <Text style={styles.configServerText}>Atur Alamat Server / IP</Text>
            </TouchableOpacity>
          </View>

          {/* Footer Version */}
          <Text style={styles.versionText}>v1.0.0 • React Native Expo</Text>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  keyboardView: {
    flex: 1,
  },
  scrollContent: {
    flexGrow: 1,
    justifyContent: 'center',
    padding: 20,
  },
  brandContainer: {
    alignItems: 'center',
    marginBottom: 24,
  },
  logoCircle: {
    width: 76,
    height: 76,
    borderRadius: 38,
    backgroundColor: Colors.primaryLight,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
    borderWidth: 1,
    borderColor: '#BFDBFE',
  },
  brandTitle: {
    fontSize: 22,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  brandSubtitle: {
    fontSize: 13,
    color: Colors.textSecondary,
    marginTop: 4,
  },
  card: {
    backgroundColor: Colors.surface,
    borderRadius: 16,
    padding: 22,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  formTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textPrimary,
  },
  formSubtitle: {
    fontSize: 13,
    color: Colors.textSecondary,
    marginBottom: 16,
    marginTop: 2,
  },
  errorBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEF2F2',
    borderColor: '#FECACA',
    borderWidth: 1,
    padding: 10,
    borderRadius: 8,
    marginBottom: 14,
  },
  errorText: {
    fontSize: 12,
    color: Colors.danger,
    flex: 1,
  },
  submitBtn: {
    marginTop: 8,
  },
  configServerLink: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 18,
    paddingVertical: 6,
  },
  configServerText: {
    fontSize: 13,
    color: Colors.textSecondary,
    fontWeight: '500',
  },
  versionText: {
    textAlign: 'center',
    fontSize: 12,
    color: Colors.textMuted,
    marginTop: 24,
  },
});
