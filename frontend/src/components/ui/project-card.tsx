import type { Project } from "@/types/api";
import { Card, CardDescription, CardHeader, CardTitle } from "./card";

interface projectCardProps {
    project: Project
    onClick?: () => void
}

export const ProjectCard = ({project, onClick}: projectCardProps) => {
    return (
        <Card className="w-full max-w-sm" onClick={onClick}>
            <img src={project.image_url} alt={project.name + "'s image"} className="relative z-20 aspect-video w-full object-cover" />
            <CardHeader>
                <CardTitle>{project.name}</CardTitle>
                <CardDescription>{project.description}</CardDescription>
            </CardHeader>
        </Card>
    )
}