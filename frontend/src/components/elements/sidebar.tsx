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
import NavProjects from "./sidebar-elements/nav-projects"

export default function AppSidebar() {

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader><GroupSelector /></SidebarHeader>

      <SidebarContent>
        <NavProjects />
        <NavMembers />
        <NavSettings />
      </SidebarContent>
      <SidebarFooter />

      <SidebarRail />
    </Sidebar>
  )
}
