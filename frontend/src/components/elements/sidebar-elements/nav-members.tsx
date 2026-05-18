import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from "@/components/ui/collapsible"
import {
  SidebarGroup,
  SidebarGroupLabel,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
} from "@/components/ui/sidebar"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useActiveMembership } from "@/hooks/useActiveMembership"
import { CaretRightIcon, UserListIcon } from "@phosphor-icons/react"
import { useState } from "react"
import { useNavigate } from "react-router-dom"

export default function NavMembers() {
  const [isOpen, setIsOpen] = useState(false)
  const { activeGroup } = useActiveGroupStore()
  const navigate = useNavigate()
  const {hasPermission} = useActiveMembership();

  if (activeGroup?.group_type == "individual") return null

  return (
    <SidebarGroup>
      <SidebarGroupLabel>Member Management</SidebarGroupLabel>
      <Collapsible
        defaultOpen={false}
        open={isOpen}
        onOpenChange={setIsOpen}
        className="group/collapsible"
      >
        <SidebarMenuItem>
          <CollapsibleTrigger asChild>
            <SidebarMenuButton
              className="gap-2"
              onClick={() => setIsOpen(true)}
            >
              <UserListIcon className="" />
              <span>Members</span>
              <CaretRightIcon className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
            </SidebarMenuButton>
          </CollapsibleTrigger>
          <CollapsibleContent>
            <SidebarMenuSub>
                <SidebarMenuSubButton asChild onClick={() => navigate('/group/members')}>
                    <span>Members</span>
                </SidebarMenuSubButton>
                {hasPermission('member.invite') ? 
                <SidebarMenuSubButton asChild onClick={()=>navigate('/group/members/invite')}>
                    <span>Invite</span>
                </SidebarMenuSubButton> 
                : null 
                }
                {hasPermission('roles.manage') ? 
                <SidebarMenuSubButton asChild onClick={() => navigate('/group/roles')}>
                    <span>Roles</span>
                </SidebarMenuSubButton>
                : null
                }
            </SidebarMenuSub>
          </CollapsibleContent>
        </SidebarMenuItem>
      </Collapsible>
    </SidebarGroup>
  )
}
