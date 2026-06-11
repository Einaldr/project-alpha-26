import type {
  Group,
  Project,
  ProjectPermissions,
  ProjectSecrets,
} from "@/types/api"
import { persist } from "zustand/middleware"
import { projectService } from "@/services/projectService"
import { create } from "zustand"

interface projectsStore {
  projects: Project[]
  activeProject: Project | null
  isLoading: boolean
  branches: { current: string; branches: string[] } | null

  fetchProjects: (group: Group | string) => void
  changeProject: (project: Project) => void
  changeAndFetchProject: (group: Group | string, id: string) => void
  fetchPermissions: (
    group: Group | string,
    project: Project
  ) => Promise<ProjectPermissions[]>

  fetchBranches: (group: Group | string, project: Project) => void
  fetchSecrets: (
    group: Group | string,
    project: Project
  ) => Promise<ProjectSecrets>

  resetProjects: () => void
}

export const useProjectsStore = create<projectsStore>()(
  persist(
    (set, get) => ({
      projects: [],
      activeProject: null,
      isLoading: false,
      branches: null,

      fetchProjects: async (group: Group | string) => {
        if (get().isLoading) {
          console.warn("Couldn't fetch projects: already loading.")
          return
        }
        set({ isLoading: true })
        let groupId = null
        if (typeof group == "string") {
          groupId = group
        } else {
          if (group?.id) {
            groupId = group.id
          } else {
            set({ isLoading: false })
            console.warn("Failed to fetch projects: couldn't extract group id.")
            throw new Error(
              "Failed to fetch projects: couldn't extract group id."
            )
          }
        }

        try {
          const newProjects = await projectService.fetchProjects(groupId)

          await Promise.all(
            newProjects.map(async (project) => {
              try {
                const projectPermissions = await get().fetchPermissions(
                  groupId,
                  project
                )
                project.permissions = projectPermissions
              } catch (error) {
                console.warn(
                  `Failed to fetch project's permissions (${project.id}): ${error}`
                )
                project.permissions = [] // Default to empty array on failure to prevent undefined errors
              }

              try {
                const projectSecrets = await get().fetchSecrets(
                  groupId,
                  project
                )
                project.secrets = projectSecrets
              } catch (error) {
                console.warn(
                  `Failed to fetch project's secrets (${project.id}): ${error}`
                )
                project.secrets = null
              }
            })
          )

          // Now, this is only called AFTER all projects are fully populated!
          set({ projects: newProjects, isLoading: false })

          set({ projects: newProjects })
        } catch (error) {
          throw new Error("Failed to fetch projects: " + error)
        } finally {
          set({ isLoading: false })
        }
      },

      changeProject: (project: Project) => {
        set({ activeProject: project })
      },

      changeAndFetchProject: async (group: Group | string, id: string) => {
        if (get().isLoading) {
          console.warn("Couldn't change and fetch a project: already loading")
          return
        }

        set({ isLoading: true })
        let groupId = null
        if (typeof group == "string") {
          groupId = group
        } else {
          if (group?.id) {
            groupId = group.id
          } else {
            set({ isLoading: false })
            console.warn("Failed to fetch projects: couldn't extract group id.")
            throw new Error(
              "Failed to fetch projects: couldn't extract group id."
            )
          }
        }

        try {
          const newProject = await projectService.fetchProject(groupId, id)

          set({ activeProject: newProject })
        } catch (error) {
          console.error(`Failed to fetch project (${id}): ${error}`)
          throw new Error(`Failed to fetch project (${id}): ${error}`)
        } finally {
          set({ isLoading: false })
        }
      },

      fetchPermissions: async (group: Group | string, project: Project) => {
        let groupId = null
        if (typeof group == "string") {
          groupId = group
        } else {
          if (group?.id) {
            groupId = group.id
          } else {
            set({ isLoading: false })
            console.warn("Failed to fetch projects: couldn't extract group id.")
            throw new Error(
              "Failed to fetch projects: couldn't extract group id."
            )
          }
        }

        try {
          const newPermissions = await projectService.fetchProjectPermissions(
            groupId,
            project.id
          )

          return newPermissions
        } catch (error) {
          console.warn("Failed to fetch project's permissions: " + error)
          throw new Error(`Failed to fetch project's permissions: ${error}`)
        }
      },

      fetchBranches: async (group: Group | string, project: Project) => {
        let groupId = null
        if (typeof group == "string") {
          groupId = group
        } else {
          if (group?.id) {
            groupId = group.id
          } else {
            console.warn("Failed to fetch projects: couldn't extract group id.")
            throw new Error(
              "Failed to fetch projects: couldn't extract group id."
            )
          }
        }

        try {
          const newBranches = await projectService.fetchAvailableBranches(
            groupId,
            project.id
          )
          set({ branches: newBranches })
        } catch (error) {
          console.warn("Failed to fetch available branches: ", error)
          throw new Error("Failed to fetch available branches: " + error)
        }
      },

      fetchSecrets: async (group: Group | string, project: Project) => {
        let groupId = null
        if (typeof group == "string") {
          groupId = group
        } else {
          if (group?.id) {
            groupId = group.id
          } else {
            console.warn(
              "Failed to fetch project secrets: couldn't extract group id."
            )
            throw new Error(
              "Failed to fetch project secrets: couldn't extract group id."
            )
          }
        }

        try {
          if (project?.id) {
            const secrets = await projectService.fetchSecrets(
              groupId,
              project.id
            )

            if (!secrets) {
              throw new Error("Failed to fetch secrets.")
            }

            return secrets
          } else {
            throw new Error("Failed to fetch secrets: project.id is not set")
          }
        } catch (error) {
          throw new Error("Failed to fetch new secrets: " + error)
        }
      },

      resetProjects: () => {
        set({
          projects: [],
          activeProject: null,
          isLoading: false,
        })
      },
    }),
    {
      name: "projects-storage",
    }
  )
)
