import { auditlogService } from "@/services/auditlogService"
import { groupService } from "@/services/groupService"
import { memberService } from "@/services/memberService"
import { roleService } from "@/services/roleService"
import type { AuditLog, Group, Role, GroupMember } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"

interface ActiveGroupState {
  workspace: Group | null
  activeGroup: Group | null
  isLoading: boolean
  groups: Group[] | null
  requests: number
  members: GroupMember[] | null

  groupRoles: Role[] | null

  auditlogs: AuditLog[] | null

  fetchAuditLogs: () => Promise<void>
  fetchGroups: () => Promise<void>
  fetchWorkspace: () => Promise<void>
  setActiveGroup: (group: Group | null) => void
  fetchAndSetActiveGroup: (id: string) => Promise<void>
  fetchMembers: () => Promise<void>

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
      auditlogs: null,
      members: null,

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

      fetchMembers: async () => {
        const groupId = get().activeGroup?.id
        try {
          if (!groupId) throw new Error("Failed to fetch members: groupId is null")
              const newMembers = await memberService.fetchGroupMembers(groupId)
              set({members: newMembers})
            } catch (error) {
              throw new Error("Members View: couldn't fetch members: " + error)
            }
      },

      fetchAuditLogs: async () => {
        const groupId = get().activeGroup?.id

        if (!groupId)
          throw new Error("Failed to fetch logs: groupId is not set.")
        try {
          const newLogs = await auditlogService.fetchLogs(groupId)
          set({ auditlogs: newLogs })
        } catch (error) {
          throw new Error("Failed to fetch logs: " + error)
        }
      },

      setActiveGroup: async (group) => {
        set({ activeGroup: group })
        await get().fetchRoles()
        await get().fetchAuditLogs()
        await get().fetchMembers()
      },

      fetchGroups: async () => {
        if (get().isLoading) throw new Error("Already fetching...")
        set({ isLoading: true })
        try {
          if (get().requests < 5) {
            set({ requests: get().requests + 1 })
            const groups = await groupService.myGroups()
            set({ groups: groups })
          }
        } catch (error) {
          console.error("Failed to fetch groups:", error)

          if (get().requests >= 5) {
            console.error("Reached 5 requests cap to fetch groups:", error)
            return
          }
        } finally {
          set({ isLoading: false, requests: 0 })
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
          get().fetchRoles()
          get().fetchAuditLogs()
          get().fetchMembers()
        }
      },

      reset: () =>
        set({
          activeGroup: null,
          workspace: null,
          isLoading: false,
          groups: null,
          groupRoles: null,
          requests: 0,
          auditlogs: null,
          members: null
        }),

      fetchRoles: async () => {
        const groupId = get().activeGroup?.id
        if (!groupId) throw new Error("Failed to fetch roles, groupId is null.")
        try {
          const newRoles = await roleService.fetchGroupRoles(groupId)
          set({ groupRoles: newRoles })
        } catch (error) {
          throw new Error("Failed to fetch new group roles: " + error)
        }
      },
    }),
    {
      name: "active-group-storage",
    }
  )
)
