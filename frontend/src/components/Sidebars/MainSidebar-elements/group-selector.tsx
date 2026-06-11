import { CaretUpDownIcon, CheckIcon, PlusIcon } from "@phosphor-icons/react"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "../../ui/dropdown-menu"
import { SidebarMenuButton, useSidebar } from "../../ui/sidebar"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import type { Group } from "@/types/api"
import { Separator } from "../../ui/separator"
import { Link, useNavigate } from "react-router-dom"

export default function GroupSelector() {
  const { activeGroup, setActiveGroup, groups } = useActiveGroupStore()
  const { isMobile } = useSidebar()
  const navigate = useNavigate()

  if (!activeGroup) {
    return null
  }

  if (!groups) {
    return null
  }

  const changeGroup = async (group: Group) => {
    await setActiveGroup(group)
    navigate("/group/projects")
  }

  const isWorkspace = activeGroup.group_type == "individual" ? true : false
  const hasParent = activeGroup?.parent ? true : false

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton size="lg">
          <div className="flex aspect-square size-8 justify-center rounded-lg">
            <img src={activeGroup.icon_url} className="rounded-lg" />
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
          <CaretUpDownIcon className="ml-auto" />
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
            <DropdownMenuItem
              className="gap-2 p-2"
              onClick={() => changeGroup(group)}
            >
              <div className="size-8">
                <img src={group.icon_url} className="rounded-sm" />
              </div>
              <span className="font-medium">{group.name}</span>
              {activeGroup.id == group.id && (
                <CheckIcon className="ml-auto" color="green" weight="bold" />
              )}
            </DropdownMenuItem>
            {!group.children
              ? null
              : group.children.map((group) => (
                  <DropdownMenuItem
                    className="gap-2 p-2"
                    onClick={() => changeGroup(group)}
                    key={group.id}
                  >
                    <div className="size-8">
                      <img src={group.icon_url} className="rounded-sm" />
                    </div>
                    <span>{group.name}</span>
                    {activeGroup.id == group.id && (
                      <CheckIcon className="ml-auto" />
                    )}
                  </DropdownMenuItem>
                ))}
            <div className="h-2" />
          </DropdownMenuGroup>
        ))}
        <DropdownMenuSeparator />
        <Link to="/group/create">
          <DropdownMenuItem
            data-active={() => location.pathname == "/group/create"}
          >
            <PlusIcon />
            <span>Create</span>
          </DropdownMenuItem>
        </Link>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
