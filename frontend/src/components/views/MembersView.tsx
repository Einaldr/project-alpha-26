import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { memberService } from "@/services/memberService"
import type { GroupMember } from "@/types/api"
import { useState } from "react"
import { Button } from "../ui/button"
import { MemberCard } from "../ui/member-card"

export default function MembersView() {
  const [members, setMembers] = useState<GroupMember[] | null>(null)
  const { activeGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)

  async function load() {
    if (isLoading) return null

    setIsLoading(true)

    if (!activeGroup?.id) {
      throw new Error(
        "Members View: couldn't fetch members because of activeGroup.id is not set"
      )
    }

    try {
      const newMembers = await memberService.fetchGroupMembers(activeGroup.id)
      setMembers(newMembers)
    } catch (error) {
      throw new Error("Members View: couldn't fetch members: " + error)
    } finally {
      setIsLoading(false)
    }
  }

  if (!members) {
    try {
      load()
    } catch {
      return (
          <div>
            <h3>Failed to load members</h3>
            <Button variant="default" onClick={load}>
              Retry
            </Button>
          </div>
      )
    }
  } else {
    return (
      <div className="flex h-full w-full flex-col justify-center gap-2 p-2 items-center">
          {members.map((member) => (
            <MemberCard member={member} key={member.id} />
          ))}
      </div>
    )
  }
}
