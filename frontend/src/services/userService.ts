import api from "@/lib/api";
import type { User } from "@/types/api";

export const userService = {
    show: async (): Promise<User> => {
        const {data} = await api.get('/me')
        return data.data
    },

    logout: async (): Promise<boolean> => {
        const response = await api.post('/auth/logout')
        return response.status == 200
    }
}