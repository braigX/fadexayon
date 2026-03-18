import { pgTable, text, serial, numeric, integer, timestamp } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

export const kpiStats = pgTable("kpi_stats", {
  id: serial("id").primaryKey(),
  title: text("title").notNull(),
  value: text("value").notNull(),
  change: numeric("change").notNull(),
  trend: text("trend").notNull(), // 'up' or 'down'
  isPrimary: integer("is_primary").default(0), // 1 for the green main card
});

export const salesGrowth = pgTable("sales_growth", {
  id: serial("id").primaryKey(),
  period: text("period").notNull(),
  value: numeric("value").notNull(),
});

export const salesVsTarget = pgTable("sales_vs_target", {
  id: serial("id").primaryKey(),
  month: text("month").notNull(),
  actual: numeric("actual").notNull(),
  target: numeric("target").notNull(),
});

export const productStats = pgTable("product_stats", {
  id: serial("id").primaryKey(),
  category: text("category").notNull(),
  value: numeric("value").notNull(),
  color: text("color").notNull(),
});

export type KpiStat = typeof kpiStats.$inferSelect;
export type SalesGrowth = typeof salesGrowth.$inferSelect;
export type SalesVsTarget = typeof salesVsTarget.$inferSelect;
export type ProductStat = typeof productStats.$inferSelect;
