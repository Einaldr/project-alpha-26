import { NavLink } from "react-router-dom"
import { ProjectUpdateForm } from "../forms/ProjectUpdateForm"
import { Button } from "../ui/button"

export default function ProjectSettingsView() {
  return (
    <div className="flex h-full w-full flex-col items-center justify-center self-center">
      <ProjectUpdateForm />
      <NavLink to="/group/projects" className="w-full max-w-lg p-4">
        <Button size="lg" variant="secondary" className="w-full">
          Go Back
        </Button>
      </NavLink>
    </div>
  )
}
