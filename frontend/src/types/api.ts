export type GroupType = 'individual' | 'org' | 'team';

export const GroupTypeEnum: GroupType[] = [ 'individual', 'org', 'team']

export type Permissions = 'group.update' |
                          'group.view' |
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

export const PermissionsSchema: Permissions[] = [
    'group.update',
    'group.delete',
    'group.view_child',
    'group.create_child',
    'member.invite',
    'member.kick',
    'members.manage_roles',
    'roles.manage',
    'project.invite',
    'project.kick',
    'project.manage',
    'repository.manage',
    'audit_log.view'
]

export type AuditActions = 'group.created' |
    'group.updated' |
    'group.deleted' |
    'child.created' |
    'member.invited' |
    'member.joined' |
    'member.left' |
    'member.kicked' |
    'member.updated' |
    'role.created' |
    'role.updated' |
    'role.deleted'

export type ProjectPermissions = 'project.read' |
                                 'project.write'|
                                 'project.manage'|
                                 'project.members.invite'|
                                 'project.members.kick'

export type GitAuthType = 'git.auth.http'

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

export interface AuditLog {
    id: string,
    actor: User | {id: null, name: "System"},
    action: AuditActions,
    payload: [],
}

export interface ProjectMember {
    member_id: string,
    user: User,
    permissions: ProjectPermissions[]
    created_at: string
}

export interface ProjectSecrets {
    auth_type: GitAuthType,
    is_configured: boolean,
    updated_at: string
}

export interface Project {
    id: string,
    name: string,
    description: string,
    image_url: string,
    git_url: string,
    default_branch: string,
    last_pulled_at: string,

    group: Group|null,
    members: ProjectMember[]|null,
    secrets: ProjectSecrets|null,
}