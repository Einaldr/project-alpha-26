import { Input } from "@/components/ui/input"
import { Field, FieldError, FieldGroup, FieldLabel } from "../ui/field"
import { Button } from "@/components/ui/button"
import { z } from "zod"
import { standardSchemaResolver } from "@hookform/resolvers/standard-schema"
import { Controller, useForm } from "react-hook-form"
import { userService } from "@/services/userService"
import { toast } from "sonner"
import { useUser } from "@/hooks/useUser"

const changePasswordFormSchema = z.object({
  current_password: z.string().min(5).max(255),
  password: z.string().min(5).max(255),
})

export default function ChangePasswordForm() {
  const {fetchUser} = useUser()

  const form = useForm<z.infer<typeof changePasswordFormSchema>>({
    resolver: standardSchemaResolver(changePasswordFormSchema),
    defaultValues: {
      current_password: "",
      password: ""
    },
  })

  async function onSubmit(data: z.infer<typeof changePasswordFormSchema>) {
    const loginWithDelay = async () => {
      const apiCall = userService.changePassword(data)
      const timer = new Promise((resolve) => setTimeout(resolve, 1000))

      const [response] = await Promise.all([apiCall, timer])

      return response
    }

    toast.promise(loginWithDelay, {
      loading: "Updating password...",
      success: (data) => {
        if (!data || typeof data != 'string') throw new Error("Failed to get a new token.")
        form.reset()
        form.setValues({'current_password': "", 'password': ""})
        fetchUser()
        localStorage.setItem('token', data)
        return `Password changed successfully`
      },
      error: (err) => {
        return err?.response?.data?.message || "Something went wrong"
      },
    })
  }

  return (
    <form
      className="flex w-full flex-col items-center"
      onSubmit={form.handleSubmit(onSubmit)}
    >
      <FieldGroup className="rounded-sm border border-border p-8">
        <Controller
          name="current_password"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="current_password">Current password:</FieldLabel>
              <Input
                {...field}
                aria-invalid={fieldState.invalid}
                type="password"
                id="current_password"
                autoComplete="current-password"
                required
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />

        <Controller
          name="password"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor="newpassword">New password:</FieldLabel>
              <Input
                {...field}
                aria-invalid={fieldState.invalid}
                type="password"
                id="newpassword"
                autoComplete="new-password"
                required
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />

        <Field>
          <Button size="lg" type="submit" className="w-full">
            Change Password
          </Button>
        </Field>
      </FieldGroup>
    </form>
  )
}
