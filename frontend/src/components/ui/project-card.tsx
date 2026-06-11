import type { Project } from "@/types/api"
import {
  Card,
  CardAction,
  CardDescription,
  CardHeader,
  CardTitle,
} from "./card"
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "./dropdown-menu"
import {
  DotsThreeVerticalIcon,
  PencilSimpleLineIcon,
  XCircleIcon,
} from "@phosphor-icons/react"
import { Button } from "./button"
import { Link } from "react-router-dom"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { projectService } from "@/services/projectService"
import { useProjectsStore } from "@/hooks/useProjectsStore"
import { toast } from "sonner"

interface projectCardProps {
  project: Project
  onClick?: () => void
}

export const ProjectCard = ({ project, onClick }: projectCardProps) => {
  const { fetchProjects } = useProjectsStore()
  const { activeGroup } = useActiveGroupStore()

  const handleDeletion = async () => {
    if (!activeGroup?.id)
      throw new Error("Failed to delete project: activeGroup.id is null.")

    try {
      await projectService.deleteProject(activeGroup.id, project.id)
    } catch {
      throw new Error("Failed to delete project.")
    }
  }

  function runDeletion() {
    toast.promise(handleDeletion, {
      loading: "Deleting project...",
      success: () => {
        if (activeGroup) fetchProjects(activeGroup)
        return "Successfully deleted the project!"
      },
      error: (err) => {
        return "Failed to kick the user: " + err
      },
    })
  }

  return (
    <Card
      className="w-full h-70 hover:drop-shadow-lg/50 hover:drop-shadow-primary flex flex-col justify-between overflow-hidden"
      onClick={onClick}
    >
      <img
        src={project.image_url}
        alt={project.name + "'s image"}
        className="w-full h-full object-cover" 
      />
      <CardHeader>
        <CardTitle>{project.name}</CardTitle>
        <CardDescription>{project.description}</CardDescription>
        <CardAction>
          {(project.permissions ?? []).includes('project.manage')  && (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="ghost" className="flex">
                  <DotsThreeVerticalIcon weight="bold" />
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent side="right" sideOffset={22} align="center">
                <DropdownMenuItem>
                  <Link
                    to={{ pathname: `/projects/${project.id}/settings` }}
                    className="flex flex-row items-center gap-2"
                  >
                    <PencilSimpleLineIcon />
                    <span>Edit project</span>
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem onClick={runDeletion}>
                  <XCircleIcon color="red" />
                  <span className="text-destructive">Delete project</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
          )}
        </CardAction>
      </CardHeader>
    </Card>
  )
}