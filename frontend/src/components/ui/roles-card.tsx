import type { Role } from "@/types/api"
import {
  Card,
  CardAction,
  CardContent,
  CardFooter,
  CardHeader,
  CardTitle,
} from "./card"
import { Button } from "./button"
import { DotsThreeVerticalIcon, MinusCircleIcon, PencilSimpleLineIcon } from "@phosphor-icons/react"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "./dropdown-menu"
import { roleService } from "@/services/roleService"
import { toast } from "sonner"
import { Link } from "react-router-dom"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"

interface RoleCardProps {
  role: Role
}

export const RoleCard = ({ role }: RoleCardProps) => {
  const {fetchRoles} = useActiveGroupStore()

  const handleDeleteRole = async () => {
    try {
      const response = await roleService.deleteRole(role.group.id, role.id)
      if (response == 409) throw new Error("Role is use.")

    } catch (error) {
      console.error("Failed to delete the role: ", error)
      throw new Error('Failed to delete the role: ' + error)
    }
  }

  function deleteRole() {
    toast.promise(handleDeleteRole, {
      loading: "Deleteing role...",
      success: () => {
        fetchRoles()
        return "Successfully deleted the role!"
      },
      error: (err) => {
        return "Failed to delete the role: " + err
      }
    })
  }

  return (
    <Card className="w-full max-w-lg min-w-sm">
      <CardHeader>
        <CardTitle>{role.name}</CardTitle>
        <CardAction>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost">
                <DotsThreeVerticalIcon weight="bold" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent side='right' sideOffset={22} align="center">
              <DropdownMenuItem>
                <Link to='/group/roles/update' state={{role: role}} className="flex flex-row gap-2 items-center">
                  <PencilSimpleLineIcon />
                  <span>Edit</span>
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem onClick={deleteRole}>
                <MinusCircleIcon color="red" />
                <span className="text-destructive">Delete role</span>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </CardAction>
      </CardHeader>
      <CardContent className="flex flex-col gap-1">
        <span>Permissions:</span>
        <div className="grid grid-cols-3 rounded-sm border bg-muted p-1">
          {role.permissions.map((perm) => (
            <span key={perm}>{perm}</span>
          ))}
        </div>
      </CardContent>
      <CardFooter />
    </Card>
  )
}
