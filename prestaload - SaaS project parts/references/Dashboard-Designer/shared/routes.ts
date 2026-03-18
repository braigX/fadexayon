import { z } from 'zod';
import { kpiStats, salesGrowth, salesVsTarget, productStats } from './schema';

export const errorSchemas = {
  internal: z.object({
    message: z.string(),
  }),
};

export const api = {
  dashboard: {
    kpis: {
      method: 'GET' as const,
      path: '/api/dashboard/kpis' as const,
      responses: {
        200: z.array(z.custom<typeof kpiStats.$inferSelect>()),
      },
    },
    salesGrowth: {
      method: 'GET' as const,
      path: '/api/dashboard/sales-growth' as const,
      responses: {
        200: z.array(z.custom<typeof salesGrowth.$inferSelect>()),
      },
    },
    salesVsTarget: {
      method: 'GET' as const,
      path: '/api/dashboard/sales-vs-target' as const,
      responses: {
        200: z.array(z.custom<typeof salesVsTarget.$inferSelect>()),
      },
    },
    productStats: {
      method: 'GET' as const,
      path: '/api/dashboard/product-stats' as const,
      responses: {
        200: z.array(z.custom<typeof productStats.$inferSelect>()),
      },
    },
  },
};

export function buildUrl(path: string, params?: Record<string, string | number>): string {
  let url = path;
  if (params) {
    Object.entries(params).forEach(([key, value]) => {
      if (url.includes(`:${key}`)) {
        url = url.replace(`:${key}`, String(value));
      }
    });
  }
  return url;
}

export type KpiStatsResponse = z.infer<typeof api.dashboard.kpis.responses[200]>;
export type SalesGrowthResponse = z.infer<typeof api.dashboard.salesGrowth.responses[200]>;
export type SalesVsTargetResponse = z.infer<typeof api.dashboard.salesVsTarget.responses[200]>;
export type ProductStatsResponse = z.infer<typeof api.dashboard.productStats.responses[200]>;
