import { ArrowUpRight, ArrowDownRight, Activity } from "lucide-react";
import { useKpis } from "@/hooks/use-dashboard";
import { Skeleton } from "@/components/ui/skeleton";
import { ResponsiveContainer, LineChart, Line } from "recharts";

export function KpiCards() {
  const { data: kpis, isLoading } = useKpis();

  if (isLoading) {
    return (
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {[1, 2, 3, 4].map((i) => (
          <Skeleton key={i} className="h-[160px] w-full rounded-2xl" />
        ))}
      </div>
    );
  }

  if (!kpis) return null;

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
      {kpis.map((kpi) => {
        const isPrimary = kpi.isPrimary === 1;
        const isUp = kpi.trend === "up";
        
        // Mock sparkline data for visual aesthetic
        const sparklineData = Array.from({ length: 10 }).map((_, i) => ({
          value: isUp ? 20 + i * 2 + Math.random() * 10 : 40 - i * 2 + Math.random() * 10
        }));

        return (
          <div
            key={kpi.id}
            className={`
              relative p-6 rounded-2xl hover-elevate overflow-hidden border
              ${isPrimary 
                ? "bg-primary text-primary-foreground border-primary/20 shadow-lg shadow-primary/20" 
                : "bg-white text-foreground border-border/50 shadow-sm"
              }
            `}
          >
            {/* Background Decoration */}
            {isPrimary && (
              <div className="absolute -right-6 -top-6 text-primary-foreground/10">
                <Activity className="w-32 h-32" />
              </div>
            )}

            <h3 className={`text-sm font-semibold mb-2 relative z-10 ${isPrimary ? "text-primary-foreground/80" : "text-muted-foreground"}`}>
              {kpi.title}
            </h3>
            
            <div className="flex items-end justify-between relative z-10">
              <div>
                <p className="text-3xl md:text-4xl font-extrabold tracking-tight">
                  {kpi.value}
                </p>
                
                <div className="flex items-center gap-2 mt-4">
                  <div className={`
                    flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full
                    ${isPrimary 
                      ? "bg-primary-foreground/15 text-primary-foreground" 
                      : isUp 
                        ? "bg-green-100 text-green-700" 
                        : "bg-red-100 text-red-700"
                    }
                  `}>
                    {isUp ? <ArrowUpRight className="w-3 h-3" /> : <ArrowDownRight className="w-3 h-3" />}
                    {kpi.change}%
                  </div>
                  <span className={`text-xs font-medium ${isPrimary ? "text-primary-foreground/70" : "text-muted-foreground"}`}>
                    vs last month
                  </span>
                </div>
              </div>

              {/* Minimal Sparkline */}
              <div className="w-16 h-12 mb-1">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={sparklineData}>
                    <Line 
                      type="monotone" 
                      dataKey="value" 
                      stroke={isPrimary ? "currentColor" : (isUp ? "hsl(var(--chart-1))" : "hsl(var(--destructive))")} 
                      strokeWidth={2.5} 
                      dot={false}
                      isAnimationActive={true}
                    />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </div>
          </div>
        );
      })}
    </div>
  );
}
