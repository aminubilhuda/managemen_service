import React from 'react';
import { 
  TouchableOpacity, 
  Text, 
  ActivityIndicator, 
  StyleSheet, 
  ViewStyle, 
  TextStyle 
} from 'react-native';
import { Colors } from '../constants/Colors';

interface ButtonProps {
  title: string;
  onPress: () => void;
  variant?: 'primary' | 'secondary' | 'outline' | 'danger';
  size?: 'small' | 'medium' | 'large';
  loading?: boolean;
  disabled?: boolean;
  style?: ViewStyle;
  textStyle?: TextStyle;
  icon?: React.ReactNode;
}

export const Button: React.FC<ButtonProps> = ({
  title,
  onPress,
  variant = 'primary',
  size = 'medium',
  loading = false,
  disabled = false,
  style,
  textStyle,
  icon,
}) => {
  const getContainerStyle = () => {
    let base: ViewStyle = styles.base;

    switch (variant) {
      case 'primary':
        base = { ...base, backgroundColor: Colors.primary };
        break;
      case 'secondary':
        base = { ...base, backgroundColor: Colors.primaryLight };
        break;
      case 'outline':
        base = { ...base, backgroundColor: 'transparent', borderWidth: 1, borderColor: Colors.surfaceBorder };
        break;
      case 'danger':
        base = { ...base, backgroundColor: Colors.danger };
        break;
    }

    if (size === 'small') {
      base = { ...base, height: 36, paddingHorizontal: 12 };
    } else if (size === 'large') {
      base = { ...base, height: 52, paddingHorizontal: 20 };
    }

    if (disabled || loading) {
      base = { ...base, opacity: 0.6 };
    }

    return base;
  };

  const getTextStyle = () => {
    let base: TextStyle = styles.textBase;

    switch (variant) {
      case 'primary':
      case 'danger':
        base = { ...base, color: '#FFFFFF' };
        break;
      case 'secondary':
      case 'outline':
        base = { ...base, color: Colors.primary };
        break;
    }

    if (size === 'small') {
      base = { ...base, fontSize: 13 };
    } else if (size === 'large') {
      base = { ...base, fontSize: 16 };
    }

    return base;
  };

  return (
    <TouchableOpacity
      activeOpacity={0.7}
      onPress={onPress}
      disabled={disabled || loading}
      style={[getContainerStyle(), style]}
    >
      {loading ? (
        <ActivityIndicator 
          size="small" 
          color={variant === 'primary' || variant === 'danger' ? '#FFFFFF' : Colors.primary} 
        />
      ) : (
        <>
          {icon}
          <Text style={[getTextStyle(), icon ? { marginLeft: 8 } : undefined, textStyle]}>
            {title}
          </Text>
        </>
      )}
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  base: {
    height: 48,
    borderRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 16,
  },
  textBase: {
    fontSize: 14,
    fontWeight: '600',
  },
});
