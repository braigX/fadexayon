import { 
  LayoutDashboard, 
  FileText, 
  MapPin, 
  Package, 
  History, 
  Settings, 
  LogOut,
  Hexagon
} from "lucide-react";
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarFooter,
} from "@/components/ui/sidebar";
import { Button } from "@/components/ui/button";

const menuItems = [
  { title: "Dashboard", url: "#", icon: LayoutDashboard, active: false },
  { title: "Report", url: "#", icon: FileText, active: false },
  { title: "Delivery", url: "#", icon: MapPin, active: true }, // Set active for design match
  { title: "Products", url: "#", icon: Package, active: false },
  { title: "History", url: "#", icon: History, active: false },
  { title: "Setting", url: "#", icon: Settings, active: false },
];

export function AppSidebar() {
  return (
    <Sidebar className="border-r border-border/50 shadow-sm z-20">
      <SidebarHeader className="p-6">
        <div className="flex items-center gap-2 px-2 text-foreground font-bold text-xl">
          <div className="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-primary-foreground shadow-sm">
            <Hexagon className="w-5 h-5 fill-current" />
          </div>
          Sasico
        </div>
      </SidebarHeader>

      <SidebarContent className="px-3">
        <SidebarGroup>
          <SidebarGroupContent>
            <SidebarMenu className="gap-2">
              {menuItems.map((item) => (
                <SidebarMenuItem key={item.title}>
                  <SidebarMenuButton 
                    asChild 
                    isActive={item.active}
                    className={`
                      px-4 py-6 rounded-xl transition-all duration-200
                      ${item.active 
                        ? "bg-sidebar-primary text-sidebar-primary-foreground shadow-md hover:bg-sidebar-primary/90" 
                        : "text-muted-foreground hover:bg-muted hover:text-foreground"
                      }
                    `}
                  >
                    <a href={item.url} className="flex items-center gap-3">
                      <item.icon className={`w-5 h-5 ${item.active ? "text-primary" : ""}`} />
                      <span className="font-semibold text-[15px]">{item.title}</span>
                    </a>
                  </SidebarMenuButton>
                </SidebarMenuItem>
              ))}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>
      </SidebarContent>

      <SidebarFooter className="p-6 pb-8">
        <SidebarMenu>
          <SidebarMenuItem className="mb-6">
            <SidebarMenuButton asChild className="px-4 py-6 rounded-xl text-muted-foreground hover:bg-muted hover:text-foreground transition-all duration-200">
              <a href="#logout" className="flex items-center gap-3">
                <LogOut className="w-5 h-5" />
                <span className="font-semibold text-[15px]">Logout</span>
              </a>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
        
        {/* Get Pro Access Card */}
        <div className="bg-primary/10 rounded-2xl p-5 border border-primary/20 relative overflow-hidden group">
          <div className="absolute -right-4 -top-4 w-16 h-16 bg-primary/20 rounded-full blur-xl group-hover:scale-150 transition-transform duration-500" />
          <h4 className="font-bold text-foreground mb-1">Get Pro Access</h4>
          <p className="text-sm text-foreground/70 mb-4 line-clamp-2">
            Unlock premium features and advanced analytics.
          </p>
          <Button 
            className="w-full bg-primary text-primary-foreground hover:bg-primary/90 shadow-md shadow-primary/20 font-bold rounded-xl"
          >
            Upgrade Now
          </Button>
        </div>
      </SidebarFooter>
    </Sidebar>
  );
}
