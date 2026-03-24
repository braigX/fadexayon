import { db } from "./db";
import {
  kpiStats,
  salesGrowth,
  salesVsTarget,
  productStats,
  type KpiStat,
  type SalesGrowth,
  type SalesVsTarget,
  type ProductStat
} from "@shared/schema";

export interface IStorage {
  getKpiStats(): Promise<KpiStat[]>;
  getSalesGrowth(): Promise<SalesGrowth[]>;
  getSalesVsTarget(): Promise<SalesVsTarget[]>;
  getProductStats(): Promise<ProductStat[]>;
}

export class DatabaseStorage implements IStorage {
  async getKpiStats(): Promise<KpiStat[]> {
    return await db.select().from(kpiStats);
  }
  
  async getSalesGrowth(): Promise<SalesGrowth[]> {
    return await db.select().from(salesGrowth);
  }
  
  async getSalesVsTarget(): Promise<SalesVsTarget[]> {
    return await db.select().from(salesVsTarget);
  }
  
  async getProductStats(): Promise<ProductStat[]> {
    return await db.select().from(productStats);
  }
}

export const storage = new DatabaseStorage();
