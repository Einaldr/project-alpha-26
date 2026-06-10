import { useProjectsStore } from "@/hooks/useProjectsStore"
import { Button } from "../ui/button"
import { ProjectCard } from "../ui/project-card"
import { Link } from "react-router-dom"

export default function ProjectsView() {
  const { projects, changeProject } = useProjectsStore()

  if (!projects)
    return (
      <div className="h-full w-full items-center">
        <h1>Couldn't fetch projects</h1>
        <Button>Retry</Button>
      </div>
    )

  return (
    <div className="flex h-full w-full flex-row flex-wrap items-start gap-4 p-6">
      {projects.map((project) => (
        <Link
          to={{ pathname: `/projects/${project.id}` }}
          key={project.id}
          onClick={() => changeProject(project)}
        >
          <ProjectCard project={project} />
        </Link>
      ))}

      <div className="flex aspect-square max-w-64 min-w-xs flex-col items-center justify-center rounded-md border-2 border-dashed hover:bg-card">
        <Link to="/projects/create">
          <Button variant="outline">Create new project</Button>
        </Link>
      </div>
    </div>
  )
}
