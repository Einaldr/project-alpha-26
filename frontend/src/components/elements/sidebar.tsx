import GroupSelector from "./sidebar-elements/group-selector"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
  SidebarSeparator,
} from "../ui/sidebar"
import NavMembers from "./sidebar-elements/nav-members"
import NavSettings from "./sidebar-elements/nav-settings"
import NavProjects from "./sidebar-elements/nav-projects"
import UserManagement from "./sidebar-elements/user-management"

export default function AppSidebar() {

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader><GroupSelector /></SidebarHeader>

      <SidebarContent>
        <NavProjects />
        <NavMembers />
        <NavSettings />
      </SidebarContent>
      <SidebarFooter>
        <SidebarSeparator />
        <UserManagement />
      </SidebarFooter>

      <SidebarRail />
    </Sidebar>
  )
}
