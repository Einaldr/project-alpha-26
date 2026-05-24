import api from "@/lib/api";
import type { GroupMember } from "@/types/api";

export const memberService = {
    fetchGroupMembers: async (groupId: string): Promise<GroupMember[]> => {
        const request = await api.get(`/groups/${groupId}/members`)
        return request.data.data
    },

    kickMember: async (memberId: string, groupId: string): Promise<void> => {
        await api.delete(`/groups/${groupId}/members/${memberId}`)
    }
}