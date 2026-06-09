import type { Project } from "@/types/api";
import { Card, CardAction, CardDescription, CardHeader, CardTitle } from "./card";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "./dropdown-menu";
import { DotsThreeVerticalIcon, PencilSimpleLineIcon } from "@phosphor-icons/react";
import { Button } from "./button";
import { Link } from "react-router-dom";

interface projectCardProps {
    project: Project
    onClick?: () => void
}

export const ProjectCard = ({project, onClick}: projectCardProps) => {
    return (
        <Card className="w-full max-w-sm hover:drop-shadow-primary hover:drop-shadow-lg/50" onClick={onClick}>
            <img src={project.image_url} alt={project.name + "'s image"} className="relative z-20 aspect-video w-full object-cover max-w-sm" />
            <CardHeader>
                <CardTitle>{project.name}</CardTitle>
                <CardDescription>{project.description}</CardDescription>
                <CardAction>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="ghost" className="flex">
                <DotsThreeVerticalIcon weight="bold" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent side='right' sideOffset={22} align='center'>
              <DropdownMenuItem>
                <Link to={{pathname: `/projects/${project.id}/settings`}} className="flex flex-row gap-2 items-center">
                  <PencilSimpleLineIcon />
                  <span>Edit project</span>
                </Link>
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </CardAction>
            </CardHeader>
        </Card>
    )
}