import { userService } from "@/services/userService"
import type { User } from "@/types/api"
import { create } from "zustand"
import { persist } from "zustand/middleware"

interface ActiveUserState {
  user: User | null
  isLoading: boolean

  fetchUser: () => Promise<void>

  // deconstructor
  reset: () => void
}

export const useUser = create<ActiveUserState>()(
  persist(
    (set, get) => ({
      user: null,
      isLoading: false,

      fetchUser: async () => {
        if (!get().isLoading) {
          set({ isLoading: true })
          try {
            const user = await userService.show()
            set({ user: user })
          } catch (error) {
            console.error("Failed to load user:", error)
          } finally {
            set({ isLoading: false })
          }
        }
      },

      reset: () => set({ user: null, isLoading: false }),
    }),
    {
      name: "active-user-storage",
    }
  )
)
