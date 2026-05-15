import { CaretUpDownIcon } from "@phosphor-icons/react"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from "./dropdown-menu"
import { SidebarMenuButton} from "./sidebar"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import type { Group } from "@/types/api"
import { useEffect, useState } from "react"
import { groupService } from "@/services/groupService"

export default function GroupSelector() {
  const [groups, setGroups] = useState<Group[]>([])
  const [isLoading, setIsLoading] = useState(false)
  const { activeGroup } = useActiveGroupStore()

  useEffect(() => {
    async function fetchGroups() {
      try {
        if (!isLoading) {
          setIsLoading(true)
          const groups = await groupService.myGroups()
          setGroups(groups)
        }
      } catch (error) {
        console.error("Failed to fetch groups:", error)
      } finally {
        setIsLoading(false)
      }
    }

    fetchGroups()
  }, [isLoading])

  if (!activeGroup) {
    return null
  }

  const isWorkspace = activeGroup.group_type == "individual" ? true : false
  const hasParent = activeGroup?.parent ? true : false

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton size="lg">
          <div>
            <img src={activeGroup.icon_url} />
          </div>
          <div>
            <span className="truncate font-medium">{activeGroup.name}</span>

            <span className="truncate text-xs">
              {isWorkspace
                ? "Workspace"
                : hasParent
                  ? activeGroup.parent?.name
                  : "Organization"}
            </span>
          </div>
          <CaretUpDownIcon />
        </SidebarMenuButton>
      </DropdownMenuTrigger>

      <DropdownMenuContent>
        {groups.map((group) => (
          <DropdownMenuGroup>
            <DropdownMenuLabel>{group.name}</DropdownMenuLabel>
            <DropdownMenuItem>
              <img src={group.icon_url} />
              <span>{group.name}</span>
            </DropdownMenuItem>
            {!group.children
              ? null
              : group.children.map((group) => (
                  <DropdownMenuItem>
                    <img src={group.icon_url} />
                    <span>{group.name}</span>
                  </DropdownMenuItem>
                ))}
          </DropdownMenuGroup>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
