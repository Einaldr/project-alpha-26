import { groupService } from "@/services/groupService"
import type { Group } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"
import { useActiveMembership } from "./useActiveMembership"

interface ActiveGroupState {
  workspace: Group | null
  activeGroup: Group | null
  isLoading: boolean

  fetchWorkspace: () => Promise<void>
  setActiveGroup: (group: Group | null) => void
  fetchAndSetActiveGroup: (id: string) => Promise<void>

  // deconstructor
  reset: () => void
}

export const useActiveGroupStore = create<ActiveGroupState>()(
  persist(
    (set, get) => ({
      workspace: null,
      activeGroup: null,
      isLoading: false,

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
          useActiveMembership.getState().updateMembership()
        }
      },

      setActiveGroup: (group) => {
        set({ activeGroup: group })
        useActiveMembership.getState().updateMembership()
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
          useActiveMembership.getState().updateMembership()
        }
      },

      reset: () =>
        set({ activeGroup: null, workspace: null, isLoading: false }),
    }),
    {
      name: "active-group-storage",
    }
  )
)
