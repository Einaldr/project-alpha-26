import GroupSelector from "./sidebar-elements/group-selector"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from "../ui/sidebar"
import NavMembers from "./sidebar-elements/nav-members"

export default function AppSidebar() {

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader><GroupSelector /></SidebarHeader>

      <SidebarContent>
        <NavMembers />
      </SidebarContent>
      <SidebarFooter />

      <SidebarRail />
    </Sidebar>
  )
}
