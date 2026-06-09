import { useProjectsStore } from "@/hooks/useProjectsStore";
import { Button } from "../ui/button";
import { ProjectCard } from "../ui/project-card";
import { Link } from "react-router-dom";

export default function ProjectsView() {
    const {projects, changeProject} = useProjectsStore()

    if (!projects) return (
        <div className="w-full h-full items-center">
            <h1>Couldn't fetch projects</h1>
            <Button>Retry</Button>
        </div>
    )

    return (
        <div className="flex h-full w-full flex-row flex-wrap items-start gap-4 p-6">
            {projects.map((project) => (
                <Link to={{pathname: `/projects/${project.id}`}} key={project.id} onClick={() => changeProject(project)}>
                    <ProjectCard project={project} />
                </Link>
            ))}
        </div>
    )
}