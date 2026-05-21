import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { SidebarMenuButton, useSidebar } from "@/components/ui/sidebar"
import { useUser } from "@/hooks/useUser"
import {
  CaretDoubleRightIcon,
  GearIcon,
  SignOutIcon,
  UserGearIcon,
} from "@phosphor-icons/react"
import { useNavigate } from "react-router-dom"

export default function UserManagement() {
  const { user } = useUser()
  const { isMobile } = useSidebar()
  const navigate = useNavigate()

  if (!user) return null

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton className="bg-primary" size="lg">
          <span className="ml-2 text-base font-bold">{user.name}</span>
          <CaretDoubleRightIcon className="mr-2 ml-auto" />
        </SidebarMenuButton>
      </DropdownMenuTrigger>

      <DropdownMenuContent
        side={isMobile ? "top" : "right"}
        sideOffset={12}
        align="start"
        className="w-(--radix-dropdown-menu-trigger-width) min-w-24 rounded-lg"
      >
        <DropdownMenuGroup>
          <DropdownMenuItem onClick={() => navigate('/user/profile')}>
            <UserGearIcon />
            <span>Profile</span>
          </DropdownMenuItem>
          <DropdownMenuItem onClick={() => navigate('/user/settings')}>
            <GearIcon />
            <span>Settings</span>
          </DropdownMenuItem>
        </DropdownMenuGroup>
        <DropdownMenuSeparator />
        <DropdownMenuGroup>
          <DropdownMenuItem variant="destructive">
            <SignOutIcon color="red" />
            <span className="text-red-500">Logout</span>
          </DropdownMenuItem>
        </DropdownMenuGroup>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}
