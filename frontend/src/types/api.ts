export type GroupType = 'individual' | 'org' | 'team';

export type Permissions = 'group.update' |
                          'group.delete' |
                          'group.create_child' |
                          'group.view_child' |
                          'member.invite' |
                          'member.kick' |
                          'members.manage_roles' |
                          'roles.manage' |
                          'project.manage' |
                          'project.invite' |
                          'project.kick' |
                          'repository.manage' |
                          'audit_log.view';

export interface User {
    id: string;
    name: string;
    email: string;
    status: string;
    tos_version: string;
    privacy_policy_version: string;
    joined_at: string;
}

export interface Group {
    id: string;
    name: string;
    group_type: GroupType;
    icon_url: string;
    parent_id?: string;
    parent?: Group;
    children?: Group[];
}

export interface Role {
    id: string;
    name: string;
    permissions: Permissions[];
    group: Group;
}

export interface GroupMember {
    member_id: string;
    user: User;
    roles: Role[];
}