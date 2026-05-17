import { roleService } from "@/services/roleService"
import type { Permissions } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"
import { useActiveGroupStore } from "./useActiveGroupStore"

interface activeMembership {
  roles: Permissions[] | null
  isLoading: boolean

  updateMembership: () => void
  hasPermission: (permission: Permissions) => boolean
}

// TODO: Finish this

export const useActiveMembership = create<activeMembership>()(
  persist(
    (set, get) => ({
      roles: null,
      isLoading: false,

      updateMembership: async () => {
        const activeGroup = useActiveGroupStore.getState().activeGroup;

        if (!get().isLoading) {
          return null
        }
        
        set({isLoading: true})
        
        try {
          if (activeGroup?.id) {
            const roles = await roleService.myRoles(activeGroup?.id)
            const permissions: Permissions[] = []

            roles.forEach((role) => {
              role.permissions.forEach((permission) => {
                if (!permissions.includes(permission)) {
                  permissions.push(permission)
                }
              })
            })

            set({ roles: permissions })
          } else if (activeGroup == null) {
            set({roles: null})
          }
        } catch (error) {
          console.error("Error when updating membership permissions:", error)
        } finally {
          set({isLoading: false})
        }
      },

      hasPermission: (permission: Permissions) => {
        if (!get().roles) {
          return false
        }

        if (get().roles?.includes(permission)) {
          return true
        }

        return false
      }
    }),
    {
      name: "active-permissions-storage",
    }
  )
)
