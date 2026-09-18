import React from 'react';
import { View, TextInput, StyleSheet, TouchableOpacity, ViewStyle } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/Colors';

interface SearchBarProps {
  value: string;
  onChangeText: (text: string) => void;
  placeholder?: string;
  onScanPress?: () => void;
  style?: ViewStyle;
}

export const SearchBar: React.FC<SearchBarProps> = ({
  value,
  onChangeText,
  placeholder = 'Cari no tiket, pelanggan, IMEI...',
  onScanPress,
  style,
}) => {
  return (
    <View style={[styles.container, style]}>
      <Ionicons name="search-outline" size={18} color={Colors.textSecondary} style={styles.icon} />
      <TextInput
        style={styles.input}
        placeholder={placeholder}
        placeholderTextColor={Colors.textMuted}
        value={value}
        onChangeText={onChangeText}
        returnKeyType="search"
      />
      {value.length > 0 && (
        <TouchableOpacity onPress={() => onChangeText('')} style={styles.actionBtn}>
          <Ionicons name="close-circle" size={18} color={Colors.textMuted} />
        </TouchableOpacity>
      )}
      {onScanPress && (
        <TouchableOpacity onPress={onScanPress} style={[styles.actionBtn, styles.scanBtn]}>
          <Ionicons name="qr-code-outline" size={20} color={Colors.primary} />
        </TouchableOpacity>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    height: 44,
    backgroundColor: Colors.surface,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.surfaceBorder,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
  },
  icon: {
    marginRight: 6,
  },
  input: {
    flex: 1,
    height: '100%',
    fontSize: 13,
    color: Colors.textPrimary,
  },
  actionBtn: {
    padding: 4,
    marginLeft: 4,
  },
  scanBtn: {
    borderLeftWidth: 1,
    borderLeftColor: Colors.surfaceBorder,
    paddingLeft: 8,
  },
});
