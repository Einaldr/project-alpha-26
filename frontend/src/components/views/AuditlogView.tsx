import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { AuditlogCard } from "../ui/auditlog-card"
import { Button } from "../ui/button"

export default function AuditlogView() {
  const { auditlogs, fetchAuditLogs } = useActiveGroupStore()

  if (!auditlogs) {
    return (
      <div>
        <h1>Failed to fetch auditlogs.</h1>
        <Button onClick={fetchAuditLogs}>Retry</Button>
      </div>
    )
  }

  return (
    <div className="flex h-full w-full flex-col items-center justify-center gap-2 p-2">
      {auditlogs.map((log) => (
        <AuditlogCard log={log} />
      ))}
    </div>
  )
}
