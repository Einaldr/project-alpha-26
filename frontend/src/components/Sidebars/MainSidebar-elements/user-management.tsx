import LogoutAlert from "@/components/alerts/LogoutAlert"
import { Button } from "@/components/ui/button"
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
import { useState } from "react"
import { useNavigate } from "react-router-dom"

export default function UserManagement() {
  const { user } = useUser()
  const { isMobile, open } = useSidebar()
  const navigate = useNavigate()
  const [isLogoutOpen, setIsLogoutOpen] = useState(false)

  if (!user) return null

  return (
    <>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <SidebarMenuButton className="bg-primary" size="lg">
            <span className="ml-2 text-base font-bold">
              {open ? user.name : user.name[0]}
            </span>
            {open && <CaretDoubleRightIcon className="mr-2 ml-auto" />}
          </SidebarMenuButton>
        </DropdownMenuTrigger>

        <DropdownMenuContent
          side={isMobile ? "top" : "right"}
          sideOffset={12}
          align="start"
          className="w-(--radix-dropdown-menu-trigger-width) max-w-48 min-w-48 rounded-lg"
        >
          <DropdownMenuGroup>
            <DropdownMenuItem onClick={() => navigate("/user/profile")}>
              <UserGearIcon />
              <span>Profile</span>
            </DropdownMenuItem>
            <DropdownMenuItem onClick={() => navigate("/user/settings")}>
              <GearIcon />
              <span>Settings</span>
            </DropdownMenuItem>
          </DropdownMenuGroup>
          <DropdownMenuSeparator />
          <DropdownMenuGroup>
            <DropdownMenuItem
              variant="destructive"
              onSelect={(e) => {
                e.preventDefault()
                setIsLogoutOpen(true)
              }}
            >
              <Button variant='destructive' className="w-full">
                <SignOutIcon className="mr-auto" />
                <span>Logout</span>
                <SignOutIcon className="ml-auto" />
              </Button>
            </DropdownMenuItem>
          </DropdownMenuGroup>
        </DropdownMenuContent>
      </DropdownMenu>

      <LogoutAlert open={isLogoutOpen} onOpenChange={setIsLogoutOpen} />
    </>
  )
}
