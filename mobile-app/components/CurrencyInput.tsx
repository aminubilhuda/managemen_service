import React from 'react';
import { View, Text, TextInput, StyleSheet, ViewStyle } from 'react-native';
import { Colors } from '../constants/Colors';

interface CurrencyInputProps {
  label?: string;
  value: number;
  onChangeValue: (value: number) => void;
  error?: string;
  placeholder?: string;
  containerStyle?: ViewStyle;
}

export const formatRupiah = (num: number): string => {
  return 'Rp ' + (num || 0).toLocaleString('id-ID');
};

export const CurrencyInput: React.FC<CurrencyInputProps> = ({
  label,
  value,
  onChangeValue,
  error,
  placeholder = '0',
  containerStyle,
}) => {
  const handleChangeText = (text: string) => {
    // Keep only numbers
    const cleanNumber = text.replace(/[^0-9]/g, '');
    const num = cleanNumber ? parseInt(cleanNumber, 10) : 0;
    onChangeValue(num);
  };

  const displayValue = value ? value.toLocaleString('id-ID') : '';

  return (
    <View style={[styles.wrapper, containerStyle]}>
      {label && <Text style={styles.label}>{label}</Text>}
      <View style={[styles.inputContainer, !!error && styles.inputError]}>
        <Text style={styles.prefix}>Rp</Text>
        <TextInput
          style={styles.input}
          keyboardType="numeric"
          placeholder={placeholder}
          placeholderTextColor={Colors.textMuted}
          value={displayValue}
          onChangeText={handleChangeText}
        />
      </View>
      {!!error && <Text style={styles.errorText}>{error}</Text>}
    </View>
  );
};

const styles = StyleSheet.create({
  wrapper: {
    marginBottom: 14,
  },
  label: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.textPrimary,
    marginBottom: 6,
  },
  inputContainer: {
    height: 48,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    backgroundColor: Colors.surface,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
  },
  inputError: {
    borderColor: Colors.danger,
  },
  prefix: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textSecondary,
    marginRight: 6,
  },
  input: {
    flex: 1,
    height: '100%',
    fontSize: 14,
    color: Colors.textPrimary,
    fontWeight: '600',
  },
  errorText: {
    fontSize: 12,
    color: Colors.danger,
    marginTop: 4,
  },
});
