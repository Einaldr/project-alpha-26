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
import {
  CaretDoubleRightIcon,
  CaretRightIcon,
  GearIcon,
} from "@phosphor-icons/react"
import { useState } from "react"
import { useLocation, useNavigate } from "react-router-dom"

export default function NavSettings() {
  const [isOpen, setIsOpen] = useState(false)
  const { activeGroup } = useActiveGroupStore()
  const navigate = useNavigate()
  const { hasPermission } = useActiveMembership()
  const location = useLocation();

  if (!hasPermission("group.update") || !hasPermission("audit_log.view"))
    return null

  if (!activeGroup) return null

  return (
    <SidebarGroup>
      {activeGroup.group_type == "individual" ? (
        <SidebarMenuButton asChild onClick={() => navigate('/group/settings')} isActive={location.pathname == "/group/settings"}>
          <div>
            <GearIcon />
            <span>Settings</span>
            <CaretDoubleRightIcon className="ml-auto" />
          </div>
        </SidebarMenuButton>
      ) : (
        <>
          <SidebarGroupLabel>Group Settings</SidebarGroupLabel>
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
                  isActive={location.pathname == "/group/auditlog" || location.pathname == "/group/settings"}
                >
                  <GearIcon className="" />
                  <span>Settings</span>
                  <CaretRightIcon className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                </SidebarMenuButton>
              </CollapsibleTrigger>
              <CollapsibleContent>
                <SidebarMenuSub>
                  {hasPermission("group.update") ? (
                    <SidebarMenuSubButton
                      asChild
                      onClick={() => navigate("/group/settings")}
                      isActive={location.pathname == "/group/settings"}
                    >
                      <span>Settings</span>
                    </SidebarMenuSubButton>
                  ) : null}

                  {hasPermission("audit_log.view") ? (
                    <SidebarMenuSubButton
                      asChild
                      onClick={() => navigate("/group/auditlog")}
                      isActive={location.pathname == "/group/auditlog"}
                    >
                      <span>Audit Log</span>
                    </SidebarMenuSubButton>
                  ) : null}
                </SidebarMenuSub>
              </CollapsibleContent>
            </SidebarMenuItem>
          </Collapsible>
        </>
      )}
    </SidebarGroup>
  )
}
