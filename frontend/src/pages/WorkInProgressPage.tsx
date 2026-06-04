import { Card, CardDescription, CardTitle } from "@/components/ui/card"

export default function WorkInProgressPage() {
  return (
    <div className="h-full w-full flex items-center justify-center">
      <Card size="default" className="max-w-lg p-4">
        <CardTitle className="flex flex-col">
          <h1>Work in Progress</h1>
          <p className="text-muted-foreground">Code: 501</p>
        </CardTitle>
        <CardDescription>
          The site's function or feature you wanted to use has not been yet
          implemented.
        </CardDescription>
      </Card>
    </div>
  )
}
