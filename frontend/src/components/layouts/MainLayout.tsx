import { Outlet } from "react-router-dom"
import AppSidebar from "../elements/sidebar"
import { SidebarProvider, SidebarTrigger } from "../ui/sidebar"
import { useEffect } from "react"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"

export default function MainLayout() {
  const { fetchWorkspace, workspace } = useActiveGroupStore()

  useEffect(() => {
    if (!workspace) {
      fetchWorkspace()
    }
  }, [fetchWorkspace, workspace])

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
