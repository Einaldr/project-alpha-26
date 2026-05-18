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
          <SidebarMenuButton onClick={() => navigate('/group/projects')}>
            <div>
                <FoldersIcon />
            </div>
            <span className="font-medium">Projects</span>
            <CaretDoubleRightIcon className="ml-auto"/>
          </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarGroup>
  )
}
