import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Button } from "../ui/button"
import { MemberCard } from "../ui/member-card"

export default function MembersView() {
  const {members, fetchMembers} = useActiveGroupStore()

  if (!members) return (
    <div className="w-full h-full items-center">
      <h1>Couldn't fetch members</h1>
      <Button onClick={fetchMembers}>Retry</Button>
    </div>
  )

  return (
    <div className="flex h-full w-full flex-col items-center justify-center gap-2 p-2">
      {members.map((member) => (
        <MemberCard member={member} key={member.member_id} onRefresh={fetchMembers} />
      ))}
    </div>
  )
}
