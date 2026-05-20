import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar"
import { CaretDoubleRightIcon, FoldersIcon } from "@phosphor-icons/react"
import { useNavigate } from "react-router-dom"

export default function NavProjects() {
  const navigate = useNavigate()

  return (
    <SidebarGroup>
      <SidebarGroupLabel>Projects</SidebarGroupLabel>
      <SidebarMenuItem>
        <SidebarMenuButton asChild onClick={() => navigate("/group/projects")}>
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
