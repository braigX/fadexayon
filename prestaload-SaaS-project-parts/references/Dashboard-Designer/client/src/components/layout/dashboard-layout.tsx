import { ReactNode } from "react";
import { SidebarProvider } from "@/components/ui/sidebar";
import { AppSidebar } from "./app-sidebar";
import { AppHeader } from "./app-header";

export function DashboardLayout({ children }: { children: ReactNode }) {
  // Configured inline styles as per shadcn instructions
  const style = {
    "--sidebar-width": "17rem",
    "--sidebar-width-icon": "5rem",
  } as React.CSSProperties;

  return (
    <SidebarProvider style={style}>
      <div className="flex h-screen w-full bg-[#f8fafc] dark:bg-background overflow-hidden">
        <AppSidebar />
        <div className="flex flex-col flex-1 h-full overflow-hidden relative">
          <AppHeader />
          <main className="flex-1 overflow-y-auto overflow-x-hidden p-6 lg:p-10 scroll-smooth">
            {children}
          </main>
        </div>
      </div>
    </SidebarProvider>
  );
}
