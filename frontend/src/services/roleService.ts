import api from "@/lib/api";
import type { Role, Permissions } from "@/types/api";

export const roleService = {
    myRoles: async (groupId: string): Promise<Role[]> => {
        const response = await api.get<Role[]>(`/groups/${groupId}/permissions`);
        return response.data;
    },

    fetchGroupRoles: async (groupId: string, order: "asc" | "desc" = "asc", search: string | null = null): Promise<Role[]> => {
        let request = null
        if (!search) {
            request = await api.get(`/groups/${groupId}/roles?permissions=1&order=${order}`)
        } else {
            request = await api.get(`/groups/${groupId}/roles?permissions=1&order=${order}&search=${search}`)
        }
        return request.data.data
    },

    createRole: async (groupId: string, data: {name: string, permissions: Permissions[]}): Promise<void> => {
        const response = await api.post(`/groups/${groupId}/roles`, data)
        return response.data.data
    },

    updateRole: async (groupId: string, roleId: string, data: {name: string, permissions: Permissions[]}): Promise<void> => {
        const response = await api.patch(`/groups/${groupId}/roles/${roleId}`, data)
        return response.data.data
    },

    deleteRole: async (groupId: string, roleId: string): Promise<void> => {
        await api.delete(`/groups/${groupId}/roles/${roleId}`)
    }
}