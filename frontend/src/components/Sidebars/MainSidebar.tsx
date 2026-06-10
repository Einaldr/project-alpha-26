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
import { useIsViewingProject } from "@/hooks/useIsViewingProject"
import { Button } from "../ui/button"
import { CaretCircleDoubleLeftIcon } from "@phosphor-icons/react"
import { Link } from "react-router-dom"
import TreeView from "./MainSidebar-elements/tree-view"

export default function AppSidebar() {
  const isViewingProject = useIsViewingProject()

  return (
    <Sidebar collapsible="icon">
      <SidebarHeader>
        {isViewingProject ? (
          <div className="flex gap-2 p-2 w-full items-center justify-center">
            <Link to="/group/projects" className="w-full">
              <Button className="w-full cursor-pointer" variant="outline">
                <CaretCircleDoubleLeftIcon weight="bold" className="mr-auto" /><span>Return to projects</span>
              </Button>
            </Link>
          </div>
        ) : (
          <GroupSelector />
        )}
        <SidebarSeparator />
      </SidebarHeader>


      <SidebarContent>
        {isViewingProject ? (
          <TreeView />
        ) : (
          <>
            <NavProjects />
            <NavMembers />
            <NavSettings />
          </>
        )}
      </SidebarContent>
      <SidebarFooter>
        <SidebarSeparator />
        <UserManagement />
      </SidebarFooter>

      <SidebarRail />
    </Sidebar>
  )
}
