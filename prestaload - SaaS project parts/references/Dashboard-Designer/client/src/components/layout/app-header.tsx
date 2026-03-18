import { Search, Bell, User, Settings, LogOut, MessageSquare, Shield } from "lucide-react";
import { format } from "date-fns";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { SidebarTrigger } from "@/components/ui/sidebar";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Separator } from "@/components/ui/separator";

export function AppHeader() {
  const today = format(new Date(), "dd MMM yyyy");

  const notifications = [
    { id: 1, title: "New Sale", message: "You have a new sale of $250.00", time: "2 min ago", icon: MessageSquare },
    { id: 2, title: "System Update", message: "System will be updated at 12:00 PM", time: "1 hour ago", icon: Shield },
    { id: 3, title: "New Message", message: "Jane Doe sent you a message", time: "2 hours ago", icon: User },
  ];

  return (
    <header className="flex flex-col md:flex-row items-start md:items-center justify-between py-3 px-6 lg:px-10 bg-background/50 backdrop-blur-sm sticky top-0 z-10 border-b border-border/40 gap-3 md:gap-0">
      <div className="flex items-center gap-3">
        <SidebarTrigger className="md:hidden" />
        <div>
          <h1 className="text-xl md:text-2xl font-bold text-foreground tracking-tight">Sales Report</h1>
          <p className="text-muted-foreground font-medium text-xs mt-0">{today}</p>
        </div>
      </div>

      <div className="flex items-center gap-3 sm:gap-4 w-full md:w-auto">
        <div className="relative flex-1 md:w-64">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-muted-foreground" />
          <Input 
            placeholder="Search report..." 
            className="w-full pl-9 pr-3 py-4 h-9 text-xs rounded-lg border-border/60 bg-white shadow-sm focus-visible:ring-primary focus-visible:border-primary transition-all"
          />
        </div>

        <Popover>
          <PopoverTrigger asChild>
            <Button size="icon" variant="outline" className="rounded-lg h-9 w-9 shrink-0 border-border/60 bg-white relative hover-elevate">
              <Bell className="w-4 h-4 text-foreground/80" />
              <span className="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-red-500 border border-white" />
            </Button>
          </PopoverTrigger>
          <PopoverContent className="w-80 p-0 rounded-2xl border-border/40 shadow-xl overflow-hidden" align="end">
            <div className="p-4 bg-muted/30 border-b border-border/40">
              <h3 className="font-bold text-foreground">Notifications</h3>
            </div>
            <div className="max-h-[300px] overflow-y-auto">
              {notifications.map((n) => (
                <div key={n.id} className="p-4 flex gap-3 hover:bg-muted/50 transition-colors cursor-pointer border-b border-border/20 last:border-0">
                  <div className="h-9 w-9 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <n.icon className="w-5 h-5 text-primary" />
                  </div>
                  <div>
                    <p className="text-sm font-bold text-foreground">{n.title}</p>
                    <p className="text-xs text-muted-foreground line-clamp-1">{n.message}</p>
                    <p className="text-[10px] text-muted-foreground mt-1 font-medium">{n.time}</p>
                  </div>
                </div>
              ))}
            </div>
            <div className="p-3 bg-muted/20 text-center">
              <Button variant="ghost" size="sm" className="text-xs font-bold text-primary hover:text-primary">
                View all notifications
              </Button>
            </div>
          </PopoverContent>
        </Popover>

        <div className="flex items-center gap-2 pl-2 sm:pl-3 border-l border-border/60 shrink-0">
          <div className="hidden sm:block text-right">
            <p className="text-xs font-bold text-foreground leading-tight">Jane Doe</p>
            <p className="text-[10px] text-muted-foreground font-medium">Admin Store</p>
          </div>
          
          <Popover>
            <PopoverTrigger asChild>
              <button className="h-9 w-9 rounded-lg overflow-hidden border border-border shadow-sm shrink-0 hover-elevate transition-all active:scale-95 outline-none">
                <img 
                  src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&h=150&fit=crop" 
                  alt="Jane Doe profile" 
                  className="h-full w-full object-cover"
                />
              </button>
            </PopoverTrigger>
            <PopoverContent className="w-56 p-2 rounded-2xl border-border/40 shadow-xl" align="end">
              <div className="p-2 mb-1">
                <p className="text-sm font-bold text-foreground">Jane Doe</p>
                <p className="text-xs text-muted-foreground font-medium">jane@sasico.com</p>
              </div>
              <Separator className="my-1 opacity-50" />
              <div className="space-y-1">
                <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-sm font-medium rounded-lg">
                  <User className="w-4 h-4" /> Profile
                </Button>
                <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-sm font-medium rounded-lg">
                  <Settings className="w-4 h-4" /> Settings
                </Button>
              </div>
              <Separator className="my-1 opacity-50" />
              <Button variant="ghost" className="w-full justify-start gap-2 h-9 text-sm font-medium rounded-lg text-red-500 hover:text-red-600 hover:bg-red-50">
                <LogOut className="w-4 h-4" /> Logout
              </Button>
            </PopoverContent>
          </Popover>
        </div>
      </div>
    </header>
  );
}
