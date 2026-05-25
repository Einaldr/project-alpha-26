import { RoleCard } from "../ui/roles-card"
import type { Role } from "@/types/api"
import { useState } from "react"
import { roleService } from "@/services/roleService"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Button } from "../ui/button"
import { PlusCircleIcon } from "@phosphor-icons/react"
import { Link } from "react-router-dom"

export default function RolesView() {
  const [isLoading, setIsLoading] = useState(false)
  const [roles, setRoles] = useState<Role[]>([])
  const { activeGroup } = useActiveGroupStore()

  const load = async () => {
    if (!activeGroup?.id) return
    if (isLoading) return

    setIsLoading(true)
    try {
      const newRoles = await roleService.fetchGroupRoles(activeGroup.id)
      setRoles(newRoles)
    } catch (error) {
      console.error("Roles View: failed to load roles:", error)
    } finally {
      setIsLoading(false)
    }
  }

  if (roles.length == 0) load()

  if (roles.length == 0 && !isLoading) {
    return (
      <div>
        <h3>Failed to load roles</h3>
        <Button variant="default" onClick={load}>
          Retry
        </Button>
      </div>
    )
  }

  // TODO: implement add role
  return (
    <div className="flex h-full w-full flex-col items-center justify-center gap-2 p-2">
      {roles.map((role) => (
        <RoleCard role={role} onRefresh={load} />
      ))}
      <Link to="/group/roles/create" className="w-full max-w-lg">
        <Button className="w-full">
          <PlusCircleIcon className="mr-auto" />
          <span>Add Role</span>
          <PlusCircleIcon className="ml-auto" />
        </Button>
      </Link>
    </div>
  )
}
