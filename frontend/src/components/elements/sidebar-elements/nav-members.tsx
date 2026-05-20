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
  useSidebar,
} from "@/components/ui/sidebar"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { useActiveMembership } from "@/hooks/useActiveMembership"
import { CaretRightIcon, UserListIcon } from "@phosphor-icons/react"
import { useState } from "react"
import { useLocation, useNavigate } from "react-router-dom"

export default function NavMembers() {
  const [isOpen, setIsOpen] = useState(false)
  const { activeGroup } = useActiveGroupStore()
  const navigate = useNavigate()
  const { hasPermission } = useActiveMembership()
  const location = useLocation()
  const { state } = useSidebar()
  const isCollapsed = state == "collapsed"

  if (activeGroup?.group_type == "individual") return null

  const handleCollapsedAction = (e: React.MouseEvent) => {
    e.preventDefault()

    navigate("/group/members")
  }

  return (
    <SidebarGroup>
      <SidebarGroupLabel className="pointer-events-none">Member Management</SidebarGroupLabel>
      <Collapsible
        defaultOpen={false}
        open={isOpen}
        onOpenChange={setIsOpen}
        className="group/collapsible"
      >
        <SidebarMenuItem>
          <CollapsibleTrigger onClick={handleCollapsedAction}>
            <SidebarMenuButton
              className="gap-2"
              onClick={() => setIsOpen(true)}
              isActive={
                location.pathname.startsWith("/group/members") ||
                location.pathname == "/group/roles"
              }
            >
              <UserListIcon className="pointer-events-none" />
              <span className="pointer-events-none">Members</span>
              <CaretRightIcon className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90 pointer-events-none" />
            </SidebarMenuButton>
          </CollapsibleTrigger>
          {!isCollapsed && (
            <CollapsibleContent>
              <SidebarMenuSub>
                <SidebarMenuSubButton
                  asChild
                  onClick={() => navigate("/group/members")}
                  isActive={location.pathname == "/group/members"}
                >
                  <span>Members</span>
                </SidebarMenuSubButton>
                {hasPermission("member.invite") ? (
                  <SidebarMenuSubButton
                    asChild
                    onClick={() => navigate("/group/members/invite")}
                    isActive={location.pathname == "/group/members/invite"}
                  >
                    <span>Invite</span>
                  </SidebarMenuSubButton>
                ) : null}
                {hasPermission("roles.manage") ? (
                  <SidebarMenuSubButton
                    asChild
                    onClick={() => navigate("/group/roles")}
                    isActive={location.pathname == "/group/roles"}
                  >
                    <span>Roles</span>
                  </SidebarMenuSubButton>
                ) : null}
              </SidebarMenuSub>
            </CollapsibleContent>
          )}
        </SidebarMenuItem>
      </Collapsible>
    </SidebarGroup>
  )
}
