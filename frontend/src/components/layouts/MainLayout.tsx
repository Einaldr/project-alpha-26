import { Outlet } from "react-router-dom";
import AppSidebar from "../elements/sidebar";
import { SidebarProvider, SidebarTrigger } from "../ui/sidebar";

export default function MainLayout() {
    return (
        <SidebarProvider>
            <AppSidebar />
            <main>
                <SidebarTrigger />
                <Outlet />
            </main>
        </SidebarProvider>
    )
}