import { Card } from "../ui/card"
import UsernameSettingForm from "../forms/UsernameSettingForm"

export default function UserSettingsView() {
  return (
    <div className="flex h-full w-full flex-col gap-2">
      <Card className="max-w-lg">
        <UsernameSettingForm />
      </Card>
    </div>
  )
}
