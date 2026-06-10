import { NavLink } from "react-router-dom"
import { ProjectUpdateForm } from "../forms/ProjectUpdateForm"
import { Button } from "../ui/button"
import SecretsSettingForm from "../forms/ProjectSecretsForm"

export default function ProjectSettingsView() {
  return (
    <div className="flex h-full w-full flex-col gap-4 items-center justify-center self-center">
      <ProjectUpdateForm />
      <SecretsSettingForm />
      <NavLink to="/group/projects" className="w-full max-w-lg p-4">
        <Button size="lg" variant="secondary" className="w-full">
          Go Back
        </Button>
      </NavLink>
    </div>
  )
}
