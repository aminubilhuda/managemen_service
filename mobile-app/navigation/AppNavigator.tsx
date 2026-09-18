import React from 'react';
import { View, ActivityIndicator, StyleSheet, Platform } from 'react-native';
import { NavigationContainer, createNavigationContainerRef } from '@react-navigation/native';

export const navigationRef = createNavigationContainerRef<any>();

if (typeof window !== 'undefined') {
  (window as any).__navigation = navigationRef;
}
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/Colors';
import { useAuthStore } from '../store/useAuthStore';

// Auth Screens
import { LoginScreen } from '../screens/auth/LoginScreen';
import { ServerConfigScreen } from '../screens/auth/ServerConfigScreen';

// Main Tab Screens
import { DashboardScreen } from '../screens/dashboard/DashboardScreen';
import { TiketListScreen } from '../screens/tiket/TiketListScreen';
import { InvoiceListScreen } from '../screens/kasir/InvoiceListScreen';
import { MasterScreen } from '../screens/master/MasterScreen';
import { AkunScreen } from '../screens/akun/AkunScreen';

// Sub / Detail Screens
import { TiketIntakeScreen } from '../screens/tiket/TiketIntakeScreen';
import { TiketDetailScreen } from '../screens/tiket/TiketDetailScreen';
import { TiketScanScreen } from '../screens/tiket/TiketScanScreen';
import { InvoiceCreateScreen } from '../screens/kasir/InvoiceCreateScreen';
import { InvoiceDetailScreen } from '../screens/kasir/InvoiceDetailScreen';
import { PengeluaranScreen } from '../screens/kasir/PengeluaranScreen';
import { PelangganDetailScreen } from '../screens/master/PelangganDetailScreen';

const Stack = createNativeStackNavigator();
const Tab = createBottomTabNavigator();

const MainTabNavigator = () => {
  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarActiveTintColor: Colors.primary,
        tabBarInactiveTintColor: Colors.textSecondary,
        tabBarStyle: {
          backgroundColor: Colors.surface,
          borderTopWidth: 1,
          borderTopColor: Colors.surfaceBorder,
          height: Platform.OS === 'web' ? 68 : 64,
          paddingBottom: Platform.OS === 'web' ? 12 : 10,
          paddingTop: 6,
        },
        tabBarLabelStyle: {
          fontSize: 11,
          fontWeight: '600',
        },
        tabBarIcon: ({ color, size, focused }) => {
          let iconName: keyof typeof Ionicons.glyphMap = 'home';

          if (route.name === 'DashboardTab') {
            iconName = focused ? 'home' : 'home-outline';
          } else if (route.name === 'ServisTab') {
            iconName = focused ? 'build' : 'build-outline';
          } else if (route.name === 'KasirTab') {
            iconName = focused ? 'receipt' : 'receipt-outline';
          } else if (route.name === 'MasterTab') {
            iconName = focused ? 'cube' : 'cube-outline';
          } else if (route.name === 'AkunTab') {
            iconName = focused ? 'person' : 'person-outline';
          }

          return <Ionicons name={iconName} size={size} color={color} />;
        },
      })}
    >
      <Tab.Screen 
        name="DashboardTab" 
        component={DashboardScreen} 
        options={{ tabBarLabel: 'Beranda' }} 
      />
      <Tab.Screen 
        name="ServisTab" 
        component={TiketListScreen} 
        options={{ tabBarLabel: 'Servis' }} 
      />
      <Tab.Screen 
        name="KasirTab" 
        component={InvoiceListScreen} 
        options={{ tabBarLabel: 'Kasir' }} 
      />
      <Tab.Screen 
        name="MasterTab" 
        component={MasterScreen} 
        options={{ tabBarLabel: 'Master' }} 
      />
      <Tab.Screen 
        name="AkunTab" 
        component={AkunScreen} 
        options={{ tabBarLabel: 'Akun' }} 
      />
    </Tab.Navigator>
  );
};

export const AppNavigator = () => {
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  const isLoading = useAuthStore((state) => state.isLoading);

  if (isLoading) {
    return (
      <View style={styles.splashContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
      </View>
    );
  }

  return (
    <NavigationContainer ref={navigationRef}>
      <Stack.Navigator screenOptions={{ headerShown: false }}>
        {!isAuthenticated ? (
          // Unauthenticated Stack
          <>
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="ServerConfig" component={ServerConfigScreen} />
          </>
        ) : (
          // Authenticated Stack
          <>
            <Stack.Screen name="MainTabs" component={MainTabNavigator} />
            <Stack.Screen name="TiketIntake" component={TiketIntakeScreen} />
            <Stack.Screen name="TiketDetail" component={TiketDetailScreen} />
            <Stack.Screen name="TiketScan" component={TiketScanScreen} />
            <Stack.Screen name="InvoiceCreate" component={InvoiceCreateScreen} />
            <Stack.Screen name="InvoiceDetail" component={InvoiceDetailScreen} />
            <Stack.Screen name="Pengeluaran" component={PengeluaranScreen} />
            <Stack.Screen name="PelangganDetail" component={PelangganDetailScreen} />
            <Stack.Screen name="ServerConfig" component={ServerConfigScreen} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
};

const styles = StyleSheet.create({
  splashContainer: {
    flex: 1,
    backgroundColor: Colors.background,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
