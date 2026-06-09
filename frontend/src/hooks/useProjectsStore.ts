import type { Group, Project } from "@/types/api"
import { persist } from "zustand/middleware"
import { projectService } from "@/services/projectService"
import { create } from "zustand"

interface projectsStore {
    projects: Project[]
    activeProject: Project|null
    isLoading: boolean

    fetchProjects: (group: Group|string) => void
    changeProject: (project: Project) => void
    changeAndFetchProject: (group: Group|string, id: string) => void

    reset: () => void
}

export const useProjectsStore = create<projectsStore>()(
    persist(
        (set, get) => ({
            projects: [],
            activeProject: null,
            isLoading: false,

            fetchProjects: async (group: Group|string) => {
                if (get().isLoading) {
                    console.warn("Couldn't fetch projects: already loading.")
                    return
                }
                let groupId = null
                if (typeof group == "string") {
                    groupId = group
                } else {
                    if (group?.id) {
                        groupId = group.id
                    } else {
                        console.warn("Failed to fetch projects: couldn't extract group id.")
                        throw new Error("Failed to fetch projects: couldn't extract group id.")
                    }
                }

                try {
                    const newProjects = await projectService.fetchProjects(groupId)

                    set({projects: newProjects});
                } catch (error) {
                    throw new Error("Failed to fetch projects: " + error)
                }
            },

            changeProject: (project: Project) => {
                set({activeProject: project})
            },

            changeAndFetchProject: async (group: Group|string, id: string) => {
                let groupId = null
                if (typeof group == "string") {
                    groupId = group
                } else {
                    if (group?.id) {
                        groupId = group.id
                    } else {
                        console.warn("Failed to fetch projects: couldn't extract group id.")
                        throw new Error("Failed to fetch projects: couldn't extract group id.")
                    }
                }

                try {
                    const newProject = await projectService.fetchProject(groupId, id)

                    set({activeProject: newProject})
                } catch (error) {
                    console.error(`Failed to fetch project (${id}): ${error}`)
                    throw new Error(`Failed to fetch project (${id}): ${error}`)
                }
            },

            reset: () => {
                set({
                    projects: [],
                    activeProject: null,
                    isLoading: false
                })
            }
        }),
        {
            name: "projects-storage",
        }
    )
)