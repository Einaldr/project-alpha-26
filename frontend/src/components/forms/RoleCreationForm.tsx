import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller,  useForm } from "react-hook-form"
import { z } from "zod"
import {
  Field,
  FieldError,
  FieldGroup,
  FieldLabel,
  FieldSet,
} from "../ui/field"
import { Input } from "../ui/input"
import { PermissionsSchema, type Permissions } from "@/types/api"
import { Checkbox } from "../ui/checkbox"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Link, useNavigate } from "react-router-dom"
import { roleService } from "@/services/roleService"
import { useState } from "react"
import { toast } from "sonner"

const createRoleFormSchema = z.object({
  name: z
    .string()
    .min(5, "Name must be at least 5 characters long.")
    .max(255, "Name can not be more than 255 characters long."),
  permissions: z
    .array(z.custom<Permissions>((val) => typeof val === "string"))
    .nonempty("You need to select at least one permission."),
})

export default function RoleCreationForm() {
    const {activeGroup} = useActiveGroupStore();
    const navigate = useNavigate();
    const [isLoading, setIsLoading] = useState(false);

  const form = useForm<z.infer<typeof createRoleFormSchema>>({
    resolver: standardSchemaResolver(createRoleFormSchema),
    defaultValues: {
      name: "",
      permissions: ["group.view"],
    },
  })

  const handleCreation = async (data: z.infer<typeof createRoleFormSchema>) => {
    if (isLoading) throw new Error('Already creating the role...')
    if (!activeGroup?.id) throw new Error('activeGroup.id is null')
        setIsLoading(true);
    try {
        const apiCall = roleService.createRole(activeGroup.id, data)
        const timer = new Promise((resolve) => setTimeout(resolve, 1000))

        const [response] = await Promise.all([apiCall, timer]);

        return response
    } catch {
        throw new Error('Failed to create the role')
    } finally {
        setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof createRoleFormSchema>) {
    toast.promise(handleCreation(data), {
        success: () => {
            navigate('/group/roles')
            return "Role successfully created!"
        },
        error: (err) => {
            return err || "Something went wrong"
        }
    })
  }

  return (
    <div className="flex h-full w-full flex-col items-center justify-center self-center">
      <Card className="w-full max-w-lg p-4">
        <form onSubmit={form.handleSubmit(onSubmit)}>
          <FieldGroup>
            <Controller
              name="name"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="creation-name">Name</FieldLabel>
                  <Input
                    {...field}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="creation-name"
                    placeholder="Name"
                    required
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="w-full mb-4 mt-4"/>
          <FieldGroup>
            <Controller
              name="permissions"
              control={form.control}
              render={({ field, fieldState }) => (
                <FieldSet data-invalid={fieldState.invalid}>
                  <FieldGroup data-slot="checkbox-group">
                    {PermissionsSchema.map((permission) => (
                      <Field key={permission} orientation='horizontal'>
                        
                        <Checkbox
                          id={permission}
                          name={permission}
                          checked={field.value.includes(permission)}
                          onCheckedChange={(checked) => {
                            const current = field.value || []
                            const next = checked
                              ? [...current, permission]
                              : current.filter((item) => item !== permission)
                            field.onChange(next)
                          }}
                        />
                        <FieldLabel className="cursor-pointer capitalize">
                          {permission.replace(".", " ").replace("_", " ")}
                        </FieldLabel>
                      </Field>
                    ))}
                  </FieldGroup>
                </FieldSet>
              )}
            />
            <Separator className="w-full mb-1 mt-1"/>
            <Field>
                <Button size='lg' type='submit' className="w-full">Create</Button>
            </Field>
            <Field>
              <Link to='/group/roles'>
                <Button variant="destructive" size='lg' className="w-full">Cancel</Button>
              </Link>
            </Field>
          </FieldGroup>
        </form>
      </Card>
    </div>
  )
}
