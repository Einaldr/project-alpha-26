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
}

export interface Group {
    id: string;
    name: string;
    group_type: GroupType;
    icon_url: string;
    permissions: Permissions[];
    parent_id?: string;
    parent?: Group;
    children?: Group[];
}