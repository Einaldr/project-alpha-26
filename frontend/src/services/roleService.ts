import api from "@/lib/api";
import type { Role } from "@/types/api";

export const roleService = {
    myRoles: async (id: string): Promise<Role[]> => {
        const {data} = await api.get<{data: Role[]}>(`/group${id}/permissions`);
        return data.data;
    }
}