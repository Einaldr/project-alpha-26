import GroupSelector from "./MainSidebar-elements/group-selector"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
  SidebarSeparator,
} from "../ui/sidebar"
import NavMembers from "./MainSidebar-elements/nav-members"
import NavSettings from "./MainSidebar-elements/nav-settings"
import NavProjects from "./MainSidebar-elements/nav-projects"
import UserManagement from "./MainSidebar-elements/user-management"

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
