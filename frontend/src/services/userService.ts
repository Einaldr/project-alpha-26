import api from "@/lib/api";
import type { User } from "@/types/api";

export const userService = {
    show: async (): Promise<User> => {
        const request = await api.get('/me')
        return request.data
    }
}