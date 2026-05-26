import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller,  useForm } from "react-hook-form"
import { z } from "zod"
import {
  Field,
  FieldGroup,
  FieldLabel,
  FieldSet,
} from "../ui/field"
import { type GroupMember } from "@/types/api"
import { Checkbox } from "../ui/checkbox"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Link, useLocation, useNavigate } from "react-router-dom"
import { useState } from "react"
import { toast } from "sonner"
import { memberService } from "@/services/memberService"

const updateMemberRoleFormSchema = z.object({
  roles: z.array(z.string()).nonempty('You need to select at least one role.')
})

export const MemberUpdateForm = () => {
    const {activeGroup, groupRoles} = useActiveGroupStore();
    const navigate = useNavigate();
    const [isLoading, setIsLoading] = useState(false);
    const location = useLocation();

    if (!location.state) throw new Error("Didn't recieve role from location state.");

    const {member}: {member: GroupMember} = location.state

    const currentRoleIds: string[] = []
    
    member.roles.forEach((role) => {
        currentRoleIds.push(role.id)
    })

  const form = useForm<z.infer<typeof updateMemberRoleFormSchema>>({
    resolver: standardSchemaResolver(updateMemberRoleFormSchema),
    defaultValues: {
      roles: currentRoleIds
    },
  })

  const handleUpdate = async (data: z.infer<typeof updateMemberRoleFormSchema>) => {
    if (isLoading) throw new Error('Already creating the role...')
    if (!activeGroup?.id) throw new Error('activeGroup.id is null')
        setIsLoading(true);
    try {
        const apiCall = memberService.updateMember(activeGroup.id, member.member_id, data.roles)
        const timer = new Promise((resolve) => setTimeout(resolve, 1000))

        const [response] = await Promise.all([apiCall, timer]);

        return response
    } catch {
        throw new Error('Failed to update the member')
    } finally {
        setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof updateMemberRoleFormSchema>) {
    toast.promise(handleUpdate(data), {
        success: () => {
            navigate('/group/members')
            return "Member roles successfully updated!"
        },
        error: (err) => {
            return err || "Something went wrong"
        }
    })
  }

  if (!groupRoles) return (
    <div>
      <h2>Failed to fetch group roles</h2>
      <Link to='/group/members'>
      <Button>Go back to members tab</Button>
      </Link>
    </div>
  )

  return (
    <div className="flex h-full w-full flex-col items-center justify-center self-center">
      <Card className="w-full max-w-lg p-4">
        <h1 className="font-bold">Updating <span className="text-primary">{member.user.name}</span>'s roles:</h1>
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <FieldGroup>
            <Controller
              name="roles"
              control={form.control}
              render={({ field, fieldState }) => (
                <FieldSet data-invalid={fieldState.invalid}>
                  <FieldGroup data-slot="checkbox-group">
                    {groupRoles.map((role) => (
                      <Field key={role.id} orientation='horizontal'>
                        
                        <Checkbox
                          id={role.id}
                          name={role.name}
                          checked={field.value.includes(role.id)}
                          onCheckedChange={(checked) => {
                            const current = field.value || []
                            const next = checked
                              ? [...current, role.id]
                              : current.filter((item) => item !== role.id)
                            field.onChange(next)
                          }}
                        />
                        <FieldLabel className="capitalize">
                          {role.name}
                        </FieldLabel>
                      </Field>
                    ))}
                  </FieldGroup>
                </FieldSet>
              )}
            />
            <Separator className="w-full mb-1 mt-1"/>
            <Field>
                <Button size='lg' type='submit' className="w-full">Update</Button>
            </Field>
            <Field>
              <Link to='/group/members'>
                <Button variant="destructive" size='lg' className="w-full">Cancel</Button>
              </Link>
            </Field>
          </FieldGroup>
        </form>
      </Card>
    </div>
  )
}
