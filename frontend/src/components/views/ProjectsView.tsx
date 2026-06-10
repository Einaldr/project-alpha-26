import { useProjectsStore } from "@/hooks/useProjectsStore"
import { Button } from "../ui/button"
import { ProjectCard } from "../ui/project-card"
import { Link } from "react-router-dom"
import { ErrorBoundary } from 'react-error-boundary';
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore";

export default function ProjectsView() {
  const { projects, changeProject,fetchProjects } = useProjectsStore()
  const {activeGroup} = useActiveGroupStore()

  if (!projects)
    return (
      <div className="h-full w-full items-center">
        <h1>Couldn't fetch projects</h1>
        <Button>Retry</Button>
      </div>
    )

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6 items-stretch">
      <ErrorBoundary fallbackRender={({resetErrorBoundary}) => (<><h1>Failed to load projects</h1><Button onClick={async () => {
        if (activeGroup?.id) {
          await fetchProjects(activeGroup.id)
        }
        resetErrorBoundary()
        }}>Retry</Button></>)}>
      {projects.map((project) => (
        <Link
          to={{ pathname: `/projects/${project.id}` }}
          key={project.id}
          onClick={() => changeProject(project)}
        >
          <ProjectCard project={project} />
        </Link>
      ))}

      <div className="flex h-70 w-full flex-col items-center justify-center rounded-xl border-2 border-dashed hover:bg-card hover:border-primary/50 transition cursor-pointer">
        <Link to="/projects/create">
          <Button variant="outline">Create new project</Button>
        </Link>
      </div>
      </ErrorBoundary>
    </div>
  )
}
