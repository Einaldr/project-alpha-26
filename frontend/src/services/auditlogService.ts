import api from "@/lib/api"

export const auditlogService = {
    fetchLogs: async (groupId: string) => {
        const {data} = await api.get(`/groups/${groupId}/auditlogs`)
        return data.data
    }
}