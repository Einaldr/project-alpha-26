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
import { DotsThreeVerticalIcon, PencilSimpleLineIcon, UserCircleMinusIcon } from "@phosphor-icons/react"
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "./dropdown-menu"
import { memberService } from "@/services/memberService"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { toast } from "sonner"
import { Link } from "react-router-dom"

interface MemberCardProps {
  member: GroupMember
  onRefresh: () => Promise<void>
}

export const MemberCard = ({ member, onRefresh }: MemberCardProps) => {
  const {activeGroup} = useActiveGroupStore()

  if (!activeGroup?.id) return null

  const handleMemberKick = async () => {
    try {
      await memberService.kickMember(member.member_id, activeGroup.id)
    } catch (error) {
      console.error("Failed to kick a member:", error);
    }
  }

  function kick() {
    toast.promise(handleMemberKick, {
      loading: "Kicking user...",
      success: () => {
        onRefresh()
        return "Successfully kicked the user!"
      },
      error: (err) => {
        return "Failed to kick the user: " + err
      } 
    })
  }

  return (
    <Card className="w-full max-w-sm">
      <CardHeader>
        <CardTitle>{member.user.name}</CardTitle>
        <CardDescription>{member.user.email}</CardDescription>
        <CardAction>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="flex">
                <DotsThreeVerticalIcon weight="bold" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent side='right' sideOffset={22} align='center'>
              <DropdownMenuItem>
                <Link to='/group/members/manage/roles' state={{member: member}} className="flex flex-row gap-2 items-center">
                  <PencilSimpleLineIcon />
                  <span>Edit roles</span>
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem onClick={kick}>
                <UserCircleMinusIcon color='red' />
                <span className="text-destructive">Kick user</span>
              </DropdownMenuItem>
            </DropdownMenuContent>
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
