import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import NotFound from "@/pages/not-found";

import { DashboardLayout } from "@/components/layout/dashboard-layout";
import Dashboard from "@/pages/dashboard";

function Router() {
  return (
    <Switch>
      <Route path="/" component={Dashboard}/>
      {/* Add additional feature routes here, all wrapped in layout */}
      
      {/* Fallback to 404 */}
      <Route component={NotFound} />
    </Switch>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        
        {/* We wrap the entire router in our specialized dashboard layout */}
        <Switch>
          {/* Apply Layout to root paths */}
          <Route path="*">
            <DashboardLayout>
              <Router />
            </DashboardLayout>
          </Route>
        </Switch>
        
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
