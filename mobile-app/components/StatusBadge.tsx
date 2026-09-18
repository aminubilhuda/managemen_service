import React from 'react';
import { View, Text, StyleSheet, ViewStyle } from 'react-native';
import { Colors } from '../constants/Colors';
import { StatusTiket } from '../types/api';

interface StatusBadgeProps {
  status: StatusTiket | 'unpaid' | 'partial' | 'paid' | 'void' | string;
  label?: string;
  type?: 'tiket' | 'invoice';
  style?: ViewStyle;
}

export const StatusBadge: React.FC<StatusBadgeProps> = ({
  status,
  label,
  type = 'tiket',
  style,
}) => {
  let bg = '#F1F5F9';
  let text = '#475569';
  let border = '#CBD5E1';
  let displayLabel = label || status;

  if (type === 'invoice' || ['unpaid', 'partial', 'paid', 'void'].includes(status)) {
    const inv = Colors.payment[status as keyof typeof Colors.payment];
    if (inv) {
      bg = inv.bg;
      text = inv.text;
      border = inv.border;
      displayLabel = label || inv.label;
    }
  } else {
    const tkt = Colors.status[status as keyof typeof Colors.status];
    if (tkt) {
      bg = tkt.bg;
      text = tkt.text;
      border = tkt.border;
      displayLabel = label || tkt.label;
    }
  }

  return (
    <View style={[styles.badge, { backgroundColor: bg, borderColor: border }, style]}>
      <Text style={[styles.text, { color: text }]}>{displayLabel}</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  badge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    borderWidth: 1,
    alignSelf: 'flex-start',
    alignItems: 'center',
    justifyContent: 'center',
  },
  text: {
    fontSize: 11,
    fontWeight: '600',
    textTransform: 'capitalize',
  },
});
