import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar"
import { CaretDoubleRightIcon, FoldersIcon } from "@phosphor-icons/react"
import { useLocation, useNavigate } from "react-router-dom"

export default function NavProjects() {
  const navigate = useNavigate()
  const location = useLocation()
  const isActive = location.pathname == "/group/projects"

  return (
    <SidebarGroup>
      <SidebarGroupLabel>Projects</SidebarGroupLabel>
      <SidebarMenuItem>
        <SidebarMenuButton asChild onClick={() => navigate("/group/projects")} isActive={isActive}>
          <div>
            <FoldersIcon />
            <span>Projects</span>
            <CaretDoubleRightIcon className="ml-auto" />
          </div>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarGroup>
  )
}
