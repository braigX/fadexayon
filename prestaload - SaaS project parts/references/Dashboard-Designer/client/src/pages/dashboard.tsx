import { KpiCards } from "@/components/dashboard/kpi-cards";
import { ActualVsTargetChart } from "@/components/dashboard/actual-vs-target-chart";
import { ProductStatsChart } from "@/components/dashboard/product-stats-chart";
import { SalesGrowthChart } from "@/components/dashboard/sales-growth-chart";

export default function Dashboard() {
  return (
    <div className="flex flex-col gap-8 max-w-[1600px] mx-auto pb-10">
      
      {/* Top KPI Row */}
      <section>
        <KpiCards />
      </section>

      {/* Main Charts Area */}
      <section className="grid grid-cols-1 xl:grid-cols-12 gap-8">
        
        {/* Left Col: Main Bar Chart (occupies 7 columns on xl) */}
        <div className="xl:col-span-7 h-[450px]">
          <ActualVsTargetChart />
        </div>

        {/* Right Col: Donut & Line charts stacked (occupies 5 columns on xl) */}
        <div className="xl:col-span-5 flex flex-col gap-8">
          
          <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-8 h-full xl:h-[450px]">
            {/* Donut Chart */}
            <div className="h-[350px] xl:h-[200px] 2xl:h-[210px]">
              <ProductStatsChart />
            </div>
            
            {/* Line Chart */}
            <div className="h-[350px] xl:h-[210px] 2xl:h-[210px]">
              <SalesGrowthChart />
            </div>
          </div>
          
        </div>
      </section>
      
    </div>
  );
}
