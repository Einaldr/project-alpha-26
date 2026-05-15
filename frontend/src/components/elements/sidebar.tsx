import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarHeader,
  SidebarRail,
} from "../ui/sidebar"

export default function AppSidebar() {

  return (
    <Sidebar>
      <SidebarHeader></SidebarHeader>

      <SidebarContent>
        <SidebarGroup></SidebarGroup>
      </SidebarContent>

      <SidebarFooter />

      <SidebarRail />
    </Sidebar>
  )
}
