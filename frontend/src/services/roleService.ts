import api from "@/lib/api";
import type { Role } from "@/types/api";

export const roleService = {
    myRoles: async (groupId: string): Promise<Role[]> => {
        const response = await api.get<Role[]>(`/groups/${groupId}/permissions`);
        return response.data;
    },

    fetchGroupRoles: async (groupId: string): Promise<Role[]> => {
        const {data} = await api.get(`/groups/${groupId}/roles?permissions=1`)
        return data.data
    }
}