import { useProductStats } from "@/hooks/use-dashboard";
import { Skeleton } from "@/components/ui/skeleton";
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip } from "recharts";
import { MoreVertical, Download, RefreshCw, Share2 } from "lucide-react";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Button } from "@/components/ui/button";

export function ProductStatsChart() {
  const { data, isLoading } = useProductStats();

  if (isLoading) {
    return <Skeleton className="h-[350px] w-full rounded-2xl" />;
  }

  const parsedData = data?.map(d => ({
    ...d,
    value: parseFloat(d.value)
  })) || [];

  return (
    <div className="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-border/50 h-full flex flex-col hover-elevate">
      <div className="flex items-center justify-between mb-2">
        <h2 className="text-xl font-bold text-foreground">Products Statistic</h2>
        <Popover>
          <PopoverTrigger asChild>
            <button className="text-muted-foreground hover:text-foreground h-8 w-8 flex items-center justify-center rounded-lg hover:bg-muted/50 transition-colors">
              <MoreVertical className="w-5 h-5" />
            </button>
          </PopoverTrigger>
          <PopoverContent className="w-48 p-1 rounded-xl border-border/40 shadow-xl" align="end">
            <div className="space-y-1">
              <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-xs font-bold rounded-lg">
                <Download className="w-4 h-4" /> Download Report
              </Button>
              <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-xs font-bold rounded-lg">
                <RefreshCw className="w-4 h-4" /> Refresh Data
              </Button>
              <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-xs font-bold rounded-lg">
                <Share2 className="w-4 h-4" /> Share Statistics
              </Button>
            </div>
          </PopoverContent>
        </Popover>
      </div>
      
      <div className="relative flex-1 flex items-center justify-center min-h-[220px]">
        {/* Center Decorative Image/Avatar */}
        <div className="absolute inset-0 flex items-center justify-center pointer-events-none z-0">
          <div className="w-[100px] h-[100px] rounded-full overflow-hidden border-4 border-white shadow-md bg-muted flex items-center justify-center">
            {/* unsplash tech product image */}
            <img 
              src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=200&fit=crop" 
              alt="Center avatar" 
              className="w-full h-full object-cover opacity-80"
            />
          </div>
        </div>

        <ResponsiveContainer width="100%" height="100%" className="z-10 relative">
          <PieChart>
            <Pie
              data={parsedData}
              cx="50%"
              cy="50%"
              innerRadius={70}
              outerRadius={95}
              stroke="none"
              paddingAngle={4}
              dataKey="value"
              cornerRadius={8}
            >
              {parsedData.map((entry, index) => (
                <Cell key={`cell-${index}`} fill={entry.color} />
              ))}
            </Pie>
            <Tooltip 
              formatter={(value: number) => [`${value}%`, 'Share']}
              contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }}
            />
          </PieChart>
        </ResponsiveContainer>
      </div>

      <div className="grid grid-cols-2 gap-y-3 gap-x-2 mt-4">
        {parsedData.map((item, i) => (
          <div key={i} className="flex items-center gap-2">
            <div 
              className="w-3 h-3 rounded-md" 
              style={{ backgroundColor: item.color }} 
            />
            <span className="text-sm font-bold text-foreground">{item.category}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
