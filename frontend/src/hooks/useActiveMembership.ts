import { roleService } from "@/services/roleService"
import type { Permissions } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"
import { useActiveGroupStore } from "./useActiveGroupStore"

interface activeMembership {
  permissions: Permissions[] | null
  isLoading: boolean

  updatePermissions: () => void
  hasPermission: (permission: Permissions) => boolean
}

export const useActiveMembership = create<activeMembership>()(
  persist(
    (set, get) => ({
      permissions: null,
      isLoading: false,

      updatePermissions: async () => {
        if (get().isLoading) return

        const activeGroup = useActiveGroupStore.getState().activeGroup
        if (!activeGroup?.id) {
          set({ permissions: null })
          return
        }

        set({ isLoading: true })

        try {
          const roles = await roleService.myRoles(activeGroup.id)

          const permissionSet = new Set<Permissions>()

          roles.forEach((role) => {
            role.permissions?.forEach((p) => permissionSet.add(p))
          })

          const permissions = Array.from(permissionSet)
          set({ permissions, isLoading: false })
        } catch (error) {
          console.error("Error when updating membership permissions:", error)
        } finally {
          set({ isLoading: false })
        }
      },

      hasPermission: (permission: Permissions) => {
        const permissions = get().permissions
        if (!permissions) return false
        return permissions.includes(permission)
      },
    }),
    {
      name: "active-permissions-storage",
    }
  )
)
