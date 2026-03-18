import { useQuery } from "@tanstack/react-query";
import { api } from "@shared/routes";

// ============================================
// DASHBOARD HOOKS
// Fetches data for KPI cards and charts
// ============================================

export function useKpis() {
  return useQuery({
    queryKey: [api.dashboard.kpis.path],
    queryFn: async () => {
      const res = await fetch(api.dashboard.kpis.path, { credentials: "include" });
      if (!res.ok) {
        if (res.status === 404) return generateMockKpis(); // Fallback for pure UI testing
        throw new Error("Failed to fetch KPIs");
      }
      return api.dashboard.kpis.responses[200].parse(await res.json());
    },
  });
}

export function useSalesGrowth() {
  return useQuery({
    queryKey: [api.dashboard.salesGrowth.path],
    queryFn: async () => {
      const res = await fetch(api.dashboard.salesGrowth.path, { credentials: "include" });
      if (!res.ok) {
        if (res.status === 404) return generateMockSalesGrowth();
        throw new Error("Failed to fetch Sales Growth");
      }
      return api.dashboard.salesGrowth.responses[200].parse(await res.json());
    },
  });
}

export function useSalesVsTarget() {
  return useQuery({
    queryKey: [api.dashboard.salesVsTarget.path],
    queryFn: async () => {
      const res = await fetch(api.dashboard.salesVsTarget.path, { credentials: "include" });
      if (!res.ok) {
        if (res.status === 404) return generateMockSalesVsTarget();
        throw new Error("Failed to fetch Sales VS Target");
      }
      return api.dashboard.salesVsTarget.responses[200].parse(await res.json());
    },
  });
}

export function useProductStats() {
  return useQuery({
    queryKey: [api.dashboard.productStats.path],
    queryFn: async () => {
      const res = await fetch(api.dashboard.productStats.path, { credentials: "include" });
      if (!res.ok) {
        if (res.status === 404) return generateMockProductStats();
        throw new Error("Failed to fetch Product Stats");
      }
      return api.dashboard.productStats.responses[200].parse(await res.json());
    },
  });
}

// ============================================
// MOCK DATA FALLBACKS (If backend endpoints missing)
// ============================================

function generateMockKpis() {
  return [
    { id: 1, title: "Total Sales", value: "$456.789", change: "12.5", trend: "up", isPrimary: 1 },
    { id: 2, title: "Total Orders", value: "34.567", change: "8.2", trend: "up", isPrimary: 0 },
    { id: 3, title: "Total Customers", value: "13.542", change: "2.4", trend: "down", isPrimary: 0 },
    { id: 4, title: "Total Refund", value: "1.654", change: "1.2", trend: "down", isPrimary: 0 },
  ];
}

function generateMockSalesGrowth() {
  return [
    { id: 1, period: "Jan", value: "30" },
    { id: 2, period: "Feb", value: "45" },
    { id: 3, period: "Mar", value: "40" },
    { id: 4, period: "Apr", value: "65" },
    { id: 5, period: "May", value: "55" },
    { id: 6, period: "Jun", value: "85" },
    { id: 7, period: "Jul", value: "70" },
    { id: 8, period: "Aug", value: "95" },
  ];
}

function generateMockSalesVsTarget() {
  return [
    { id: 1, month: "Jan", actual: "45", target: "65" },
    { id: 2, month: "Feb", actual: "70", target: "80" },
    { id: 3, month: "Mar", actual: "55", target: "60" },
    { id: 4, month: "Apr", actual: "90", target: "75" },
    { id: 5, month: "May", actual: "85", target: "95" },
    { id: 6, month: "Jun", actual: "65", target: "70" },
    { id: 7, month: "Jul", actual: "95", target: "85" },
  ];
}

function generateMockProductStats() {
  return [
    { id: 1, category: "Electronic", value: "45", color: "hsl(var(--chart-1))" },
    { id: 2, category: "Fashion", value: "25", color: "hsl(var(--chart-2))" },
    { id: 3, category: "Furniture", value: "20", color: "hsl(var(--chart-3))" },
    { id: 4, category: "Food", value: "10", color: "hsl(var(--chart-4))" },
  ];
}
