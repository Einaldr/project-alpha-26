import UsernameSettingForm from "../forms/UsernameSettingForm"

export default function UserSettingsView() {
  return (
    <div className="flex h-full w-full flex-col gap-2 items-center pt-4">
      <div className="border-card bg-card rounded-xs min-w-lg">
        <UsernameSettingForm />
      </div>
    </div>
  )
}
