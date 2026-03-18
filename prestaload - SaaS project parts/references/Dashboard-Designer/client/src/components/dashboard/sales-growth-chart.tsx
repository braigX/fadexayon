import { useSalesGrowth } from "@/hooks/use-dashboard";
import { Skeleton } from "@/components/ui/skeleton";
import { 
  AreaChart, 
  Area, 
  XAxis, 
  YAxis, 
  CartesianGrid, 
  Tooltip, 
  ResponsiveContainer 
} from "recharts";

export function SalesGrowthChart() {
  const { data, isLoading } = useSalesGrowth();

  if (isLoading) {
    return <Skeleton className="h-[350px] w-full rounded-2xl" />;
  }

  const parsedData = data?.map(d => ({
    ...d,
    value: parseFloat(d.value)
  })) || [];

  return (
    <div className="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-border/50 h-full flex flex-col hover-elevate">
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-xl font-bold text-foreground">Sales Growth</h2>
          <p className="text-sm font-medium text-muted-foreground mt-1">Growth percentage over time</p>
        </div>
        <select className="bg-muted/50 border-none text-sm font-bold text-foreground rounded-xl px-4 py-2 appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/20">
          <option>2024</option>
          <option>2023</option>
        </select>
      </div>

      <div className="flex-1 min-h-[220px] w-full -ml-4 mt-auto">
        <ResponsiveContainer width="100%" height="100%">
          <AreaChart data={parsedData} margin={{ top: 10, right: 10, left: -10, bottom: 0 }}>
            <defs>
              <linearGradient id="colorValue" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="hsl(var(--primary))" stopOpacity={0.4}/>
                <stop offset="95%" stopColor="hsl(var(--primary))" stopOpacity={0}/>
              </linearGradient>
            </defs>
            <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="hsl(var(--border))" opacity={0.5} />
            <XAxis 
              dataKey="period" 
              axisLine={false} 
              tickLine={false} 
              tick={{ fontSize: 12, fontWeight: 600, fill: "hsl(var(--muted-foreground))" }} 
              dy={15}
            />
            <YAxis 
              axisLine={false} 
              tickLine={false} 
              tick={{ fontSize: 12, fontWeight: 600, fill: "hsl(var(--muted-foreground))" }}
              tickFormatter={(val) => `${val}%`}
            />
            <Tooltip 
              formatter={(val: number) => [`${val}%`, 'Growth']}
              contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }}
            />
            <Area 
              type="monotone" 
              dataKey="value" 
              stroke="hsl(var(--primary))" 
              strokeWidth={4}
              fillOpacity={1} 
              fill="url(#colorValue)" 
            />
          </AreaChart>
        </ResponsiveContainer>
      </div>
    </div>
  );
}
