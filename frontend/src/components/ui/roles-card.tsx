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
import { DotsThreeVerticalIcon } from "@phosphor-icons/react"

interface RoleCardProps {
  role: Role
  // onRefresh: () => Promise<void> | void
}

export const RoleCard = ({ role }: RoleCardProps) => {
  return (
    <Card className="w-full max-w-lg min-w-sm">
      <CardHeader>
        <CardTitle>{role.name}</CardTitle>
        <CardAction>
          <Button variant='ghost'>
            <DotsThreeVerticalIcon weight="bold" />
          </Button>
        </CardAction>
      </CardHeader>
      <CardContent className="flex flex-col gap-1">
        <span>Permissions:</span>
        <div className="grid grid-cols-3 border bg-muted p-1 rounded-sm">
        {role.permissions.map((perm) => (
          <span key={perm}>{perm}</span>
        ))}
        </div>
      </CardContent>
      <CardFooter />
    </Card>
  )
}
