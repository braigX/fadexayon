import { useSalesVsTarget } from "@/hooks/use-dashboard";
import { Skeleton } from "@/components/ui/skeleton";
import { 
  BarChart, 
  Bar, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer 
} from "recharts";

export function ActualVsTargetChart() {
  const { data, isLoading } = useSalesVsTarget();

  if (isLoading) {
    return <Skeleton className="h-[350px] w-full rounded-2xl" />;
  }

  // Convert string values to numbers for Recharts
  const parsedData = data?.map(d => ({
    ...d,
    actual: parseFloat(d.actual),
    target: parseFloat(d.target),
  })) || [];

  return (
    <div className="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-border/50 h-full flex flex-col hover-elevate">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h2 className="text-xl font-bold text-foreground">Actual Sales VS Target</h2>
          <p className="text-sm font-medium text-muted-foreground mt-1">Monthly performance comparison</p>
        </div>
        <div className="flex gap-4">
          <div className="flex items-center gap-2">
            <span className="w-3 h-3 rounded-full bg-muted-foreground/20" />
            <span className="text-xs font-bold text-muted-foreground">Target</span>
          </div>
          <div className="flex items-center gap-2">
            <span className="w-3 h-3 rounded-full bg-primary" />
            <span className="text-xs font-bold text-foreground">Actual</span>
          </div>
        </div>
      </div>

      <div className="flex-1 min-h-[250px] w-full -ml-4 mt-auto">
        <ResponsiveContainer width="100%" height="100%">
          <BarChart data={parsedData} margin={{ top: 10, right: 10, left: 0, bottom: 0 }}>
            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="hsl(var(--border))" opacity={0.5} />
            <XAxis 
              dataKey="month" 
              axisLine={false} 
              tickLine={false} 
              tick={{ fontSize: 13, fontWeight: 600, fill: "hsl(var(--muted-foreground))" }} 
              dy={15}
            />
            <YAxis 
              axisLine={false} 
              tickLine={false} 
              tick={{ fontSize: 13, fontWeight: 600, fill: "hsl(var(--muted-foreground))" }} 
              dx={-10}
            />
            <Tooltip 
              cursor={{ fill: 'transparent' }}
              contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }}
            />
            
            {/* Overlapping bars using different xAxisId */}
            <XAxis dataKey="month" xAxisId="target-axis" hide />
            <XAxis dataKey="month" xAxisId="actual-axis" hide />
            
            <Bar 
              dataKey="target" 
              xAxisId="target-axis"
              fill="hsl(var(--chart-5))" 
              radius={[6, 6, 6, 6]} 
              barSize={32}
            />
            <Bar 
              dataKey="actual" 
              xAxisId="actual-axis"
              fill="hsl(var(--primary))" 
              radius={[6, 6, 6, 6]} 
              barSize={16}
            />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}
