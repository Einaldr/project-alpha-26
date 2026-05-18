import GroupSelector from "./sidebar-elements/group-selector"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from "../ui/sidebar"
import NavMembers from "./sidebar-elements/nav-members"
import NavSettings from "./sidebar-elements/nav-settings"

export default function AppSidebar() {

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader><GroupSelector /></SidebarHeader>

      <SidebarContent>
        <NavMembers />
        <NavSettings />
      </SidebarContent>
      <SidebarFooter />

      <SidebarRail />
    </Sidebar>
  )
}
