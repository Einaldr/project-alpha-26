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

const createGroupFormSchema = z.object({
  name: z
    .string()
    .min(5, "Name must be at least 5 characters long.")
    .max(255, "Name can not be more than 255 characters long."),
  icon: z.file().mime(["image/jpeg", "image/png", "image/webp"]).max(5_000_000).nullable(),
  billing_email: z.email().nullable(),
})

export type createGroupFormSchemaType = z.infer<typeof createGroupFormSchema>

export const GroupCreateForm = () => {
  const { fetchGroups, setActiveGroup } = useActiveGroupStore()
  const [isLoading, setIsLoading] = useState(false)
  const navigate = useNavigate()

  const form = useForm<z.infer<typeof createGroupFormSchema>>({
    resolver: standardSchemaResolver(createGroupFormSchema),
    defaultValues: {
      name: "",
      icon: null,
      billing_email: null,
    },
  })

  const handleCreate = async (data: z.infer<typeof createGroupFormSchema>) => {
    if (isLoading) throw new Error("Group is already being created.")
    setIsLoading(true)
    try {
      const apiCall = groupService.store(data)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    } catch {
      throw new Error("Failed to create the role")
    } finally {
      setIsLoading(false)
    }
  }

  async function onSubmit(data: z.infer<typeof createGroupFormSchema>) {
    toast.promise(handleCreate(data), {
      success: async (newGroup) => {
        fetchGroups()
        setActiveGroup(newGroup)
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
                    placeholder="Enter the group name here"
                    required
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

        <Link to="/group/projects">
                <Button variant="destructive" size="lg" className="w-full">Cancel</Button>
        </Link>
      </Card>
    </div>
  )
}
