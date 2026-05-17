import { CaretUpDownIcon } from "@phosphor-icons/react"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from "../../ui/dropdown-menu"
import { SidebarMenuButton, useSidebar } from "../../ui/sidebar"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import type { Group } from "@/types/api"
import { useEffect, useState } from "react"
import { groupService } from "@/services/groupService"
import { Separator } from "../../ui/separator"

export default function GroupSelector() {
  const [groups, setGroups] = useState<Group[]>([])
  const [isLoading, setIsLoading] = useState(false)
  const [requests, setRequests] = useState(0)
  const { activeGroup, setActiveGroup } = useActiveGroupStore()
  const { isMobile } = useSidebar()

  useEffect(() => {
    async function fetchGroups() {
      try {
        if (!isLoading && requests < 5) {
          setRequests(requests + 1)
          setIsLoading(true)
          const groups = await groupService.myGroups()
          setGroups(groups)
        }
      } catch (error) {
        console.error("Failed to fetch groups:", error)

        if (requests >= 5) {
          console.error("Reached 5 requests cap to fetch groups:", error)
          return
        }
      } finally {
        setIsLoading(false)
      }
    }

    fetchGroups()
  })

  if (!activeGroup) {
    return null
  }

  const changeGroup = (group: Group): void => {
    setActiveGroup(group)
  }

  const isWorkspace = activeGroup.group_type == "individual" ? true : false
  const hasParent = activeGroup?.parent ? true : false

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton size="lg">
          <div className="flex aspect-square justify-center size-8 rounded-lg">
            <img src={activeGroup.icon_url} className="rounded-lg"/>
          </div>
          <div className="grid">
            <span className="truncate font-medium">{activeGroup.name}</span>

            <span className="truncate text-xs">
              {isWorkspace
                ? "Workspace"
                : hasParent
                  ? activeGroup.parent?.name
                  : "Organization"}
            </span>
          </div>
          <CaretUpDownIcon className="ml-auto"/>
        </SidebarMenuButton>
      </DropdownMenuTrigger>

      <DropdownMenuContent
        side={isMobile ? "bottom" : "right"}
        sideOffset={12}
        align="start"
        className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
      >
        {groups.map((group) => (
          <DropdownMenuGroup key={group.id}>
            <DropdownMenuLabel>{group.name}</DropdownMenuLabel>
            <Separator />
            <DropdownMenuItem className="gap-2 p-2" onClick={() => changeGroup(group)}>
              <div className="size-8 rounded-lg">
                <img src={group.icon_url} className="rounded-sm" />
              </div>
              <div>
                <span className="font-medium">{group.name}</span>
              </div>
            </DropdownMenuItem>
            {!group.children
              ? null
              : group.children.map((group) => (
                  <DropdownMenuItem className="gap-2 p-2" onClick={() => changeGroup(group)} key={group.id}>
                    <div className="size-8 rounded-lg">
                      <img src={group.icon_url} className="rounded-sm" />
                    </div>
                    <div>
                      <span>{group.name}</span>
                    </div>
                  </DropdownMenuItem>
                ))}
            <div className="h-2" />
          </DropdownMenuGroup>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
