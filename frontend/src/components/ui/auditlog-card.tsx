import type { AuditLog } from "@/types/api"
import {
  Card,
  CardContent,
  CardFooter,
  CardHeader,
  CardTitle,
} from "./card"

interface AuditlogCardProps {
  log: AuditLog
}

export const AuditlogCard = ({ log }: AuditlogCardProps) => {

  return (
    <Card size='sm' className="w-full max-w-lg min-w-sm">
      <CardHeader>
        <CardTitle className="capitalize">{log.action.replace('.', ' ')}</CardTitle>
      </CardHeader>
      <CardContent>
        By: {log.actor.name}
      </CardContent>
      <CardFooter />
    </Card>
  )
}
