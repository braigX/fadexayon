import type { Express } from "express";
import type { Server } from "http";
import { storage } from "./storage";
import { api } from "@shared/routes";
import { db } from "./db";
import { kpiStats, salesGrowth, salesVsTarget, productStats } from "@shared/schema";

export async function registerRoutes(
  httpServer: Server,
  app: Express
): Promise<Server> {
  app.get(api.dashboard.kpis.path, async (req, res) => {
    const data = await storage.getKpiStats();
    res.json(data);
  });

  app.get(api.dashboard.salesGrowth.path, async (req, res) => {
    const data = await storage.getSalesGrowth();
    res.json(data);
  });

  app.get(api.dashboard.salesVsTarget.path, async (req, res) => {
    const data = await storage.getSalesVsTarget();
    res.json(data);
  });

  app.get(api.dashboard.productStats.path, async (req, res) => {
    const data = await storage.getProductStats();
    res.json(data);
  });

  // Seed database on startup
  seedDatabase().catch(console.error);

  return httpServer;
}

async function seedDatabase() {
  const existingKpis = await storage.getKpiStats();
  if (existingKpis.length === 0) {
    await db.insert(kpiStats).values([
      { title: "Total Sales", value: "$456.789", change: "1.23", trend: "up", isPrimary: 1 },
      { title: "Total Orders", value: "34.567", change: "0.54", trend: "up", isPrimary: 0 },
      { title: "Total Delivery", value: "13.542", change: "1.89", trend: "up", isPrimary: 0 },
      { title: "Total Products", value: "1.654", change: "-4.56", trend: "down", isPrimary: 0 },
    ]);

    await db.insert(salesGrowth).values([
      { period: "1D", value: "12000" },
      { period: "1W", value: "15000" },
      { period: "1M", value: "18000" },
      { period: "4M", value: "13000" },
      { period: "8M", value: "22000" },
      { period: "1Y", value: "15026" },
    ]);

    await db.insert(salesVsTarget).values([
      { month: "jan", actual: "10", target: "20" },
      { month: "feb", actual: "21", target: "27" },
      { month: "mar", actual: "6", target: "15" },
      { month: "apr", actual: "0", target: "0" },
      { month: "may", actual: "22", target: "25" },
      { month: "jun", actual: "9", target: "20" },
      { month: "jul", actual: "13", target: "24" },
    ]);

    await db.insert(productStats).values([
      { category: "Electronic", value: "120500", color: "#b1f25f" }, // Lime green
      { category: "Fashion", value: "1342050", color: "#171717" }, // Black
      { category: "Furniture", value: "2002", color: "#22c55e" }, // Darker green
      { category: "Food", value: "0", color: "#d1d5db" }, // Gray
    ]);
  }
}
