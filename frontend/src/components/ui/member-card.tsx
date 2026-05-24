import type { GroupMember } from "@/types/api"
import {
  Card,
  CardAction,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "./card"
import { Button } from "./button"
import { DotsThreeVerticalIcon } from "@phosphor-icons/react"
import { DropdownMenu, DropdownMenuTrigger } from "./dropdown-menu"

interface MemberCardProps {
  member: GroupMember
}

// TODO: Complete dropdownmenu for user management.
export const MemberCard = ({ member }: MemberCardProps) => {
  return (
    <Card className="w-full max-w-sm">
      <CardHeader>
        <CardTitle>{member.user.name}</CardTitle>
        <CardDescription>{member.user.email}</CardDescription>
        <CardAction>
          <DropdownMenu>
            <DropdownMenuTrigger>
              <Button variant="ghost" className="flex">
                <DotsThreeVerticalIcon weight="bold" />
              </Button>
              
            </DropdownMenuTrigger>
          </DropdownMenu>
        </CardAction>
      </CardHeader>
      <CardFooter>
        {member.roles.map((role) => (
          <div className="rounded-md border-2 p-1 text-xs text-muted-foreground">
            {role.name}
          </div>
        ))}
      </CardFooter>
    </Card>
  )
}
