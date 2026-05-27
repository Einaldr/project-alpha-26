import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { z } from "zod"
import {
  Field,
  FieldError,
  FieldGroup,
  FieldLabel,
} from "../ui/field"
import { Input } from "../ui/input"
import { Card } from "../ui/card"
import { Button } from "../ui/button"
import { Separator } from "../ui/separator"
import { useActiveGroupStore } from "@/hooks/useActiveGroupStore"
import { Link, useNavigate } from "react-router-dom"
import { useState } from "react"
import { toast } from "sonner"
import { groupService } from "@/services/groupService"

const updateGroupFormSchema = z.object({
  name: z
    .string()
    .min(5, "Name must be at least 5 characters long.")
    .max(255, "Name can not be more than 255 characters long.").nullable(),
  icon: z.file().mime(["image/jpeg", "image/png", "image/webp"]).max(5_000_000).nullable(),
  billing_email: z.email().nullable(),
})

export const GroupUpdateForm = () => {
  const { activeGroup, fetchGroups, setActiveGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const form = useForm<z.infer<typeof updateGroupFormSchema>>({
    resolver: standardSchemaResolver(updateGroupFormSchema),
    defaultValues: {
      name: activeGroup?.name || null,
      icon: null,
      billing_email: null,
    },
  })

  if (!activeGroup)
    return (
      <div>
        <h2>There was an issue with retrieving active group</h2>
        <Link to="/group/projects">
          <Button>Go to home</Button>
        </Link>
      </div>
    )

  const handleUpdate = async (data: z.infer<typeof updateGroupFormSchema>) => {
    if (isLoading) throw new Error("Group is already being created.")
    setIsLoading(true)
    try {
      const formData = new FormData()

      formData.append('_method', 'PATCH')

      if (data.name) formData.append('name', data.name)
      if (data.billing_email) formData.append('billing_email', data.billing_email)

      if (data.icon instanceof File) {
        formData.append('icon', data.icon)
      }

      const apiCall = groupService.update(activeGroup.id, formData)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    } catch {
      throw new Error("Failed to create the role")
    } finally {
      setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof updateGroupFormSchema>) {
    toast.promise(handleUpdate(data), {
      success: async () => {
        await fetchGroups()
        const updatedGroups = useActiveGroupStore.getState().groups
        if (activeGroup?.id && updatedGroups){
          const currentGroupId = activeGroup.id
          const updatedGroup = updatedGroups.find(group => group.id === currentGroupId)
          if (updatedGroup) {
            await setActiveGroup(updatedGroup)
          }
        } 
        navigate("/group/projects")
        return "Group successfully updated!"
      },
      error: (err) => {
        return err || "Something went wrong"
      },
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
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="text"
                    id="name"
                    placeholder={activeGroup.name}
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
          </FieldGroup>
          <Separator className="mt-4 mb-4 w-full" />
          <FieldGroup>
            <Controller
              name="billing_email"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field>
                  <FieldLabel htmlFor="fieldupdate-email">
                    Billing Email
                  </FieldLabel>
                  <Input
                    {...field}
                    value={(field.value as string) ?? ""}
                    aria-invalid={fieldState.invalid}
                    type="email"
                    id="fieldupdate-email"
                    placeholder=""
                    autoComplete="email"
                  />
                  {fieldState.invalid && (
                    <FieldError errors={[fieldState.error]} />
                  )}
                </Field>
              )}
            />
            <FieldGroup>
              <Controller
                name="icon"
                control={form.control}
                render={({
                  // eslint-disable-next-line @typescript-eslint/no-unused-vars
                  field: { value, onChange, ...fieldProps },
                  fieldState,
                }) => (
                  <Field>
                    <FieldLabel htmlFor="fieldupdate-file">
                      Upload icon
                    </FieldLabel>
                    <Input
                      {...fieldProps}
                      type="file"
                      accept="image/*"
                      onChange={(event) => {
                        const file = event.target.files?.[0]
                        onChange(file)
                      }}
                    />
                    {fieldState.invalid && (
                      <FieldError errors={[fieldState.error]} />
                    )}
                  </Field>
                )}
              />
            </FieldGroup>
            <Separator className="mt-1 mb-1 w-full" />
            <Field>
              <Button size="lg" type="submit" className="w-full">
                Update
              </Button>
            </Field>
          </FieldGroup>
        </form>
      </Card>
    </div>
  )
}
