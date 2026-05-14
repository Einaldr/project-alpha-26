import { Sidebar, SidebarContent, SidebarFooter, SidebarGroup, SidebarHeader, SidebarProvider, SidebarRail, SidebarTrigger } from "../ui/sidebar";

export default function sidebar() {
    return (
        <SidebarProvider>
            <Sidebar>
                <SidebarHeader />

                <SidebarContent>

                    <SidebarGroup>

                    </SidebarGroup>

                </SidebarContent>

                <SidebarFooter />

                <SidebarRail />
            </Sidebar>

            <SidebarTrigger>
                
            </SidebarTrigger>
        </SidebarProvider>
    )
}