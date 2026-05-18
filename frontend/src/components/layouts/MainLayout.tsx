import { Outlet } from "react-router-dom"
import AppSidebar from "../elements/sidebar"
import { SidebarProvider, SidebarTrigger } from "../ui/sidebar"
import { useEffect } from "react"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useActiveMembership } from "@/hooks/useActiveMembership"

export default function MainLayout() {
  const { fetchWorkspace, workspace, activeGroup } = useActiveGroupStore()
  const { updatePermissions } = useActiveMembership()

  useEffect(() => {
    if (!workspace) {
      fetchWorkspace()
    }
  }, [fetchWorkspace, workspace])

  useEffect(() => {
    updatePermissions();
  }, [activeGroup?.id, updatePermissions])

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
