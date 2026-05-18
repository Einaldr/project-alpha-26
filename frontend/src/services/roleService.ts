import api from "@/lib/api";
import type { Role } from "@/types/api";

export const roleService = {
    myRoles: async (id: string): Promise<Role[]> => {
        const response = await api.get<Role[]>(`/groups/${id}/permissions`);
        return response.data;
    }
}