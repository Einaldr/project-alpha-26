import { groupService } from "@/services/groupService"
import { roleService } from "@/services/roleService"
import type { Group, Role } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"

interface ActiveGroupState {
  workspace: Group | null
  activeGroup: Group | null
  isLoading: boolean
  groups: Group[] | null
  requests: number

  groupRoles: Role[] | null

  fetchGroups: () => Promise<void>
  fetchWorkspace: () => Promise<void>
  setActiveGroup: (group: Group | null) => void
  fetchAndSetActiveGroup: (id: string) => Promise<void>

  fetchRoles: () => Promise<void>

  // deconstructor
  reset: () => void
}

export const useActiveGroupStore = create<ActiveGroupState>()(
  persist(
    (set, get) => ({
      workspace: null,
      activeGroup: null,
      groupRoles: null,
      isLoading: false,
      groups: null,
      requests: 0,

      fetchWorkspace: async () => {
        set({ isLoading: true })
        try {
          const data = await groupService.workspace()
          set({ workspace: data })

          if (!get().activeGroup) {
            set({ activeGroup: data })
          }
        } catch (error) {
          console.error("Failed to fetch workspace", error)
        } finally {
          set({ isLoading: false })
          get().fetchRoles()
        }
      },

      setActiveGroup: async (group) => {
        set({ activeGroup: group })
        await get().fetchRoles()
      },

      fetchGroups: async () => {
        if (get().isLoading) throw new Error("Already fetching...")
        set({isLoading: true})
        try {
        if (get().requests < 5) {
          set({requests: get().requests + 1})
          const groups = await groupService.myGroups()
          set({groups: groups})
        }
      } catch (error) {
        console.error("Failed to fetch groups:", error)

        if (get().requests >= 5) {
          console.error("Reached 5 requests cap to fetch groups:", error)
          return
        }
      } finally {
        set({isLoading: false, requests: 0})
      }

      },

      fetchAndSetActiveGroup: async (id) => {
        set({ isLoading: true })

        try {
          const group = await groupService.show(id)
          set({ activeGroup: group })
        } catch (error) {
          console.error("Failed to fetch group", error)
        } finally {
          set({ isLoading: false })
          get().fetchRoles();
        }
      },

      reset: () =>
        set({ activeGroup: null, workspace: null, isLoading: false }),

      fetchRoles: async () => {
        const groupId = get().activeGroup?.id
        if (!groupId) throw new Error("Failed to fetch roles, groupId is null.")
          try{ 
            const newRoles = await roleService.fetchGroupRoles(groupId)
            set({groupRoles: newRoles})
          } catch (error) {
            throw new Error("Failed to fetch new group roles: " + error)
          }
      }
    }),
    {
      name: "active-group-storage",
    }
  )
)
