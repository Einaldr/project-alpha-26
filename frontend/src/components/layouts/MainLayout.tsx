import { Outlet } from "react-router-dom"
import AppSidebar from "../elements/sidebar"
import { SidebarProvider, SidebarTrigger } from "../ui/sidebar"
import { useEffect } from "react"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useActiveMembership } from "@/hooks/useActiveMembership"
import { useUser } from "@/hooks/useUser"

export default function MainLayout() {
  const { fetchWorkspace, workspace, activeGroup } = useActiveGroupStore()
  const { updatePermissions } = useActiveMembership()
  const {fetchUser, user} = useUser()

  useEffect(() => {
    if (!workspace) {
      fetchWorkspace()
    }
    if (!user) {
      fetchUser()
      console.log(user)
    }
  }, [fetchWorkspace, workspace, user, fetchUser])

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
