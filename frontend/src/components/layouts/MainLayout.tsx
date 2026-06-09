import { Outlet } from "react-router-dom"
import AppSidebar from "../Sidebars/MainSidebar"
import { SidebarProvider, SidebarTrigger } from "../ui/sidebar"
import { useEffect } from "react"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useActiveMembership } from "@/hooks/useActiveMembership"
import { useUser } from "@/hooks/useUser"
import { useProjectsStore } from "@/hooks/useProjectsStore"

export default function MainLayout() {
  const { fetchWorkspace, workspace, activeGroup, groups, fetchGroups } = useActiveGroupStore()
  const { updatePermissions } = useActiveMembership()
  const {fetchProjects, resetProjects} = useProjectsStore()
  const {fetchUser, user} = useUser()

  useEffect(() => {
    if (!workspace) {
      fetchWorkspace()
    }
    if (!user) {
      fetchUser()
    }
  }, [fetchWorkspace, workspace, user, fetchUser])

  useEffect(() => {
    updatePermissions();
  }, [activeGroup?.id, updatePermissions])

  useEffect(() => {
    if (activeGroup?.id){
      resetProjects()
      fetchProjects(activeGroup.id)
    }
  }, [activeGroup?.id, resetProjects, fetchProjects])

  useEffect(()=> {
    if (!groups) {
      fetchGroups()
    }
  })

  return (
    <SidebarProvider>
      <AppSidebar />
      <main className="flex-1 flex flex-col">
        <SidebarTrigger className="absolute z-50"/>
        <Outlet />
      </main>
    </SidebarProvider>
  )
}
