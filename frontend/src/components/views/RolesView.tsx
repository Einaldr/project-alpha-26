import { RoleCard } from "../ui/roles-card"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Button } from "../ui/button"
import { PlusCircleIcon } from "@phosphor-icons/react"
import { Link } from "react-router-dom"

export default function RolesView() {
  const { groupRoles, fetchRoles } = useActiveGroupStore()

  if (!groupRoles) return (
    <div className="items-center justify-center grid-rows-2">
      <h1>Failed to get group's roles</h1>
      <Button onClick={fetchRoles}>Reload</Button>
    </div>
  )

  return (
    <div className="flex h-full w-full flex-col items-center justify-center gap-2 p-2">
      {groupRoles.map((role) => (
        <RoleCard role={role} />
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
